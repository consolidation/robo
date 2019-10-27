<?php

/**
 * Marker interface for tasks that use the IO trait
 */

namespace Robo\Contract;

use \Symfony\Component\Console\Input\InputAwareInterface;

interface IOAwareInterface extends OutputAwareInterface, InputAwareInterface
{
}
