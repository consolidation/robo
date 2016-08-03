<?php
namespace Robo;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class Application extends SymfonyApplication implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct($name, $version)
    {
        parent::__construct($name, $version);

        $this->getDefinition()
            ->addOption(
                new InputOption('--simulate', null, InputOption::VALUE_NONE, 'Run in simulated mode (show what would have happened).')
            );
        $this->getDefinition()
            ->addOption(
                new InputOption('--progress-delay', null, InputOption::VALUE_REQUIRED, 'Number of seconds before progress bar is displayed in long-running task collections. Default: 2s.')
            );
        $this->getDefinition()
            ->addOption(
                new InputOption('--supress-messages', null, InputOption::VALUE_NONE, 'Supress all Robo TaskIO messages.')
            );
    }

    public function addInitRoboFileCommand($roboFile, $roboClass)
    {
        $createRoboFile = new Command('init');
        $createRoboFile->setDescription("Intitalizes basic RoboFile in current dir");
        $createRoboFile->setCode(function () use ($roboClass, $roboFile) {
            $output = Robo::output();
            $output->writeln("<comment>  ~~~ Welcome to Robo! ~~~~ </comment>");
            $output->writeln("<comment>  ". $roboFile ." will be created in current dir </comment>");
            file_put_contents(
                $roboFile,
                '<?php'
                . "\n/**"
                . "\n * This is project's console commands configuration for Robo task runner."
                . "\n *"
                . "\n * @see http://robo.li/"
                . "\n */"
                . "\nclass " . $roboClass . " extends \\Robo\\Tasks\n{\n    // define public methods as commands\n}"
            );
            $output->writeln("<comment>  Edit RoboFile.php to add your commands! </comment>");
        });
        $this->add($createRoboFile);
    }
}
