<?php
namespace Robo;

use \CliGuy;

class GenerateMarkdownDocCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toGenerateDocumentation(CliGuy $I)
    {
        $sourceFile = codecept_data_dir() . 'TestedRoboTask.php';
        $I->seeFileFound($sourceFile);
        include $sourceFile;
        $I->assertTrue(class_exists('TestedRoboTask'));

        $collection = $I->collectionBuilder();
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

        $collection->run();
        $I->seeFileFound('TestedRoboTask.md');

        $contents = file_get_contents('TestedRoboTask.md');
        $I->assertContains('A test task file. Used for testig documentation generation.', $contents);
        $I->assertContains('taskTestedRoboTask', $contents);
        $I->assertContains('Set the destination file', $contents);
    }
}
