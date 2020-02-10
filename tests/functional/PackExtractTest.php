<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class PackExtractTest extends TestCase
{
    use TestTasksTrait;
    use Task\Archive\loadTasks;

    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
    }

    public function tearDown()
    {
        $this->fixtures->cleanup();
    }

    /**
     * Data provider for testPackExtract.
     */
    public function archiveTypeProvider()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return [['zip']];
        }
        return [['zip'], ['tar'], ['tar.gz'], ['tar.bz2'], ['tgz']];
    }

    /**
     * Test all of the different sorts of archivers
     *
     * @dataProvider archiveTypeProvider
     */
    public function testPackExtract($archiveType)
    {
        // Archive directory and then extract it again with Archive and Extract tasks
        $this->fixtures->createAndCdToSandbox();

        // Assert fixture was created correctly
        $this->assertDirectoryExists('some/deeply/nested');
        $this->assertFileExists('some/deeply/nested/structu.re');
        $this->assertFileExists('some/deeply/existing_file');

        // First, take everything from the folder 'some/deeply' and make
        // an archive for it located in 'deep'
        $this->taskPack("deeply.$archiveType")
            ->add(['deep' => 'some/deeply'])
            ->run();
        $this->assertFileExists("deeply.$archiveType");
        // We are next going to extract the archive we created, this time
        // putting it into a folder called "extracted-$archiveType" (different
        // for each archive type we test).  We rely on the default behavior
        // of our extractor to remove the top-level directory in the archive
        // ("deeply").
        $this->taskExtract("deeply.$archiveType")
            ->to("extracted-$archiveType")
            ->preserveTopDirectory(false) // this is the default
            ->run();
        $this->assertDirectoryExists("extracted-$archiveType");
        $this->assertDirectoryExists("extracted-$archiveType/nested");
        $this->assertFileExists("extracted-$archiveType/nested/structu.re");
        // Next, we'll extract the same archive again, this time preserving
        // the top-level folder.
        $this->taskExtract("deeply.$archiveType")
            ->to("preserved-$archiveType")
            ->preserveTopDirectory()
            ->run();
        $this->assertDirectoryExists("preserved-$archiveType");
        $this->assertDirectoryExists("preserved-$archiveType/deep/nested");
        $this->assertFileExists("preserved-$archiveType/deep/nested/structu.re");
        // Make another archive, this time composed of fanciful locations
        $this->taskPack("composed.$archiveType")
            ->add(['a/b/existing_file' => 'some/deeply/existing_file'])
            ->add(['x/y/z/structu.re' => 'some/deeply/nested/structu.re'])
            ->run();
        $this->assertFileExists("composed.$archiveType");
        // Extract our composed archive, and see if the resulting file
        // structure matches expectations.
        $this->taskExtract("composed.$archiveType")
            ->to("decomposed-$archiveType")
            ->preserveTopDirectory()
            ->run();
        $this->assertDirectoryExists("decomposed-$archiveType");
        $this->assertDirectoryExists("decomposed-$archiveType/x/y/z");
        $this->assertFileExists("decomposed-$archiveType/x/y/z/structu.re");
        $this->assertDirectoryExists("decomposed-$archiveType/a/b");
        $this->assertFileExists("decomposed-$archiveType/a/b/existing_file");

    }
}
