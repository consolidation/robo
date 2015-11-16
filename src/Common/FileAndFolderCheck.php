<?php
namespace Robo\Common;

trait FileAndFolderCheck
{
    use IO;

    /**
     * Checks if the given input is a file or folder.
     *
     * @param string|array $resources
     * @param string $type "file", "dir", "fileAndDir"
     * @return void
     */
    protected function checkResources($resources, $type)
    {
        if (!in_array($type, ['file', 'dir', 'fileAndDir'])) {
            throw new \InvalidArgumentException(sprintf('Invalid resource check of  type "%s" used!', $type));
        }
        if (is_string($resources)) {
            $resources = [$resources];
        }
        $success = true;
        foreach ($resources as $resource) {
            switch ($type) {
                case 'file':
                    if (!$this->isFile($resource)) {
                        $this->printTaskError(sprintf('File "%s" does not exist!', $resource), $this);
                        $success = false;
                        continue;
                    }
                case 'dir':
                    if (!$this->isDir($resource)) {
                        $this->printTaskError(sprintf('Directory "%s" does not exist!', $resource), $this);
                        $success = false;
                        continue;
                    }
                case 'fileAndDir':
                    if (!$this->isDir($resource) && !$this->isFile($resource)) {
                        $this->printTaskError(sprintf('File or directory "%s" does not exist!', $resource), $this);
                        $success = false;
                        continue;
                    }
            }
        }
        return $success;
    }

    /**
     * Convenience method to check the often uses "source => target" file / folder arrays.
     *
     * @param string|array $resources
     * @return void
     */
    protected function checkSourceAndTargetResource($resources)
    {
        if (is_string($resources)) {
            $resources = [$resources];
        }
        $sources = [];
        $targets = [];
        foreach ($resources as $source => $target) {
            $sources[] = $source;
            $target[] = $target;
        }
        $this->checkResources($sources);
        $this->checkResources($targets);
    }

    protected function isDir($directory)
    {
        return is_dir($directory);
    }

    protected function isFile($file)
    {
        return file_exists($file);
    }
}
