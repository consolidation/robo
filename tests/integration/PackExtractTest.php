<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class PackExtractTest extends TestCase
{
    use TestTasksTrait;
    use Task\Archive\Tasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
    }

    public function tearDown(): void
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
        if ((version_compare(PHP_VERSION, '7.4.0') >= 0) && (getenv('TRAVIS'))) {
          $this->markTestSkipped('Zip libraries apparently not available on Travis CI with PHP 7.4 image.');
        }

        // Archive directory and then extract it again with Archive and Extract tasks
        $this->fixtures->createAndCdToSandbox();

        // Assert fixture was created correctly
        $this->assertDirectoryExists('some/deeply/nested');
        $this->assertFileExists('some/deeply/nested/structu.re');
        $this->assertFileExists('some/deeply/existing_file');

        // First, take everything from the folder 'some/deeply' and make
        // an archive for it located in 'deep'
        $result = $this->taskPack("deeply.$archiveType")
            ->add(['deep' => 'some/deeply'])
            ->exclude(['structu3[0-9].re'])
            ->exclude('nested4')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists("deeply.$archiveType");
        // We are next going to extract the archive we created, this time
        // putting it into a folder called "extracted-$archiveType" (different
        // for each archive type we test).  We rely on the default behavior
        // of our extractor to remove the top-level directory in the archive
        // ("deeply").
        $result = $this->taskExtract("deeply.$archiveType")
            ->to("extracted-$archiveType")
            ->preserveTopDirectory(false) // this is the default
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertDirectoryExists("extracted-$archiveType");
        $this->assertDirectoryExists("extracted-$archiveType/nested");
        $this->assertFileExists("extracted-$archiveType/nested/structu.re");
        $this->assertFileDoesNotExist("extracted-$archiveType/nested3/structu31.re");
        $this->assertFileDoesNotExist("extracted-$archiveType/nested3/structu32.re");
        $this->assertDirectoryDoesNotExist("extracted-$archiveType/nested4");
        // Next, we'll extract the same archive again, this time preserving
        // the top-level folder.
        $this->taskExtract("deeply.$archiveType")
            ->to("preserved-$archiveType")
            ->preserveTopDirectory()
            ->run();
        $this->assertDirectoryExists("preserved-$archiveType");
        $this->assertDirectoryExists("preserved-$archiveType/deep/nested");
        $this->assertFileExists("preserved-$archiveType/deep/nested/structu.re");
        $this->assertFileDoesNotExist("preserved-$archiveType/deep/nested3/structu31.re");
        $this->assertFileDoesNotExist("preserved-$archiveType/deep/nested3/structu32.re");
        $this->assertDirectoryDoesNotExist("preserved-$archiveType/deep/nested4");
        // Make another archive, this time composed of fanciful locations
        $result = $this->taskPack("composed.$archiveType")
            ->add(['a/b/existing_file' => 'some/deeply/existing_file'])
            ->add(['x/y/z/structu.re' => 'some/deeply/nested/structu.re'])
            ->add(['missing_files' => 'some/deeply/nested3'])
            ->exclude(['structu3[0-9].re'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists("composed.$archiveType");
        // Extract our composed archive, and see if the resulting file
        // structure matches expectations.
        $result = $this->taskExtract("composed.$archiveType")
            ->to("decomposed-$archiveType")
            ->preserveTopDirectory()
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertDirectoryExists("decomposed-$archiveType");
        $this->assertDirectoryExists("decomposed-$archiveType/x/y/z");
        $this->assertFileExists("decomposed-$archiveType/x/y/z/structu.re");
        $this->assertFileDoesNotExist("decomposed-$archiveType/missing_files/structu31.re");
        $this->assertFileDoesNotExist("decomposed-$archiveType/missing_files/structu32.re");
        $this->assertDirectoryExists("decomposed-$archiveType/a/b");
        $this->assertFileExists("decomposed-$archiveType/a/b/existing_file");

    }
}
