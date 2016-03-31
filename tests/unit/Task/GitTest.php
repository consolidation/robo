<?php

use AspectMock\Test as test;
use Robo\Config;

class GitTest extends \Codeception\TestCase\Test
{

    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $git;

    protected function _before()
    {
        $this->git = test::double('Robo\Task\Vcs\GitStack', [
            'run' => new \AspectMock\Proxy\Anything(),
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->container = Config::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
    }

    // tests
    public function testGitStackRun()
    {
        $this->container->get('taskGitStack', ['git'])->add('-A')->pull()->run();
        $this->git->verifyInvoked('run', []);
    }

    public function testGitStackCommands()
    {
        verify(
            $this->renderCommand(
                $this->container->get('taskGitStack')
                    ->cloneRepo('http://github.com/Codegyre/Robo')
                    ->pull()
                    ->add('-A')
                    ->commit('changed')
                    ->push()
                    ->tag('0.6.0')
                    ->push('origin', '0.6.0')
                    ->getCommandStack()
            )
        )->equals("cloneRepository http://github.com/Codegyre/Robo []
pull
_add -A
_commit changed
push
_tag 0.6.0
push origin 0.6.0");
    }

    public function testGitStackCommandsWithTagMessage()
    {
        verify(
            $this->renderCommand(
                $this->container->get('taskGitStack')
                    ->cloneRepo('http://github.com/Codegyre/Robo')
                    ->pull()
                    ->add('-A')
                    ->commit('changed')
                    ->push()
                    ->tag('0.6.0', 'message')
                    ->push('origin', '0.6.0')
                    ->getCommandStack()
            )
        )->equals("cloneRepository http://github.com/Codegyre/Robo []
pull
_add -A
_commit changed
push
_tag 0.6.0 message
push origin 0.6.0");
    }

    /**
     * Get accumulated command stack for reporting / debugging purposes.
     */
    protected function renderCommand($commandStack)
    {
        return implode("\n",
            array_map(
                function ($item) {
                    // The first item is a callable array (object, method);
                    // ignore the object, and assign the method name to the
                    // first element.
                    $item[0] = array_pop($item[0]);
                    // The last item might be an array of options.
                    $last = array_pop($item);
                    $item[] = $this->renderOptions($last);
                    return implode(" ", $item);
                },
                $commandStack
            )
        );
    }

    protected function renderOptions($options)
    {
        if (!is_array($options)) {
            return $options;
        }
        $optionList = [];
        foreach ($options as $key => $value) {
            $optionList[] = "$key=$value";
        }
        return '[' . implode(', ', $optionList) . ']';
    }
}
