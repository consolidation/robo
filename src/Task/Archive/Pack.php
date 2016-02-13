<?php

namespace Robo\Task\Archive;

use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Finder\Finder;

/**
 * Creates a zip or tar archive.
 *
 * ``` php
 * <?php
 * $this->taskPack(
 * <archiveFile>)
 * ->add('README')                         // Puts file 'README' in archive at the root
 * ->add('project')                        // Puts entire contents of directory 'project' in archinve inside 'project'
 * ->addFile('dir/file.txt', 'file.txt')   // Takes 'file.txt' from cwd and puts it in archive inside 'dir'.
 * ->run();
 * ?>
 * ```
 */
class Pack extends BaseTask implements PrintedInterface
{
    use \Robo\Common\DynamicParams;
    use \Robo\Common\Timer;
    use \Robo\Common\PHPStatus;

    /**
     * The list of items to be packed into the archive.
     *
     * @var array
     */
    private $items = [];

    /**
     * The full path to the archive to be created.
     *
     * @var string
     */
    private $archiveFile;

    /**
     * Construct the class.
     *
     * @param string $folder  The full path to the folder and subfolders to pack.
     * @param string $zipname The full path and name of the zipfile to create.
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
     * @return bool Always returns true.
     *
     * @since   1.0
     */
    public function getPrinted()
    {
        return true;
    }

    /**
     * Add an item to the archive. Like file_exists(), the parameter
     * may be a file or a directory.
     *
     * @var string
     *             Relative path and name of item to store in archive
     * @var string
     *             Absolute or relative path to file or directory's location in filesystem
     */
    public function addFile($placementLocation, $filesystemLocation)
    {
        $this->items[$placementLocation] = $filesystemLocation;

        return $this;
    }

    /**
     * Alias for addFile, in case anyone has angst about using
     * addFile with a directory.
     *
     * @var string
     *             Relative path and name of directory to store in archive
     * @var string
     *             Absolute or relative path to directory or directory's location in filesystem
     */
    public function addDir($placementLocation, $filesystemLocation)
    {
        $this->addFile($placementLocation, $filesystemLocation);

        return $this;
    }

    /**
     * Add a file or directory, or list of same to the archive.
     *
     * @var string|array
     *                   If given a string, should contain the relative filesystem path to the
     *                   the item to store in archive; this will also be used as the item's
     *                   path in the archive, so absolute paths should not be used here.
     *                   If given an array, the key of each item should be the path to store
     *                   in the archive, and the value should be the filesystem path to the
     *                   item to store.
     */
    public function add($item)
    {
        if (is_array($item)) {
            $this->items = array_merge($this->items, $item);
        } else {
            $this->addFile($item, $item);
        }

        return $this;
    }

    /**
     * Create a zip archive for distribution.
     *
     * @return bool True on success | False on failure.
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

        try {
            // Inform the user which archive we are creating
            $this->printTaskInfo("Creating archive <info>{$this->archiveFile}</info>");
            if ($extension == 'zip') {
                $result = $this->archiveZip($this->archiveFile, $this->items);
            } else {
                $result = $this->archiveTar($this->archiveFile, $this->items);
            }
            $this->printTaskSuccess("<info>{$this->archiveFile}</info> created.");
        } catch (Exception $e) {
            $this->printTaskError("Could not create {$this->archiveFile}. ".$e->getMessage());
            $result = Result::error($this);
        }
        $this->stopTimer();
        $result['time'] = $this->getExecutionTime();

        return $result;
    }

    protected function archiveTar($archiveFile, $items)
    {
        $tar_object = new \Archive_Tar($archiveFile);
        foreach ($items as $placementLocation => $fileSystemLocation) {
            $p_remove_dir = $fileSystemLocation;
            $p_add_dir = $placementLocation;
            if (is_file($fileSystemLocation)) {
                $p_remove_dir = dirname($fileSystemLocation);
                $p_add_dir = dirname($placementLocation);
                if (basename($fileSystemLocation) != basename($placementLocation)) {
                    return Result::error($this, "Tar archiver does not support renaming files during extraction; could not add $fileSystemLocation as $placementLocation.");
                }
            }

            if (!$tar_object->addModify([$fileSystemLocation], $p_add_dir, $p_remove_dir)) {
                return Result::error($this, "Could not add $fileSystemLocation to the archive.");
            }
        }

        return Result::success($this);
    }

    protected function archiveZip($archiveFile, $items)
    {
        $result = $this->checkExtension('zip archiver', 'zlib');
        if (!$result->wasSuccessful()) {
            return $result;
        }

        $zip = new \ZipArchive($archiveFile, \ZipArchive::CREATE);
        if (!$zip->open($archiveFile, \ZipArchive::CREATE)) {
            return Result::error($this, "Could not create zip archive {$archiveFile}");
        }
        $result = $this->addItemsToZip($zip, $items);
        $zip->close();

        return $result;
    }

    protected function addItemsToZip($zip, $items)
    {
        foreach ($items as $placementLocation => $fileSystemLocation) {
            if (is_dir($fileSystemLocation)) {
                $finder = new Finder();
                $finder->files()->in($fileSystemLocation);

                foreach ($finder as $file) {
                    if (!$zip->addFile($file->getRealpath(), "{$placementLocation}/{$file->getRelativePathname()}")) {
                        return Result::error($this, "Could not add directory $fileSystemLocation to the archive; error adding {$file->getRealpath()}.");
                    }
                }
            } elseif (is_file($fileSystemLocation)) {
                if (!$zip->addFile($fileSystemLocation, $placementLocation)) {
                    return Result::error($this, "Could not add file $fileSystemLocation to the archive.");
                }
            } else {
                return Result::error($this, "Could not find $fileSystemLocation for the archive.");
            }
        }

        return Result::success($this);
    }
}
