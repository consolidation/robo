<?php
namespace Robo\Contract;

interface CommandInterface {

    /**
     * Returns command that can be executed.
     * This method is used to pass generated command from one task to another.
     *
     * @return string
     */
    public function getCommand();

}