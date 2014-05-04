<?php
namespace Robo;

class Tasks
{
	use Task\Composer;
	use Task\Development;
	use Task\Exec;
	use Task\FileSystem;
	use Task\Git;
	use Task\GitHub;
	use Task\PackPhar;
	use Task\PhpServer;
	use Task\PHPUnit;
    use Task\Codeception;
	use Task\SymfonyCommand;
	use Task\Watch;
    use Task\ParallelExec;
    use Task\Concat;
    use Task\Bower;
	use Output;

    protected function stopOnFail()
    {
        Result::$stopOnFail = true;
    }
}
