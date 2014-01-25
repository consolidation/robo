<?php
class Robofile extends \Robo\Tasks
{
    public function release()
    {
        $this->say("Releasing Robo");
        $this->taskExec("git tag")->args(\Robo\Runner::VERSION)->run();
        $this->taskExec("git push origin master --tags")->run();
    }
}