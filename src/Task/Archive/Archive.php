<?php
namespace Robo\Task\Archive;

use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Helper\ProgressBar;
use Alchemy\Zippy\Zippy;

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

    /**
     * Our archiver.
     *
     * @var Zippy
     */
    private $zippy;

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
        $this->zippy = Zippy::load();
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
        $status = 0;

        // Inform the user which archive we are creating
        $this->printTaskInfo("Creating archive <info>{$this->archiveFile}</info>");

        try {
            $archive = $this->zippy->create($this->archiveFile, $this->items);

            $this->printTaskSuccess("<info>{$this->archiveFile}</info> produced");
        }
        catch(Exception $e) {
            $this->printTaskError("Could not create {$this->archiveFile}. " . $e->getMessage());
            $status = 1;
        }

        return new Result($this, $status, '', ['time' => $this->getExecutionTime()]);
    }
}
