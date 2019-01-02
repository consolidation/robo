<?php
use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{
    /**
     * Run the Robo unit tests.
     */
    public function test(array $args, $options =
        [
            'coverage-html' => false,
            'coverage' => false
        ])
    {
        $taskCodecept = $this->taskCodecept()
            ->args($args);

        if ($options['coverage']) {
            $taskCodecept->coverageXml('../../build/logs/clover.xml');
        }
        if ($options['coverage-html']) {
            $taskCodecept->coverageHtml('../../build/logs/coverage');
        }

        return $taskCodecept->run();
     }

    /**
     * Code sniffer.
     *
     * Run the PHP Codesniffer on a file or directory.
     *
     * @param string $file
     *    A file or directory to analyze.
     * @option $autofix Whether to run the automatic fixer or not.
     * @option $strict Show warnings as well as errors.
     *    Default is to show only errors.
     */
    public function sniff(
        $file = 'src/',
        $options = [
            'autofix' => false,
            'strict' => false,
        ]
    ) {
        $strict = $options['strict'] ? '' : '-n';
        $result = $this->taskExec("./vendor/bin/phpcs --standard=PSR2 --exclude=Squiz.Classes.ValidClassName {$strict} {$file}")->run();
        if (!$result->wasSuccessful()) {
            if (!$options['autofix']) {
                $options['autofix'] = $this->confirm('Would you like to run phpcbf to fix the reported errors?');
            }
            if ($options['autofix']) {
                $result = $this->taskExec("./vendor/bin/phpcbf --standard=PSR2 --exclude=Squiz.Classes.ValidClassName {$file}")->run();
            }
        }
        return $result;
    }

    /**
     * Generate a new Robo task that wraps an existing utility class.
     *
     * @param $className The name of the existing utility class to wrap.
     * @param $wrapperClassName The name of the wrapper class to create. Optional.
     * @usage generate:task 'Symfony\Component\Filesystem\Filesystem' FilesystemStack
     */
    public function generateTask($className, $wrapperClassName = "")
    {
        return $this->taskGenTask($className, $wrapperClassName)->run();
    }

    /**
     * Release Robo.
     */
    public function release($opts = ['beta' => false])
    {
        $this->checkPharReadonly();

        $version = \Robo\Robo::VERSION;
        $stable = !$opts['beta'];
        if ($stable) {
            $version = preg_replace('/-.*/', '', $version);
        }
        else {
            $version = $this->incrementVersion($version, 'beta');
        }
        $this->writeVersion($version);
        $this->yell("Releasing Robo $version");

        $this->docs();
        $this->taskGitStack()
            ->add('-A')
            ->commit("Robo release $version")
            ->pull()
            ->push()
            ->run();

        if ($stable) {
            $this->pharPublish();
        }
        $this->publish();

        $this->taskGitStack()
            ->tag($version)
            ->push('origin 1.x --tags')
            ->run();

        if ($stable) {
            $version = $this->incrementVersion($version) . '-dev';
            $this->writeVersion($version);

            $this->taskGitStack()
                ->add('-A')
                ->commit("Prepare for $version")
                ->push()
                ->run();
        }
    }

    /**
     * Update changelog.
     *
     * Add an entry to the Robo CHANGELOG.md file.
     *
     * @param string $addition The text to add to the change log.
     */
    public function changed($addition)
    {
        $version = preg_replace('/-.*/', '', \Robo\Robo::VERSION);
        return $this->taskChangelog()
            ->version($version)
            ->change($addition)
            ->run();
    }

    /**
     * Update the version of Robo.
     *
     * @param string $version The new verison for Robo.
     *   Defaults to the next minor (bugfix) version after the current relelase.
     * @option stage The version stage: dev, alpha, beta or rc. Use empty for stable.
     */
    public function versionBump($version = '', $options = ['stage' => ''])
    {
        // If the user did not specify a version, then update the current version.
        if (empty($version)) {
            $version = $this->incrementVersion(\Robo\Robo::VERSION, $options['stage']);
        }
        return $this->writeVersion($version);
    }

    /**
     * Write the specified version string back into the Robo.php file.
     * @param string $version
     */
    protected function writeVersion($version)
    {
        // Write the result to a file.
        return $this->taskReplaceInFile(__DIR__.'/src/Robo.php')
            ->regex("#VERSION = '[^']*'#")
            ->to("VERSION = '".$version."'")
            ->run();
    }

    /**
     * Advance to the next SemVer version.
     *
     * The behavior depends on the parameter $stage.
     *   - If $stage is empty, then the patch or minor version of $version is incremented
     *   - If $stage matches the current stage in the current version, then add one
     *     to the stage (e.g. alpha3 -> alpha4)
     *   - If $stage does not match the current stage in the current version, then
     *     reset to '1' (e.g. alpha4 -> beta1)
     *
     * @param string $version A SemVer version
     * @param string $stage dev, alpha, beta, rc or an empty string for stable.
     * @return string
     */
    protected function incrementVersion($version, $stage = '')
    {
        $stable = empty($stage);
        $versionStageNumber = '0';
        preg_match('/-([a-zA-Z]*)([0-9]*)/', $version, $match);
        $match += ['', '', ''];
        $versionStage = $match[1];
        $versionStageNumber = $match[2];
        if ($versionStage != $stage) {
            $versionStageNumber = 0;
        }
        $version = preg_replace('/-.*/', '', $version);
        $versionParts = explode('.', $version);
        if ($stable) {
            $versionParts[count($versionParts)-1]++;
        }
        $version = implode('.', $versionParts);
        if (!$stable) {
            $version .= '-' . $stage;
            if ($stage != 'dev') {
                $versionStageNumber++;
                $version .= $versionStageNumber;
            }
        }
        return $version;
    }

    /**
     * Generate the Robo documentation files.
     */
    public function docs()
    {
        $collection = $this->collectionBuilder();
        $collection->progressMessage('Generate documentation from source code.');
        $files = Finder::create()->files()->name('*.php')->in('src/Task');
        $docs = [];
        foreach ($files as $file) {
            if ($file->getFileName() == 'loadTasks.php') {
                continue;
            }
            if ($file->getFileName() == 'loadShortcuts.php') {
                continue;
            }
            $ns = $file->getRelativePath();
            if (!$ns) {
                continue;
            }
            $class = basename(substr($file, 0, -4));
            class_exists($class = "Robo\\Task\\$ns\\$class");
            $docs[$ns][] = $class;
        }
        ksort($docs);

        foreach ($docs as $ns => $tasks) {
            $taskGenerator = $collection->taskGenDoc("docs/tasks/$ns.md");
            $taskGenerator->filterClasses(function (\ReflectionClass $r) {
                return !($r->isAbstract() || $r->isTrait()) && $r->implementsInterface('Robo\Contract\TaskInterface');
            })->prepend("# $ns Tasks");
            sort($tasks);
            foreach ($tasks as $class) {
                $taskGenerator->docClass($class);
            }

            $taskGenerator->filterMethods(
                function (\ReflectionMethod $m) {
                    if ($m->isConstructor() || $m->isDestructor() || $m->isStatic()) {
                        return false;
                    }
                    $undocumentedMethods =
                    [
                        '',
                        'run',
                        '__call',
                        'inflect',
                        'injectDependencies',
                        'getCommand',
                        'getPrinted',
                        'getConfig',
                        'setConfig',
                        'logger',
                        'setLogger',
                        'setProgressIndicator',
                        'progressIndicatorSteps',
                        'setBuilder',
                        'getBuilder',
                        'collectionBuilder',
                        'setVerbosityThreshold',
                        'verbosityThreshold',
                        'setOutputAdapter',
                        'outputAdapter',
                        'hasOutputAdapter',
                        'verbosityMeetsThreshold',
                        'writeMessage',
                        'detectInteractive',
                        'background',
                        'timeout',
                        'idleTimeout',
                        'env',
                        'envVars',
                        'setInput',
                        'interactive',
                        'silent',
                        'printed',
                        'printOutput',
                        'printMetadata',
                    ];
                    return !in_array($m->name, $undocumentedMethods) && $m->isPublic(); // methods are not documented
                }
            )->processClassSignature(
                function ($c) {
                    return "## " . preg_replace('~Task$~', '', $c->getShortName()) . "\n";
                }
            )->processClassDocBlock(
                function (\ReflectionClass $c, $doc) {
                    $doc = preg_replace('~@method .*?(.*?)\)~', '* `$1)` ', $doc);
                    $doc = str_replace('\\'.$c->name, '', $doc);
                    return $doc;
                }
            )->processMethodSignature(
                function (\ReflectionMethod $m, $text) {
                    return str_replace('#### *public* ', '* `', $text) . '`';
                }
            )->processMethodDocBlock(
                function (\ReflectionMethod $m, $text) {

                    return $text ? ' ' . trim(strtok($text, "\n"), "\n") : '';
                }
            );
        }
        $collection->progressMessage('Documentation generation complete.');
        return $collection->run();
    }

    /**
     * Publish Robo.
     *
     * Builds a site in gh-pages branch. Uses mkdocs
     */
    public function publish()
    {
        $current_branch = exec('git rev-parse --abbrev-ref HEAD');

        return $this->collectionBuilder()
            ->taskGitStack()
                ->checkout('site')
                ->merge('1.x')
            ->completion($this->taskGitStack()->checkout($current_branch))
            ->taskFilesystemStack()
                ->copy('CHANGELOG.md', 'docs/changelog.md')
            ->completion($this->taskFilesystemStack()->remove('docs/changelog.md'))
            ->taskExec('mkdocs gh-deploy')
            ->run();
    }

    /**
     * Build the Robo phar executable.
     */
    public function pharBuild()
    {
        $this->checkPharReadonly();

        // Create a collection builder to hold the temporary
        // directory until the pack phar task runs.
        $collection = $this->collectionBuilder();

        $workDir = $collection->tmpDir();
        $roboBuildDir = "$workDir/robo";

        // Before we run `composer install`, we will remove the dev
        // dependencies that we only use in the unit tests.  Any dev dependency
        // that is in the 'suggested' section is used by a core task;
        // we will include all of those in the phar.
        $devProjectsToRemove = $this->devDependenciesToRemoveFromPhar();

        // We need to create our work dir and run `composer install`
        // before we prepare the pack phar task, so create a separate
        // collection builder to do this step in.
        $prepTasks = $this->collectionBuilder();

        $preparationResult = $prepTasks
            ->taskFilesystemStack()
                ->mkdir($workDir)
            ->taskRsync()
                ->fromPath(
                    [
                        __DIR__ . '/composer.json',
                        __DIR__ . '/scripts',
                        __DIR__ . '/src',
                        __DIR__ . '/data'
                    ]
                )
                ->toPath($roboBuildDir)
                ->recursive()
                ->progress()
                ->stats()
            ->taskComposerRemove()
                ->dir($roboBuildDir)
                ->dev()
                ->noUpdate()
                ->args($devProjectsToRemove)
            ->taskComposerInstall()
                ->dir($roboBuildDir)
                ->noScripts()
                ->printed(true)
                ->run();

        // Exit if the preparation step failed
        if (!$preparationResult->wasSuccessful()) {
            return $preparationResult;
        }

        // Decide which files we're going to pack
        $files = Finder::create()->ignoreVCS(true)
            ->files()
            ->name('*.php')
            ->name('*.exe') // for 1symfony/console/Resources/bin/hiddeninput.exe
            ->name('GeneratedWrapper.tmpl')
            ->path('src')
            ->path('vendor')
            ->notPath('docs')
            ->notPath('/vendor\/.*\/[Tt]est/')
            ->in(is_dir($roboBuildDir) ? $roboBuildDir : __DIR__);

        // Build the phar
        return $collection
            ->taskPackPhar('robo.phar')
                ->addFiles($files)
                ->addFile('robo', 'robo')
                ->executable('robo')
            ->taskFilesystemStack()
                ->chmod('robo.phar', 0777)
            ->run();
    }

    protected function checkPharReadonly()
    {
        if (ini_get('phar.readonly')) {
            throw new \Exception('Must set "phar.readonly = Off" in php.ini to build phars.');
        }
    }

    /**
     * The phar:build command removes the project requirements from the
     * 'require-dev' section that are not in the 'suggest' section.
     *
     * @return array
     */
    protected function devDependenciesToRemoveFromPhar()
    {
        $composerInfo = (array) json_decode(file_get_contents(__DIR__ . '/composer.json'));

        $devDependencies = array_keys((array)$composerInfo['require-dev']);
        $suggestedProjects = array_keys((array)$composerInfo['suggest']);

        return array_diff($devDependencies, $suggestedProjects);
    }

    /**
     * Install Robo phar.
     *
     * Installs the Robo phar executable in /usr/bin. Uses 'sudo'.
     */
    public function pharInstall()
    {
        return $this->taskExec('sudo cp')
            ->arg('robo.phar')
            ->arg('/usr/bin/robo')
            ->run();
    }

    /**
     * Publish Robo phar.
     *
     * Commits the phar executable to Robo's GitHub pages site.
     */
    public function pharPublish()
    {
        $this->pharBuild();

        $this->collectionBuilder()
            ->taskFilesystemStack()
                ->rename('robo.phar', 'robo-release.phar')
            ->taskGitStack()
                ->checkout('site')
                ->pull('origin site')
            ->taskFilesystemStack()
                ->remove('robotheme/robo.phar')
                ->rename('robo-release.phar', 'robotheme/robo.phar')
            ->taskGitStack()
                ->add('robotheme/robo.phar')
                ->commit('Update robo.phar to ' . \Robo\Robo::VERSION)
                ->push('origin site')
                ->checkout('1.x')
                ->run();
    }
}
