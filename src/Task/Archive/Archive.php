<?php
namespace Robo\Task\Archive;

use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Creates a zip or tar archive.
 *
 * ``` php
 * <?php
 * $this->taskArchive(
 * <archiveFile>)
 * ->add('README')                         // Puts file 'README' in archive at the root
 * ->add('project')                        // Puts entire contents of directory 'project' in archinve inside 'project'
 * ->addFile('dir/file.txt' => 'file.txt') // Takes 'file.txt' from cwd and puts it in archive inside 'dir'.
 * ->run();
 * ?>
 * ```
 */
class Archive extends BaseTask implements PrintedInterface
{
    use \Robo\Common\DynamicParams;
    use \Robo\Common\Timer;
    use \Robo\Common\PHP;

    /**
     * The list of items to be packed into the archive.
     *
     * @var    array
     */
    private $items = [];

    /**
     * The full path to the archive to be created.
     *
     * @var    string
     */
    private $archiveFile;

    /**
     * Construct the class.
     *
     * @param   string  $folder   The full path to the folder and subfolders to pack.
     * @param   string  $zipname  The full path and name of the zipfile to create.
     *
     * @since   1.0
     */
    public function __construct($archive)
    {
        $this->archiveFile = $archive;
    }

    /**
     * Satisfy the parent requirement.
     *
     * @return  bool  Always returns true.
     *
     * @since   1.0
     */
    public function getPrinted()
    {
        return true;
    }

    /**
     * Add an item to the archive.
     *
     * Like file_exists(), the parameter may be a file or a directory.
     *
     * @var   string
     * @var   string
     */
    public function addFile($placementLocation, $filesystemLocation) {
        $this->items[$placementLocation] = $filesystemLocation;
        return $this;
    }

    /**
     * Add an item to the archive.
     *
     * @var   string|array
     */
    public function add($item) {
        if (is_array($item)) {
            $this->items = array_merge($this->items, $item);
        }
        else {
            $this->addFile($item, $item);
        }
        return $this;
    }

    /**
     * Create a zip archive for distribution.
     *
     * @return  bool  True on success | False on failure.
     *
     * @since   1.0
     */
    public function run()
    {
        $this->startTimer();

        // Use the file extension to determine what kind of archive to create.
        $fileInfo = new \SplFileInfo($this->archiveFile);
        $extension = strtolower($fileInfo->getExtension());
        if (empty($extension)) {
            return Result::error($this, "Archive filename must use an extension (e.g. '.zip') to specify the kind of archive to create.");
        }

        // Look up the specific archive creation method from the extension
        $archiveMethod = "create_" . strtr($extension, '.', '_');
        if (!method_exists($this, $archiveMethod)) {
            return Result::error($this, "Cannot create $extension archives");
        }

        try {
            // Inform the user which archive we are creating
            $this->printTaskInfo("Creating archive <info>{$this->archiveFile}</info>");
            $result = $this->$archiveMethod();
            $this->printTaskSuccess("<info>{$this->archiveFile}</info> created.");
        }
        catch(Exception $e) {
            $this->printTaskError("Could not create {$this->archiveFile}. " . $e->getMessage());
            $result = Result::error($this);
        }
        $data = $result->getData() + ['time' => $this->getExecutionTime()];
        return new Result($this, $result->getExitCode(), $result->getMessage(), $data);
    }

    protected function create_zip() {
        $result = $this->checkExtension('zip archiver', 'zlib');
        if (!$result->wasSuccessful()) {
            return $result;
        }

        $zip = new \ZipArchive($this->archiveFile, \ZipArchive::CREATE);
        $zip->open($this->archiveFile, \ZipArchive::CREATE);

        foreach ($this->items as $item) {
            if (is_dir($item)) {
                $finder = new Finder();
                $finder->files()->in($item);

                foreach ($finder as $file) {
                    $zip->addFile($file->getRealpath(), $file->getRelativePathname());
                }
            }
            elseif (is_file($item)) {
                $zip->addFile($item);
            }
            else {
                return Result::error($this, "Could not find $item for the archive.");
            }
        }

        return Result::success($this);
    }
}
