<?php
// Here you can initialize variables that will for your tests

use Robo\Robo;
use Robo\Runner;
use League\Container\Container;
use Symfony\Component\Console\Input\StringInput;

$container = new Container();
$config = new \Robo\Config();
$input = new StringInput('');
Robo::configureContainer($container, $config, $input);
Robo::setContainer($container);
