<?php
class Robofile
{
    use Robo\Output;
    use Robo\Task\GitHub;
    use Robo\Task\Development;
    use Robo\Task\Watch;
    use Robo\Task\Git;
    use Robo\Task\FileSystem;
    use Robo\Task\Composer;
    use Robo\Task\PhpServer;
    use Robo\Task\SymfonyCommand;
    use Robo\Task\Exec;
    use Robo\Task\PackPhar;

    public function release()
    {
        $this->say("Releasing Robo");

        $changelog = $this->taskChangelog()
            ->version(\Robo\Runner::VERSION)
            ->askForChanges()
            ->run();

        if (!$changelog->wasSuccessful()) exit(1);

        $this->taskGit()
            ->add('CHANGELOG.md')
            ->commit('updated changelog')
            ->push()
            ->run();

        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->changes($changelog->getData())
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

    // publish docs
    public function docs()
    {
        $docs = [];
        foreach (get_declared_classes() as $task) {
            if (!preg_match('~Robo\\\Task.*?Task$~', $task)) continue;
            $docs[basename((new ReflectionClass($task))->getFileName(),'.php')][] = $task;
        }

        ksort($docs);
        $taskGenerator = $this->taskGenDoc('docs/tasks.md')->filterClasses(function (\ReflectionClass $r) {
            return !$r->isAbstract();
        })->prepend("# Tasks");

        foreach ($docs as $file => $classes) {
            $taskGenerator->docClass("Robo\\Task\\$file");
            foreach ($classes as $task) {
                $taskGenerator->docClass($task);
            }
        }

        $taskGenerator->filterMethods(function(\ReflectionMethod $m) {
            if ($m->isConstructor() or $m->isDestructor()) return false;
            return $m->name != 'run' and $m->name != '__call' and $m->isPublic(); // methods are not documented
        })->processMethod(function (\ReflectionMethod $m, $text) {
            return "* " . $m->name . '('.implode(', ', $m->getParameters()).")\n";
        })->processClass(function(\ReflectionClass $refl, $text) {
            $text = str_replace("@method ".$refl->getShortName(),'*',$text);
            if ($refl->isTrait()) {
                return "## ".$refl->getName()."\n$text";
            } else {
                return "### ".$refl->getShortName()."\n".$text;
            }
        })->run();
    }

    public function watch()
    {
        $this->taskWatch()->monitor('composer.json', function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }
    
}