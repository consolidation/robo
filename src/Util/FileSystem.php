<?php
namespace Robo\Util;

use Symfony\Component\Filesystem\Filesystem as SfFilesystem;
use Robo\Task\Shared\TaskException;

/**
 * @author tiger
 */
class FileSystem extends SfFilesystem
{
    public function doEmptyDir($path)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if ($path->isDir()) {
                $dir = (string) $path;
                if (basename($dir) === '.' || basename($dir) === '..') {
                    continue;
                }
                $this->remove($dir);
            } else {
                $file = (string)$path;
                if (basename($file) === '.gitignore') {
                    continue;
                }
                if (basename($file) === '.gitkeep') {
                    continue;
                }

                $this->remove($path->__toString());
            }
        }
    }

    public function deleteDir($dir)
    {
        $this->remove($dir);
    }

    public function copyDir($src, $dst)
    {
        $dir = @opendir($src);
        if (false === $dir) {
            throw new TaskException(__CLASS__, "Cannot open source directory '" . $src . "'");
        }
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::copyDir($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}

