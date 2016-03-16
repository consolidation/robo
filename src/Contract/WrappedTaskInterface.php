<?php
namespace Robo\Contract;

interface WrappedTaskInterface
{
    /**
     * @return \Robo\Contract\TaskInterface
     */
    public function original();
}
