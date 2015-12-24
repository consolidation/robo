<?php

namespace Task;

use Robo\Common\TaskIO;
use Robo\Contract\TaskInterface;
use Robo\Result;
use Symfony\Component\Console\Helper\ProgressBar;

class DownloadFileTask implements TaskInterface
{
    use TaskIO;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    protected $url;
    protected $destination;

    public function __construct($url, $destination)
    {
        $this->url = $url;
        $this->destination = $destination;
    }

    public function run()
    {
        $context = stream_context_create(array(), array('notification' => array($this, 'progress')));
        $resource = fopen($this->url, 'r', null, $context);

        $stream = fopen($this->destination, 'w+');

        if (!$stream) {
            return Result::error($this, 'Could not create local file');
        }
        stream_copy_to_stream($resource, $stream);

        if (!fclose($stream)) {
            return Result::error($this, 'Could not save local file');
        }

        $this->progressBar->finish();

        return new Result($this, 0, 'File downloaded.');
    }

    /**
     * @param int $notificationCode
     * @param int $severity
     * @param string $message
     * @param int $messageCode
     * @param int $bytesTransferred
     * @param int $bytesMax
     */
    public function progress($notificationCode, $severity, $message, $messageCode, $bytesTransferred, $bytesMax)
    {
        if (STREAM_NOTIFY_REDIRECTED === $notificationCode) {
            $this->progressBar->clear();
            $this->progressBar = null;
        }

        if (STREAM_NOTIFY_FILE_SIZE_IS === $notificationCode) {
            if ($this->progressBar) {
                $this->progressBar->clear();
            }
            $this->progressBar = new ProgressBar($this->getOutput(), $bytesMax);
            $this->progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %estimated:-6s%');
        }

        if (STREAM_NOTIFY_PROGRESS === $notificationCode) {
            if (null === $this->progressBar) {
                $this->progressBar = new ProgressBar($this->getOutput());
                $this->progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %estimated:-6s%');
            }
            $this->progressBar->setProgress($bytesTransferred);
        }

        if (STREAM_NOTIFY_COMPLETED === $notificationCode) {
            $this->progressBar->finish();
        }
    }
}
