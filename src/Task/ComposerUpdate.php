<?php
namespace Robo\Task;

use Robo\TaskInterface;

class ComposerUpdate extends BaseComposer implements TaskInterface {

    public function run()
    {
        $options = $this->prefer;
        $this->dev ?: $options.= "--no-dev";
        $this->printTaskInfo('Updating Packages: '.$options);
        return system($this->command.' update '.$options);
    }

} 