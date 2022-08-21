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
        $this->assertStringContainsString(<<<'EOT'
* `to($dst)`

* `param string` $dst
* `return Concat` The current instance

Set the destination file
EOT
, $contents);
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
        $this->assertStringContainsString(<<<'EOT'
#### *final public static* executeTask($task)

* `author` Gintautas Miselis <gintautas@localhost>
* `since` 2.0.0 New method
* `param \Robo\Task\Composer\Install|\Robo\Task\Composer\Update` $task
* `return array|string`

Short description

Long description 1
Long description 2
Long description 3
EOT
, $contents);
    }
}
