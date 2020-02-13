<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class WriteFileTest extends TestCase
{
    use TestTasksTrait;
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

    public function writeFewLines()
    {
        // write lines with WriteToFile task
        $result = $this->taskWriteToFile('blogpost.md')
           ->line('****')
           ->line('hello world')
           ->line('****')
           ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('blogpost.md');
        $contents = file_get_contents('blogpost.md');
        $expreded = <<<HERE
****
hello world
****

HERE;
        $this->assertContains($expected, $contents);
    }

    public function appendToFile()
    {
        $result = $this->taskWriteToFile('a.txt')
           ->append()
           ->line('hello world')
           ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('a.txt');
        $contents = file_get_contents('a.txt');
        $expected = <<<HERE
Ahello world

HERE;
        $this->assertContains($expected, $contents);
    }

    public function testWouldChange()
    {
        $writeTask = $this->taskWriteToFile('a.txt')
           ->append();
        $this->assertEquals(false, $writeTask->wouldChange(), "No changes to test file.");
        $writeTask->line('hello world');
        $this->assertEquals(true, $writeTask->wouldChange(), "Test file would change.");
    }

    public function insertFile()
    {
        $result = $this->taskWriteToFile('a.txt')
            ->line('****')
            ->textFromFile('b.txt')
            ->line("C")
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('a.txt');
        $contents = file_get_contents('a.txt');
        $expected = <<<HERE
****
BC

HERE;
        $this->assertContains($expected, $contents);
    }

    public function appendIfMatch()
    {
        // append lines with WriteToFile task, but only if pattern does not match
        $result = $this->taskWriteToFile('blogpost.md')
           ->line('****')
           ->line('hello world')
           ->line('****')
           ->appendUnlessMatches('/hello/', 'Should not add this')
           ->appendUnlessMatches('/goodbye/', 'Should add this')
           ->appendIfMatches('/hello/', ' and should also add this')
           ->appendIfMatches('/goodbye/', ' but should not add this')
           ->appendIfMatches('/should/', '!')
           ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('blogpost.md');
        $contents = file_get_contents('blogpost.md');
        $expected = <<<HERE
****
hello world
****
Should add this and should also add this!
HERE;
        $this->assertContains($expected, $contents);
    }

    public function replaceInFile()
    {
        $result = $this->taskReplaceInFile('a.txt')
            ->from('A')
            ->to('B')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('a.txt');
        $contents = file_get_contents('a.txt');
        $this->assertContains('B', $contents);

    }

    public function replaceMultipleInFile()
    {
        $result = $this->taskReplaceInFile('box/robo.txt')
            ->from(array('HELLO', 'ROBO'))
            ->to(array('Hello ', 'robo.li!'))
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $contents = file_get_contents('box/robo.txt');
        $this->assertContains('Hello robo.li!', $contents);
    }
}

