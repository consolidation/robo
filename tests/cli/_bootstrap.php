<?php
// Here you can initialize variables that will for your tests

use Robo\Config;
use Robo\Runner;
use Robo\Container\RoboContainer;
use Symfony\Component\Console\Input\StringInput;

$container = new RoboContainer();
$input = new StringInput('');
Runner::configureContainer($container, $input);
Config::setContainer($container);
