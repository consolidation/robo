<?php
namespace RoboExample\Robo\Plugin\Commands;

use Consolidation\AnnotatedCommand\Input\StdinAwareInterface;
use Consolidation\AnnotatedCommand\Input\StdinAwareTrait;
use Symfony\Component\Console\Input\InputInterface;

class StdinCommands implements StdinAwareInterface
{
    use StdinAwareTrait;

    /**
     * @command cat
     * @param string $file
     * @default $file -
     */
    public function cat(InputInterface $input)
    {
        return $this->stdin()->select($input, 'file')->contents();
    }
}
