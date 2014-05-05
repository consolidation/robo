<?php
use Symfony\Component\Finder\Finder;

class Robofile extends \Robo\Tasks
{
    public function release()
    {
        $this->say("Releasing Robo");

        $this->docs();
        $this->taskGitStack()
            ->add('-A')
            ->commit("auto-update")
            ->pull()
            ->push()
            ->run();
        
        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->run();
        
        $this->pharPublish();
    }

    public function test()
    {
        return $this->taskCodecept()
            ->run();
    }

    public function added($addition)
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
        $docs = [];
        foreach (get_declared_classes() as $task) {
            if (!preg_match('~Robo\\\Task.*?Task$~', $task)) continue;
            $docs[basename((new ReflectionClass($task))->getFileName(),'.php')][] = $task;
        }

        ksort($docs);
        $taskGenerator = $this->taskGenDoc('docs/tasks.md')->filterClasses(function (\ReflectionClass $r) {
            return !$r->isAbstract() or $r->isTrait();
        })->prepend("# Tasks");

        foreach ($docs as $file => $classes) {
            $taskGenerator->docClass("Robo\\Task\\$file");
            foreach ($classes as $task) {
                $taskGenerator->docClass($task);
            }
        }

        $taskGenerator->filterMethods(function(\ReflectionMethod $m) {
            if ($m->isConstructor() or $m->isDestructor() or $m->isStatic()) return false;
            return !in_array($m->name, ['run', '', '__call', 'getCommand']) and $m->isPublic(); // methods are not documented
        })->processClassSignature(function ($c) {
            return "## {$c->getShortName()}\n";
        })->processClassDocBlock(function($c, $doc) {
            return preg_replace('~@method .*?\wTask (.*?)\)~', '* `$1)` ', $doc);
        })->processMethodSignature(function (\ReflectionMethod $m, $text) {
            return str_replace('#### *public* ', '* `', $text) . '`';
        })->processMethodDocBlock(function(\ReflectionMethod $m, $text) {
            return $text ? ' ' . strtok($text, "\n") : '';
        })->processClass(function(\ReflectionClass $refl, $text) {
            if ($refl->isTrait()) {
                return "## ".$refl->getShortName()."\n\n``` use ".$refl->getName().";```\n$text";
            } else {
                return "### ".$refl->getShortName()."\n".$text;
            }
        })->run();
    }

    public function pharBuild()
    {
        $files = Finder::create()->ignoreVCS(true)
            ->files()
            ->name('*.php')
            ->path('src')
            ->path('vendor/symfony')
            ->path('vendor/henrikbjorn')
            ->in(__DIR__);
        $packer = $this->taskPackPhar('robo.phar');
        foreach ($files as $file) {
            $packer->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
        }
        $packer->addFile('robo','robo')
            ->executable('robo')
            ->run();
    }

    public function pharPublish()
    {
        $this->pharBuild();
        rename('robo.phar', 'robo-release.phar');
        $this->taskGit()->checkout('gh-pages')->run();
        rename('robo-release.phar', 'robo.phar');
        $this->taskGit()
            ->add('robo.phar')
            ->commit('robo.phar published')
            ->push('origin','gh-pages')
            ->checkout('master')
            ->run();
    }

    public function watch()
    {
        $this->taskWatch()->monitor('composer.json', function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }

    /**
     * Test parallel execution
     */
    public function para()
    {
        $this->taskParallelExec()
            ->process('php ~/demos/robotests/parascript.php hey')
            ->process('php ~/demos/robotests/parascript.php hoy')
            ->process('php ~/demos/robotests/parascript.php gou')
            ->run();
    }
    
}
