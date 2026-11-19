<?php

namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class GenerateMarkdownDocTest extends TestCase
{
    use TestTasksTrait;
    use Collection\Tasks;
    use Task\Development\Tasks;
    use Task\File\Tasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
        $this->fixtures->createAndCdToSandbox();
    }

    public function tearDown(): void
    {
        $this->fixtures->cleanup();
    }

    public function testGenerateDocumentation()
    {
        $sourceFile = $this->fixtures->dataFile('TestedRoboTask.php');
        $this->assertFileExists($sourceFile);
        include $sourceFile;
        $this->assertTrue(class_exists('TestedRoboTask'));

        $collection = $this->collectionBuilderForTest();
        $taskGenerator = $collection->taskGenDoc("TestedRoboTask.md");
        $taskGenerator->filterClasses(function (\ReflectionClass $r) {
            return !($r->isAbstract() || $r->isTrait()) && $r->implementsInterface('Robo\Contract\TaskInterface');
        })->prepend("# TestedRoboTask Tasks");
        $taskGenerator->docClass('TestedRoboTask');

        $taskGenerator->filterMethods(
            function (\ReflectionMethod $m) {
                if ($m->isConstructor() || $m->isDestructor() || $m->isStatic()) {
                    return false;
                }
                $undocumentedMethods =
                [
                    '',
                    'run',
                    '__call',
                    'inflect',
                    'injectDependencies',
                    'getCommand',
                    'getPrinted',
                    'getConfig',
                    'setConfig',
                    'logger',
                    'setLogger',
                    'setProgressIndicator',
                    'progressIndicatorSteps',
                    'setBuilder',
                    'getBuilder',
                    'collectionBuilder',
                ];
                return !in_array($m->name, $undocumentedMethods) && $m->isPublic(); // methods are not documented
            }
        )->processClassSignature(
            function ($c) {
                return "## " . preg_replace('~Task$~', '', $c->getShortName()) . "\n";
            }
        )->processClassDocBlock(
            function (\ReflectionClass $c, $doc) {
                $doc = preg_replace('~@method .*?(.*?)\)~', '* `$1)` ', $doc);
                $doc = str_replace('\\'.$c->name, '', $doc);
                return $doc;
            }
        )->processMethodSignature(
            function (\ReflectionMethod $m, $text) {
                return str_replace('#### *public* ', '* `', $text) . '`';
            }
        )->processMethodDocBlock(
            function (\ReflectionMethod $m, $text) {
                return $text;
            }
        );

        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists('TestedRoboTask.md');

        $contents = file_get_contents('TestedRoboTask.md');
        $this->assertStringContainsString('A test task file. Used for testig documentation generation.', $contents);
        $this->assertStringContainsString('taskTestedRoboTask', $contents);
        if (PHP_OS_FAMILY === 'Windows') {
            $this->assertStringContainsString('* `to($dst)`', $contents);
            $this->assertStringContainsString('* `param string` $dst', $contents);
            $this->assertStringContainsString('* `return Concat` The current instance', $contents);
            $this->assertStringContainsString('Set the destination file', $contents);
        } else {
            $this->assertStringContainsString(<<<'EOT'
* `to($dst)`

* `param string` $dst
* `return Concat` The current instance

Set the destination file
EOT, $contents);
        }
    }

    public function testMarkdownOfUnionType()
    {
        if (version_compare(PHP_VERSION, '8.0') < 0) {
            $this->markTestSkipped('Requires PHP 8.0');
        }

        $sourceFile = $this->fixtures->dataFile('ClassWithUnionParam.php');
        $this->assertFileExists($sourceFile);
        include $sourceFile;
        $this->assertTrue(class_exists('ClassWithUnionParam'));

        $collection = $this->collectionBuilderForTest();
        $taskGenerator = $collection->taskGenDoc("ClassWithUnionParam.md");
        $taskGenerator->docClass('ClassWithUnionParam');
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists('ClassWithUnionParam.md');

        $contents = file_get_contents('ClassWithUnionParam.md');
        $this->assertStringContainsString('A test file. Used for testing documentation generation.', $contents);

        if (PHP_OS_FAMILY === 'Windows') {
            $this->assertStringContainsString('#### *final public static* executeTask($task, $documented)', $contents);
            $this->assertStringContainsString('* `author` Gintautas Miselis <gintautas@localhost>', $contents);
            $this->assertStringContainsString('* `since` 2.0.0 New method', $contents);
            $this->assertStringContainsString('* `param \Robo\Task\Composer\Install|\Robo\Task\Composer\Update` $task', $contents);
            $this->assertStringContainsString('* `param string` $documented Documented parameter', $contents);
            $this->assertStringContainsString('* `return array|string`', $contents);
            $this->assertStringContainsString('Short description', $contents);
            $this->assertStringContainsString('Long description 1', $contents);
            $this->assertStringContainsString('Long description 2', $contents);
            $this->assertStringContainsString('Long description 3', $contents);
        } else {
            $this->assertStringContainsString(<<<'EOT'
#### *final public static* executeTask($task, $documented)

* `author` Gintautas Miselis <gintautas@localhost>
* `since` 2.0.0 New method
* `param \Robo\Task\Composer\Install|\Robo\Task\Composer\Update` $task
* `param string` $documented Documented parameter
* `return array|string`

Short description

Long description 1
Long description 2
Long description 3
EOT, $contents);
        }
    }

    public function testMethodOrderIsStable()
    {
        $sourceFile = $this->fixtures->dataFile('ClassWithMethodOrder.php');
        $this->assertFileExists($sourceFile);
        include $sourceFile;
        $this->assertTrue(class_exists('ClassWithMethodOrder'));

        $collection = $this->collectionBuilderForTest();
        $taskGenerator = $collection->taskGenDoc("ClassWithMethodOrder.md");
        $taskGenerator->docClass('ClassWithMethodOrder');
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        // Own methods in source order, then trait methods, then inherited methods.
        $contents = file_get_contents('ClassWithMethodOrder.md');
        $positions = [];
        foreach (['zeta', 'alpha', 'fromTrait', 'fromParent'] as $method) {
            $position = strpos($contents, "* $method()");
            $this->assertNotFalse($position, "$method() is documented");
            $positions[] = $position;
        }
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);
    }
}
