<?php
use Robo\Result;
use Robo\ResultData;
use Robo\Collection\CollectionBuilder;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

/**
 * Example RoboFile.
 *
 * To test:
 *
 * $ cd examples
 * $ ../robo try:success
 */
class RoboFile extends \Robo\Tasks
{
   /**
     * Watch a file.
     *
     * Demonstrates the 'watch' command. Runs 'composer update' any time
     * composer.json changes.
     */
    public function tryWatch()
    {
        $this->taskWatch()->monitor(['composer.json', 'composer.lock'], function () {
            $this->taskComposerUpdate()->run();
        })->run();
    }

    /**
     * Demonstrates Robo input APIs.
     */
    public function tryInput()
    {
        $answer = $this->ask('how are you?');
        $this->say('You are '.$answer);
        $yes = $this->confirm('Do you want one more question?');
        if (!$yes) {
            return Result::cancelled();
        }
        $lang = $this->askDefault('what is your favorite scripting language?', 'PHP');
        $this->say($lang);
        $pin = $this->askHidden('Ok, now tell your PIN code (it is hidden)');
        $this->yell('Ha-ha, your pin code is: '.$pin);
        $this->say('Bye!');
    }

    /**
     * Demonstrates parallel execution.
     *
     * @option $printed Print the output of each process.
     * @option $error Include an extra process that fails.
     */
    public function tryPara($options = ['printed' => false, 'error' => false])
    {
        $dir = __DIR__;
        $para = $this->taskParallelExec()
            ->printed($options['printed'])
            ->process("php $dir/tests/_data/parascript.php hey 4")
            ->process("php $dir/tests/_data/parascript.php hoy 3")
            ->process("php $dir/tests/_data/parascript.php gou 2")
            ->process("php $dir/tests/_data/parascript.php die 1");
        if ($options['error']) {
            $para->process("ls $dir/tests/_data/filenotfound");
        }
        return $para->run();
    }

    /**
     * Demonstrates Robo argument passing.
     *
     * @param string $a The first parameter. Required.
     * @param string $b The second parameter. Optional.
     */
    public function tryArgs($a, $b = 'default')
    {
        $this->say("The parameter a is $a and b is $b");
    }

    /**
     * Demonstrate Robo variable argument passing.
     *
     * @param $a A list of commandline parameters.
     */
    public function tryArrayArgs(array $a)
    {
        $this->say("The parameters passed are:\n" . var_export($a, true));
    }

    /**
     * Demonstrate Robo boolean options.
     *
     * @param $opts The options.
     * @option boolean $silent Supress output.
     */
    public function tryOptbool($opts = ['silent|s' => false])
    {
        if (!$opts['silent']) {
            $this->say("Hello, world");
        }
    }

    /**
     * Demonstrate the use of the PHP built-in webserver.
     */
    public function tryServer()
    {
        return $this->taskServer(8000)
            ->dir('site')
            ->arg('site/index.php')
            ->run();
    }

    /**
     * Demonstrate the use of the Robo open-browser task.
     */
    public function tryOpenBrowser()
    {
        return $this->taskOpenBrowser([
            'http://robo.li',
            'https://github.com/consolidation-org/Robo'
            ])->run();
    }

    /**
     * Demonstrate Robo error output and command failure.
     */
    public function tryError()
    {
        return $this->taskExec('ls xyzzy' . date('U'))->dir('/tmp')->run();
    }

    /**
     * Demonstrate Robo standard output and command success.
     */
    public function trySuccess()
    {
        return $this->_exec('pwd');
    }

    /**
     * Demonstrate Robo formatters.  Default format is 'table'.
     *
     * @field-labels
     *   first: I
     *   second: II
     *   third: III
     * @default-string-field second
     * @usage try:formatters --format=yaml
     * @usage try:formatters --format=csv
     * @usage try:formatters --fields=first,third
     * @usage try:formatters --fields=III,II
     */
    public function tryFormatters($options = ['format' => 'table', 'fields' => ''])
    {
        $outputData = [
            [ 'first' => 'One',  'second' => 'Two',  'third' => 'Three' ],
            [ 'first' => 'Eins', 'second' => 'Zwei', 'third' => 'Drei'  ],
            [ 'first' => 'Ichi', 'second' => 'Ni',   'third' => 'San'   ],
            [ 'first' => 'Uno',  'second' => 'Dos',  'third' => 'Tres'  ],
        ];
        return new RowsOfFields($outputData);
    }

    /**
     * Demonstrate what happens when a command or a task
     * throws an exception.  Note that typically, Robo commands
     * should return Result objects rather than throw exceptions.
     */
    public function tryException($options = ['task' => false])
    {
        if (!$options['task']) {
            throw new RuntimeException('Command failed with an exception.');
        }
        return new ExceptionTask('Task failed with an exception.');
    }

    /**
     * Demonstrate deprecated task behavior.
     *
     * Demonstrate what happens when using a task that is created via
     * direct instantiation, which omits initialization done by the
     * container.  Emits a warning message.
     */
    public function tryDeprecated()
    {
        // Calling 'new' directly without manually setting
        // up dependencies will result in a deprecation warning.
        // @see RoboFile::trySuccess()
        return (new \Robo\Task\Base\Exec('pwd'))->run();
    }

    /**
     * Demonstrate the use of a collection builder to chain multiple tasks
     * together into a collection, which is executed once constructed.
     *
     * For demonstration purposes only; this could, of course, be done
     * with a single FilesystemStack.
     */
    public function tryBuilder()
    {
        return $this->collectionBuilder()
            ->taskFilesystemStack()
                ->mkdir('a')
                ->touch('a/a.txt')
            ->taskFilesystemStack()
                ->mkdir('a/b')
                ->touch('a/b/b.txt')
            ->taskFilesystemStack()
                ->mkdir('a/b/c')
                ->touch('a/b/c/c.txt')
            ->run();
    }

    public function tryBuilderRollback()
    {
        // This example will create two builders, and add
        // the first one as a child of the second in order
        // to demonstrate nested rollbacks.
        $collection = $this->collectionBuilder()
            ->taskFilesystemStack()
                ->mkdir('g')
                ->touch('g/g.txt')
            ->rollback(
                $this->taskDeleteDir('g')
            )
            ->taskFilesystemStack()
                ->mkdir('g/h')
                ->touch('g/h/h.txt')
            ->taskFilesystemStack()
                ->mkdir('g/h/i/c')
                ->touch('g/h/i/i.txt');

        return $this->collectionBuilder()
            ->progressMessage('Start recursive collection')
            ->addTask($collection)
            ->progressMessage('Done with recursive collection')
            ->taskExec('ls xyzzy' . date('U'))
                ->dir('/tmp')
            ->run();
    }

    public function tryWorkdir()
    {
        // This example works like tryBuilderRollback,
        // but does equivalent operations using a working
        // directory. The working directory is deleted on rollback
        $collection = $this->collectionBuilder();

        $workdir = $collection->workDir('w');

        $collection
            ->taskFilesystemStack()
                ->touch("$workdir/g.txt")
            ->taskFilesystemStack()
                ->mkdir("$workdir/h")
                ->touch("$workdir/h/h.txt")
            ->taskFilesystemStack()
                ->mkdir("$workdir/h/i/c")
                ->touch("$workdir/h/i/i.txt");

        return $this->collectionBuilder()
            ->progressMessage('Start recursive collection')
            ->addTask($collection)
            ->progressMessage('Done with recursive collection')
            ->taskExec('ls xyzzy' . date('U'))
                ->dir('/tmp')
            ->run();
    }

    /**
     * Demonstrates Robo temporary directory usage.
     */
    public function tryTmpDir()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilder();

        // Get a temporary directory to work in. Note that we get a path
        // back, but the directory is not created until the task runs.
        $tmpPath = $collection->tmpDir();

        $result = $collection
            ->taskWriteToFile("$tmpPath/file.txt")
                ->line('Example file')
            ->run();

        if (is_dir($tmpPath)) {
            $this->say("The temporary directory at $tmpPath was not cleaned up after the collection completed.");
        } else {
            $this->say("The temporary directory at $tmpPath was automatically deleted.");
        }

        return $result;
    }

    /**
     * Description
     * @param $options
     * @option delay Miliseconds delay
     * @return type
     */
    public function tryProgress($options = ['delay' => 500])
    {
        $delay = $options['delay'];
        $delayUntilProgressStart = \Robo\Robo::config()->get(\Robo\Config::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL);
        $this->say("Progress bar will display after $delayUntilProgressStart seconds of activity.");

        $processList = range(1, 10);
        return $this->collectionBuilder()
            ->taskForEach($processList)
                ->iterationMessage('Processing {value}')
                ->call(
                    function ($value) use($delay) {
                        // TaskForEach::call should only be used to do
                        // non-Robo operations. To use Robo tasks in an
                        // iterator, @see TaskForEach::withBuilder.
                        usleep($delay * 1000); // delay units: msec, usleep units: usec
                    }
                )
            ->run();
    }

    public function tryIter()
    {
        $workdir = 'build/iter-example';
        $this->say("Creating sample direcories in $workdir.");

        $processList = ['cats', 'dogs', 'sheep', 'fish', 'horses', 'cows'];
        return $this->collectionBuilder()
            ->taskFilesystemStack()
                ->mkdir($workdir)
            ->taskCleanDir($workdir)
            ->taskForEach($processList)
                ->withBuilder(
                    function ($builder, $key, $value) use ($workdir) {
                        return $builder
                            ->taskFilesystemStack()
                                ->mkdir("$workdir/$value");
                    }
                )
            ->run();
    }
}

class ExceptionTask extends \Robo\Task\BaseTask
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function run()
    {
        throw new RuntimeException($this->message);
    }
}
