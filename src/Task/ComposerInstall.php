<?php
namespace Robo\Task;
use Robo\TaskInterface;

class ComposerInstall extends BaseComposer implements TaskInterface {

    public function run()
    {
        $options = $this->prefer;
        $this->dev ?: $options.= "--no-dev";
        $this->printTaskInfo('Installing Packages: '.$options);
        return system($this->command.' install '.$options);
    }
    
}
 