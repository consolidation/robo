<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class GenerateMarkdownDocTest extends TestCase
{
    use TestTasksTrait;
    use Collection\loadTasks;
    use Task\Development\loadTasks;
    use Task\File\loadTasks;

    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
        $this->fixtures->createAndCdToSandbox();
    }

    public function tearDown()
    {
        $this->fixtures->cleanup();
    }

    public function testGenerateDocumentation()
    {
        $sourceFile = $this->fixtures->dataFile('TestedRoboTask.php');
        $this->assertFileExists($sourceFile);
        include $sourceFile;
        $this->assertTrue(class_exists('TestedRoboTask'));

        $collection = $this->collectionBuilder();
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

                return $text ? ' ' . trim(strtok($text, "\n"), "\n") : '';
            }
        );

        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists('TestedRoboTask.md');

        $contents = file_get_contents('TestedRoboTask.md');
        $this->assertContains('A test task file. Used for testig documentation generation.', $contents);
        $this->assertContains('taskTestedRoboTask', $contents);
        $this->assertContains('Set the destination file', $contents);
    }

}
