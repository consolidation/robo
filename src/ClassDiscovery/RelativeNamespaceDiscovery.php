<?php

namespace Robo\ClassDiscovery;

use Robo\Common\ClassLoaderAwareTrait;
use Robo\Contract\ClassLoaderAwareInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class RelativeNamespaceDiscovery
 *
 * @package Robo\Plugin\ClassDiscovery
 */
class RelativeNamespaceDiscovery extends AbstractClassDiscovery implements ClassLoaderAwareInterface
{
    use ClassLoaderAwareTrait;

    /**
     * @var string
     */
    protected $relativeNamespace;

    /**
     * Psr4Discovery constructor.
     *
     * @param $relativeNamespace
     */
    public function __construct($relativeNamespace)
    {
        $this->relativeNamespace = $relativeNamespace;
    }

    /**
     * @return string
     */
    public function getRelativeNamespace()
    {
        return $this->relativeNamespace;
    }

    /**
     * @inheritDoc
     */
    public function getClasses()
    {
        $classes = [];
        $relativePath = $this->convertNamespaceToPath($this->relativeNamespace);

        foreach ($this->getClassLoader()->getPrefixesPsr4() as $baseNamespace => $directories) {
            $directories = array_map(function ($directory) use ($relativePath) {
                return $directory.$relativePath;
            }, $directories);

            $directories = array_filter($directories, function ($path) {
                return is_dir($path);
            });

            foreach ($this->search($directories, $this->searchPattern) as $file) {
                $relativePathName = $file->getRelativePathname();
                $classes[] = $baseNamespace.$this->convertPathToNamespace($relativePath.DIRECTORY_SEPARATOR.$relativePathName);
            }
        }

        return $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile($class)
    {
        return $this->getClassLoader()->findFile($class);
    }

    /**
     * @param $directories
     * @param $pattern
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function search($directories, $pattern)
    {
        $finder = new Finder();
        $finder->files()
          ->name($pattern)
          ->in($directories);

        return $finder;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function convertPathToNamespace($path)
    {
        return str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], trim($path, DIRECTORY_SEPARATOR));
    }

    /**
     * @return string
     */
    public function convertNamespaceToPath($namespace)
    {
        return DIRECTORY_SEPARATOR.str_replace("\\", DIRECTORY_SEPARATOR, trim($namespace, '\\'));
    }
}
