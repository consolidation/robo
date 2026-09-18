<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Robo\Traits\Common\CommandArgumentsHost;

class CommandArgumentsTest extends TestCase
{
    public static function casesArgs() {
        return [
            'no arguments' => [
                ' ',
                ' ',
                [],
            ],
            'empty string' => [
                " ''",
                ' ""',
                [''],
            ],
            'space' => [
                " ' '",
                ' " "',
                [' '],
            ],
            'no escape - a' => [
                " a",
                " a",
                ['a'],
            ],
            'no escape - A' => [
                " A",
                " A",
                ['A'],
            ],
            'no escape - 0' => [
                " 0",
                " 0",
                ['0'],
            ],
            'no escape - --' => [
                " --",
                " --",
                ['--'],
            ],
            'no escape - @_~.' => [
                " @_~.",
                " @_~.",
                ['@_~.'],
            ],
            '$' => [
                " 'a\$b'",
                ' "a$b"',
                ['a$b'],
            ],
            '*' => [
                " 'a*b'",
                ' "a*b"',
                ['a*b'],
            ],
            'multi' => [
                " '' a '\$PATH'",
                ' "" a "$PATH"',
                ['', 'a', '$PATH'],
            ],
        ];
    }

    #[DataProvider('casesArgs')]
    public function testArgs($expectedLinux, $expectedWindows, $args)
    {
        $expected = stripos(PHP_OS, 'WIN') === 0 ? $expectedWindows : $expectedLinux;
        $commandArguments = new CommandArgumentsHost();
        $commandArguments->args($args);
        $this->assertEquals($expected, $commandArguments->getArguments());

        if ($args) {
            $commandArguments = new CommandArgumentsHost();
            call_user_func_array([$commandArguments, 'args'], $args);
            $this->assertEquals($expected, $commandArguments->getArguments());
        }
    }
}
