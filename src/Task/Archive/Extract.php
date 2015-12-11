<?php
namespace Robo\Task\Archive;

use Robo\Result;
use Robo\Task\BaseTask;
use Alchemy\Zippy\Zippy;

/**
 * Extracts an archive.
 *
 * ``` php
 * <?php
 * $this->taskExtract($archivePath)
 *  ->to($destination)
 *  ->run();
 * ?>
 * ```
 *
 * @method to(string) location to store extracted files
 */
class Extract extends BaseTask
{
    use \Robo\Common\DynamicParams;
    use \Robo\Common\Timer;

    protected $filename;
    protected $to;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

  function run() {
    if (!file_exists($this->filename)) {
      $this->printTaskError("File {$this->filename} does not exist");
      return false;
    }

    $status = 0;

    // We will first extract to $extractLocation and then move to $this->to
    $extractLocation = static::getTmpDir();
    @mkdir($extractLocation);
    @mkdir(dirname($this->to));

    $this->startTimer();
    $this->printTaskInfo("Extracting <info>{$this->filename}</info>");

    try {
      $zippy = Zippy::load();
      $archive = $zippy->open($this->filename);
      $archive->extract($extractLocation);
      $this->stopTimer();

      // Now, we want to move the extracted files to $this->to. There
      // are two possibilities that we must consider:
      //
      // (1) Archived files were encapsulated in a folder with an arbitrary name
      // (2) There was no encapsulating folder, and all the files in the archive
      //     were extracted into $extractLocation
      //
      // In the case of (1), we want to move and rename the encapsulating folder
      // to $this->to.
      //
      // In the case of (2), we will just move and rename $extractLocation.
      $filesInExtractLocation = glob("$extractLocation/*");
      $hasEncapsulatingFolder = ((count($filesInExtractLocation) == 1) && is_dir($filesInExtractLocation[0]));
      if ($hasEncapsulatingFolder) {
        rename($filesInExtractLocation[0], $this->to);
        rmdir($extractLocation);
      }
      else {
        rename($extractLocation, $this->to);
      }
    }
    catch (Exception $e) {
      $this->printTaskError("Could not extract {$this->filename}. " . $e->getMessage());
      $status = 1;
    }
    return new Result($this, $status, '', ['time' => $this->getExecutionTime()]);
  }

  protected static function getTmpDir() {
    return getcwd() . '/tmp' . rand() . time();
  }
}
