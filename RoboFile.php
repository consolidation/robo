<?php
use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{
    public function release()
    {
        $this->yell("Releasing Robo");

        $this->docs();
        $this->taskGitStack()
            ->add('-A')
            ->commit("auto-update")
            ->pull()
            ->push()
            ->run();

        $this->publish();

        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->run();
        
        $this->pharPublish();
        $this->versionBump();
    }

    public function test($args = "")
    {
        return $this->taskCodecept()
            ->args($args)
            ->run();
    }

    public function changed($addition)
    {
        $this->taskChangelog()
            ->version(\Robo\Runner::VERSION)
            ->change($addition)
            ->run();
    }

    public function versionBump($version = null)
    {
        if (!$version) {
            $versionParts = explode('.', \Robo\Runner::VERSION);
            $versionParts[count($versionParts)-1]++;
            $version = implode('.', $versionParts);
        }
        $this->taskReplaceInFile(__DIR__.'/src/Runner.php')
            ->from("VERSION = '".\Robo\Runner::VERSION."'")
            ->to("VERSION = '".$version."'")
            ->run();
    }

    /**
     * generate docs
     */
    public function docs()
    {
        $files = Finder::create()->files()->name('*.php')->in('src/Task');
        $docs = [];
        foreach ($files as $file) {
            if ($file->getFileName() == 'loadTasks.php') {
                continue;
            }
            if ($file->getFileName() == 'loadShortucts.php') {
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
            $taskGenerator = $this->taskGenDoc("docs/tasks/$ns.md");
            $taskGenerator->filterClasses(function (\ReflectionClass $r) {
                return !($r->isAbstract() or $r->isTrait()) and $r->implementsInterface('Robo\Contract\TaskInterface');
            })->prepend("# $ns Tasks");
            sort($tasks);
            foreach ($tasks as $class) {
                $taskGenerator->docClass($class);
            }

            $taskGenerator->filterMethods(
                function (\ReflectionMethod $m) {
                    if ($m->isConstructor() or $m->isDestructor() or $m->isStatic()) {
                        return false;
                    }
                    return !in_array($m->name, ['run', '', '__call', 'getCommand', 'getPrinted']) and $m->isPublic(); // methods are not documented
                }
            )->processClassSignature(
                function ($c) {
                    return "## " . preg_replace('~Task$~', '', $c->getShortName()) . "\n";
                }
            )->processClassDocBlock(
                function (\ReflectionClass $c, $doc) {
                    $doc = preg_replace('~@method .*?(.*?)\)~', '* `$1)` ', $doc);
                    $doc = str_replace('\\'.$c->getName(), '', $doc);
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
            )->run();
        }
    }

    /**
     * Builds a site in gh-pages branch. Uses mkdocs
     */
    public function publish()
    {
        $this->stopOnFail();
        $this->taskGitStack()
            ->checkout('site')
            ->merge('master')
            ->run();
        $this->_copy('CHANGELOG.md', 'docs/changelog.md');
        $this->_exec('mkdocs gh-deploy');
        $this->taskGitStack()
            ->checkout('master')
            ->run();
        $this->_remove('docs/changelog.md');
    }

    public function pharBuild()
    {

        $packer = $this->taskPackPhar('robo.phar');
        $this->taskComposerInstall()
            ->noDev()
            ->printed(false)
            ->run();

        $files = Finder::create()->ignoreVCS(true)
            ->files()
            ->name('*.php')
            ->path('src')
            ->path('vendor')
            ->in(__DIR__);
        foreach ($files as $file) {
            $packer->addFile($file->getRelativePathname(), $file->getRealPath());
        }
        $packer->addFile('robo','robo')
            ->executable('robo')
            ->run();

        $this->taskComposerInstall()
            ->printed(false)
            ->run();
    }

    public function pharInstall()
    {
        $this->taskExec('sudo cp')
            ->arg('robo.phar')
            ->arg('/usr/bin/robo')
            ->run();
    }

    public function pharPublish()
    {
        $this->pharBuild();

        rename('robo.phar', 'robo-release.phar');
        $this->taskGitStack()->checkout('gh-pages')->run();
        rename('robo-release.phar', 'robo.phar');
        $this->taskGitStack()
            ->add('robo.phar')
            ->commit('robo.phar published')
            ->push('origin','gh-pages')
            ->checkout('master')
            ->run();
    }

    public function tryWatch()
    {
        $this->taskWatch()->monitor(['composer.json', 'composer.lock'], function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }

    public function tryInput()
    {
        $answer = $this->ask('how are you?');
        $this->say('You are '.$answer);
        $yes = $this->confirm('Do you want one more question?');
        if (!$yes) return;
        $lang = $this->askDefault('what is your favorite scripting language?', 'PHP');
        $this->say($lang);
        $pin = $this->askHidden('Ok, now tell your PIN code (it is hidden)');
        $this->yell('Ha-ha, your pin code is: '.$pin);
        $this->say('Bye!');
    }

    /**
     * Test parallel execution
     */
    public function tryPara()
    {
        $this->taskParallelExec()
            ->process('php ~/demos/robotests/parascript.php hey')
            ->process('php ~/demos/robotests/parascript.php hoy')
            ->process('php ~/demos/robotests/parascript.php gou')
            ->process('php ~/demos/robotests/parascript.php die')
            ->run();
    }

    public function tryOptbool($opts = ['silent|s' => false])
    {
        if (!$opts['silent']) $this->say("Hello, world");
    }

    public function tryServer()
    {
        $this->taskServer(8000)
            ->dir('site')
            ->arg('site/index.php')
            ->run();
    }

    public function tryInteractive()
    {
        new SomeTask();
        $this->_exec('php -r "echo php_sapi_name();"');
    }
    
}
