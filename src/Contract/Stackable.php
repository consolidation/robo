<?php

interface Stackable
{
    public static function stack();
    public static function init();
    public function stopOnFail();
} 