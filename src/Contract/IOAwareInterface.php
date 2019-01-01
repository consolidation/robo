<?php

/**
 * Marker interface for tasks that use the IO trait
 */

namespace Robo\Contract;

use Robo\Symfony\IOStorage;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface IOAwareInterface extends OutputAwareInterface, InputAwareInterface
{
    public function setIOStorage(IOStorage $ioStorage);
    public function resetIO(InputInterface $input, OutputInterface $output);
}
