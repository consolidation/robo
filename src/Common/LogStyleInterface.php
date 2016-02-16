<?php
namespace Robo\Common;

/**
 * Allow a log message to by styled.
 */
interface LogStyleInterface
{
    public function defaultStyles();
    public function style($context);

    public function text($message, $context);
    public function success($message, $context);
    public function error($message, $context);
    public function warning($message, $context);
    public function note($message, $context);
    public function caution($message, $context);
}
