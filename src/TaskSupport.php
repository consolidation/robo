<?php
namespace Robo;

// Always `use \Robo\TaskSupport` if bringing in any other Robo
// tasks via its loadTasks trait.
trait TaskSupport
{
    protected $taskAssembler;

    public function setTaskAssembler($taskAssembler)
    {
        $this->taskAssembler = $taskAssembler;
    }

    public function taskAssembler()
    {
        return $this->taskAssembler;
    }
}
