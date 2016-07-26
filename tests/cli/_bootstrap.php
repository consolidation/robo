<?php
// Here you can initialize variables that will for your tests

use Robo\Robo;
use Robo\Runner;
use League\Container\Container;
use Symfony\Component\Console\Input\StringInput;

$container = new Container();
$input = new StringInput('');
Runner::configureContainer($container, $input);
Robo::setContainer($container);
