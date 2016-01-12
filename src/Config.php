<?php 
namespace Robo;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Config
{
    protected static $config = [
        'output' => null,
        'input' => null
    ];

    public static function setOutput(OutputInterface $output)
    {
        self::$config['output'] = $output;
    }

    public static function setInput(InputInterface $input)
    {
        self::$config['input'] = $input;
    }

    public static function get($key, $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}