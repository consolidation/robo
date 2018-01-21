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
     * @return string
     */
    public function getRelativeNamespacePath()
    {
        return str_replace("\\", '/', $this->getRelativeNamespace());
    }

    /**
     * @inheritDoc
     */
    public function getClasses()
    {
        $classes = [];

        foreach ($this->getClassLoader()->getPrefixesPsr4() as $baseNamespace => $directories) {
            $path = $this->getRelativeNamespacePath();

            $directories = array_map(function ($directory) use ($path) {
                return $directory.$path;
            }, $directories);

            $directories = array_filter($directories, function ($path) {
                return is_dir($path);
            });

            foreach ($this->search($directories, $this->searchPattern) as $file) {
                $relativePathName = $file->getRelativePathname();
                $classes[] = $baseNamespace.str_replace(['/', '.php'], ['\\', ''], $relativePathName);
            }

            return $classes;
        }
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
}
