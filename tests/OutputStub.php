<?php

namespace Robo\Tests;

use Robo\Output;

class OutputStub
{
    use Output {
        Output::say as _say;
        Output::ask as _ask;
    }

    public function say($text)
    {
        $this->_say($text);
    }

    public function ask($question)
    {
        $this->_ask($question);
    }
}
