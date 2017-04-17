<?php
namespace Robo\Task\Development;

use Robo\Common\Timer;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Creates a zip file.
 *
 * ``` php
 * <?php
 * $this->PackZip(
 * <full path to folder to zip>,
 * <full path to zip filename to create>)
 * ->run();
 * ?>
 * ```
 */
class PackZip extends BaseTask implements PrintedInterface
{
	// Use the timer
	use Timer;

	/**
	 * The full path to the folder where the files reside to be packed.
	 *
	 * @var    string
	 */
	private $folder = '';

	/**
	 * The full path to the zipfile to be created.
	 *
	 * @var    string
	 */
	private $zipname = '';

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
	 * Construct the class.
	 *
	 * @param   string  $folder   The full path to the folder and subfolders to pack.
	 * @param   string  $zipname  The full path and name of the zipfile to create.
	 *
	 * @since   1.0
	 */
	public function __construct($folder, $zipname)
	{
		// Prepare the folder
		// Set all separators to forward slashes for comparison
		$this->folder = substr(str_replace('\\', '/', $folder), 0, -1);

		// Prepare the zipname
		$file = new \SplFileInfo($zipname);
		$this->zipname = $zipname;

		if (file_exists($file->getRealPath()))
		{
			@unlink($file->getRealPath());
		}

		// Instantiate the ZipArchive
		$this->zip = new \ZipArchive($file->getRealPath(), \ZipArchive::CREATE);
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
		// Inform the user which archive we are creating
		$this->printTaskInfo("creating <info>{$this->zipname}</info>");

		// Instantiate the zip archive
		$this->zip->open($this->zipname, \ZipArchive::CREATE);

		// Count the files to pack
		$files = 0;

		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->folder), \RecursiveIteratorIterator::SELF_FIRST) as $subfolder)
		{
			if ($subfolder->isFile())
			{
				$files++;
			}
		}

		$this->printTaskInfo('packing ' . $files . ' files into zip');

		// Start the progress bar
		$progress = new ProgressBar($this->getOutput());
		$progress->start($files);

		// Start the timer
		$this->startTimer();

		// Process the files to zip
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->folder), \RecursiveIteratorIterator::SELF_FIRST) as $subfolder)
		{
			if ($subfolder->isFile())
			{
				// Set all separators to forward slashes for comparison
				$usefolder = str_replace('\\', '/', $subfolder->getPath());

				// Drop the folder part as we don't want them added to archive
				$addpath = str_ireplace($this->folder, '', $usefolder);

				// Remove preceding slash
				$findfirst = strpos($addpath, '/');

				if ($findfirst == 0 && $findfirst !== false)
				{
					$addpath = substr($addpath, 1);
				}

				if (strlen($addpath) > 0 || empty($addpath))
				{
					$addpath .= '/';
				}

				$options = array('add_path' => $addpath, 'remove_all_path' => true);
				$this->zip->addGlob($usefolder . '/*.*', GLOB_BRACE, $options);

				// Update the progess bar
				$progress->advance();
			}
		}

		// Finish the progressbar
		$progress->finish();

		// Close the zip archive
		$this->zip->close();

		// Stop the timer
		$this->stopTimer();

		// Inform user the package has been produced
		$this->getOutput()->writeln("\r\n");
		$this->printTaskSuccess("<info>{$this->zipname}</info> produced");

		return Result::success($this, '', ['time' => $this->getExecutionTime()]);
	}
}
