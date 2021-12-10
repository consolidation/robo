<?php
namespace RoboExample\Robo\Plugin\Commands;

use Robo\Result;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Robo\Symfony\ConsoleIO;

/**
 * Example Robo Plugin Commands.
 *
 * To create a Robo Plugin, create a standard Composer project. The
 * namespace for your commands must end Robo\Plugin\Commands, and
 * this suffix must immediately follow some namespace in your composer.json
 * file's autoload section.
 *
 * For example:
 *
 * "autoload": {
 *         "psr-4": {
 *             "RoboExample\\": "src"
 *         }
 *     },
 *
 * In this instance, the namespace for your plugin commands must be
 * RoboExample\Robo\Plugin\Commands.
 */
class ExampleCommands extends \Robo\Tasks
{
    /**
     * Demonstrate variable args and options
     *
     * This command will concatenate two or more parameters. If the --flip flag
     * is provided, then the result is the concatenation of two and one.
     *
     * @command try:echo
     * @param array $args The argument list
     * @param bool $flip The "flip" option
     * @option flip Whether or not the second parameter should come first in the result.
     * @aliases c
     * @usage bet alpha --flip
     *   Concatenate "alpha" and "bet".
     */
    public function tryEcho(array $args, $flip = false)
    {
        if ($flip) {
            $args = array_reverse($args);
        }
        return implode(" ", $args);
    }

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
     * Demonstrates Robo input APIs using ConsoleIO (recommended).
     */
    public function tryInput(ConsoleIO $io)
    {
        $io->say('The <b>expression</b> <bogus>is</bogus> <info>a < b</> it even works');
        $answer = $io->ask('how are you?');
        $io->say('You are '.$answer);
        $yes = $io->confirm('Do you want one more question?');
        if (!$yes) {
            return Result::cancelled();
        }
        $lang = $io->ask('what is your favorite scripting language?', 'PHP');
        $io->say($lang);
        $pin = $io->askHidden('Ok, now tell your PIN code (it is hidden)');
        $io->yell('Ha-ha, your pin code is: '.$pin);
        $io->say('Bye!');
    }

    /**
     * Demonstrate Robo configuration.
     *
     * Config values are loaded from the followig locations:
     *
     *  - [Robo Project]/robo.yml
     *  - $HOME/.robo/robo.yml
     *  - $CWD/robo.yml
     *  - Environment variables ROBO_CONFIG_KEY (e.g. ROBO_OPTIONS_PROGRESS_DELAY)
     *  - Overridden on the commandline via -Doptions.progress-delay=value
     *
     * @param string $key Name of the option to read (e.g. options.progress-delay)
     * @option opt An option whose value is printed. Can be overridden in
     *   configuration via the configuration key command.try.config.options.opt.
     * @option show-all Also print out the value of all configuration options
     */
    public function tryConfig(ConsoleIO $io, $key = 'options.progress-delay', $options = ['opt' => '0', 'show-all' => false])
    {
        $value = \Robo\Robo::config()->get($key);

        $io->say("The value of $key is " . var_export($value, true));
        $io->say("The value of --opt (command.try.config.options.opt) is " . var_export($options['opt'], true));

        if ($options['show-all']) {
            $io->say(var_export(\Robo\Robo::config()->export(), true) . "\n");
        }
    }

    /**
     * Demonstrates serial execution.
     *
     * @option $printed Print the output of each process.
     * @option $error Include an extra process that fails.
     */
    public function tryExec(ConsoleIO $io, $options = ['printed' => true, 'error' => false])
    {
        $dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $tasks = $this->collectionBuilder($io)
            ->taskExec('php')
                ->args(["$dir/tests/_data/parascript.php", "hey", "4"])
            ->taskExec('php')
                ->args(["$dir/tests/_data/parascript.php", "hoy", "3"])
            ->taskExec('php')
                ->args(["$dir/tests/_data/parascript.php", "gou", "2"])
            ->taskExec('php')
                ->args(["$dir/tests/_data/parascript.php", "die", "1"]);
        if ($options['error']) {
            $tasks->taskExec('ls')->arg("$dir/tests/_data/filenotfound");
        }
        return $tasks->run();
    }

    /**
     * Demonstrates capturing output from taskExec
     */
    public function tryCaptureExec(ConsoleIO $io)
    {
        $result = $this->taskExec('echo')->args(['one', 'two', 'three'])->printOutput(false)->run();

        $io->writeln('Captured output from exec >>> ' . $result->getOutputData());
    }

    /**
     * Demonstrates parallel execution.
     *
     * @option $printed Print the output of each process.
     * @option $error Include an extra process that fails.
     */
    public function tryPara(ConsoleIO $io, $options = ['printed' => true, 'error' => false])
    {
        $dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $para = $this->collectionBuilder($io)
            ->taskParallelExec()
                ->printOutput($options['printed'])
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
     * try:opt-required
     */
    public function tryOptRequired(ConsoleIO $io, $options = ['foo' => InputOption::VALUE_REQUIRED])
    {
        $io->writeln("foo is " . $options['foo']);
    }

    /**
     * Demonstrates Robo argument passing.
     *
     * @param string $a The first parameter. Required.
     * @param string $b The second parameter. Optional.
     */
    public function tryArgs(ConsoleIO $io, $a, $b = 'default')
    {
        $io->say("The parameter a is $a and b is $b");
    }

    /**
     * Demonstrate Robo variable argument passing.
     *
     * @param array $a A list of commandline parameters.
     * @param array $options
     */
    public function tryArrayArgs(ConsoleIO $io, array $a, array $options = ['foo' => []])
    {
        $io->say("The parameters passed are:\n" . var_export($a, true));
        if (!empty($options['foo'])) {
            $io->say("The options passed via --foo are:\n" . var_export($options['foo'], true));
        }
    }

    /**
     * Demonstrate use of Symfony $input object in Robo in place of
     * the usual "parameter arguments".
     *
     * Note that $io provides '$input', so you do not need to declare both
     * as parameters. See next example.
     *
     * @arg array $a A list of commandline parameters.
     * @option foo
     * @default a []
     * @default foo []
     */
    public function trySymfony(ConsoleIO $io, InputInterface $input)
    {
        $a = $input->getArgument('a');
        $io->writeln("The parameters passed are:\n" . var_export($a, true));
        $foo = $input->getOption('foo');
        if (!empty($foo)) {
            $io->writeln("The options passed via --foo are:\n" . var_export($foo, true));
        }
    }

    /**
     * Demonstrate use of Symfony $input object in Robo in place of
     * the usual "parameter arguments".
     *
     * @command try:console-io
     * @arg array $a A list of commandline parameters.
     * @option foo
     * @default a []
     * @default foo []
     */
    public function tryConsoleIO(ConsoleIO $io)
    {
        $a = $io->input()->getArgument('a');
        $io->say("The parameters passed are:\n" . var_export($a, true));
        $foo = $io->input()->getOption('foo');
        if (!empty($foo)) {
            $io->say("The options passed via --foo are:\n" . var_export($foo, true));
        }
    }

    /**
     * Demonstrate Robo boolean options.
     *
     * @param array $opts The options.
     * @option boolean $silent Supress output.
     */
    public function tryOptbool(ConsoleIO $io, $opts = ['silent|s' => false])
    {
        if (!$opts['silent']) {
            $io->say("Hello, world");
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
    public function tryOpenBrowser(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)->taskOpenBrowser([
            'https://robo.li',
            'https://github.com/consolidation-org/Robo'
            ])->run();
    }

    /**
     * Demonstrate Robo error output and command failure.
     */
    public function tryError(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)->taskExec('ls xyzzy' . date('U'))->dir('/tmp')->run();
    }

    /**
     * Demonstrate Robo standard output and command success.
     */
    public function trySuccess(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)->taskExec('pwd');
    }

    /**
     * @field-labels
     *   name: Name
     *   species: Species
     *   legs: Legs
     *   food: Favorite Food
     *   id: Id
     * @return PropertyList
     */
    public function tryInfo()
    {
        $outputData = [
            'name' => 'fluffy',
            'species' => 'cat',
            'legs' => 4,
            'food' => 'salmon',
            'id' => 389245032,
        ];

        $data = new PropertyList($outputData);

        // Add a render function to transform cell data when the output
        // format is a table, or similar.  This allows us to add color
        // information to the output without modifying the data cells when
        // using yaml or json output formats.
        $data->addRendererFunction(
            // n.b. There is a fourth parameter $rowData that may be added here.
            function ($key, $cellData, FormatterOptions $options) {
                if ($key == 'name') {
                    return "<info>$cellData</>";
                }
                return $cellData;
            }
        );

        return $data;
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
     * @aliases tf
     *
     * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
     */
    public function tryFormatters($somthing = 'default', $options = ['format' => 'table', 'fields' => ''])
    {
        $outputData = [
            'en' => [ 'first' => 'One',  'second' => 'Two',  'third' => 'Three' ],
            'de' => [ 'first' => 'Eins', 'second' => 'Zwei', 'third' => 'Drei'  ],
            'jp' => [ 'first' => 'Ichi', 'second' => 'Ni',   'third' => 'San'   ],
            'es' => [ 'first' => 'Uno',  'second' => 'Dos',  'third' => 'Tres'  ],
        ];
        return new RowsOfFields($outputData);
    }

    /**
     * Try word wrapping
     *
     * @field-labels
     *   first: First
     *   second: Second
     *
     * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
     */
    public function tryWrap()
    {
        $data = [
            [
                'first' => 'This is a really long cell that contains a lot of data. When it is rendered, it should be wrapped across multiple lines.',
                'second' => 'This is the second column of the same table. It is also very long, and should be wrapped across multiple lines, just like the first column.',
            ]
        ];
        return new RowsOfFields($data);
    }

    /**
     * Demonstrate an alter hook with an option
     *
     * @hook alter try:formatters
     * @option $french Add a row with French numbers.
     * @usage try:formatters --french
     */
    public function alterFormatters($result, CommandData $commandData)
    {
        if ($commandData->input()->getOption('french')) {
            $result['fr'] = [ 'first' => 'Un',  'second' => 'Deux',  'third' => 'Trois'  ];
        }

        return $result;
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
    public function tryBuilder(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)
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

    public function tryState(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)
            ->taskExec('uname -n')
                ->printOutput(false)
                ->storeState('system-name')
            ->taskFilesystemStack()
                ->deferTaskConfiguration('mkdir', 'system-name')
            ->run();
    }

    public function tryBuilderRollback(ConsoleIO $io)
    {
        // This example will create two builders, and add
        // the first one as a child of the second in order
        // to demonstrate nested rollbacks.
        $collection = $this->collectionBuilder($io)
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

        return $this->collectionBuilder($io)
            ->progressMessage('Start recursive collection')
            ->addTask($collection)
            ->progressMessage('Done with recursive collection')
            ->taskExec('ls xyzzy' . date('U'))
                ->dir('/tmp')
            ->run();
    }

    public function tryWorkdir(ConsoleIO $io)
    {
        // This example works like tryBuilderRollback,
        // but does equivalent operations using a working
        // directory. The working directory is deleted on rollback
        $collection = $this->collectionBuilder($io);

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

        return $this->collectionBuilder($io)
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
    public function tryTmpDir(ConsoleIO $io)
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilder($io);

        // Get a temporary directory to work in. Note that we get a path
        // back, but the directory is not created until the task runs.
        $tmpPath = $collection->tmpDir();

        $result = $collection
            ->taskWriteToFile("$tmpPath/file.txt")
                ->line('Example file')
            ->run();

        if (is_dir($tmpPath)) {
            $io->say("The temporary directory at $tmpPath was not cleaned up after the collection completed.");
        } else {
            $io->say("The temporary directory at $tmpPath was automatically deleted.");
        }

        return $result;
    }

    /**
     * Description
     * @param $options
     * @option delay Miliseconds delay
     * @return type
     */
    public function tryProgress(ConsoleIO $io, $options = ['delay' => 500])
    {
        $delay = $options['delay'];
        $delayUntilProgressStart = \Robo\Robo::config()->get(\Robo\Config::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL);
        $io->say("Progress bar will display after $delayUntilProgressStart seconds of activity.");

        $processList = range(1, 10);
        return $this->collectionBuilder($io)
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

    public function tryIter(ConsoleIO $io)
    {
        $workdir = 'build/iter-example';
        $this->say("Creating sample direcories in $workdir.");

        $processList = ['cats', 'dogs', 'sheep', 'fish', 'horses', 'cows'];
        return $this->collectionBuilder($io)
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
