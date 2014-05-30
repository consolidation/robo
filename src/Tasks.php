<?php
namespace Robo;

class Tasks
{
    use Output;
    use Task\Bower;
    use Task\Codeception;
    use Task\Composer;
    use Task\Concat;
    use Task\Development;
    use Task\Drush;
    use Task\Exec;
    use Task\FileSystem;
    use Task\Git;
    use Task\GitHub;
    use Task\PHPUnit;
    use Task\PackPhar;
    use Task\ParallelExec;
    use Task\PhpServer;
    use Task\SymfonyCommand;
    use Task\Watch;

    protected function stopOnFail()
    {
        Result::$stopOnFail = true;
    }
}
