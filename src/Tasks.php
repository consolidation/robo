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
	use Task\SymfonyCommand;
	use Task\Watch;
	use Output;
}
