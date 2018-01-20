<?php

namespace Robo\Plugin\Discovery;

use Robo\Common\ClassLoaderAwareTrait;
use Robo\Contract\ClassLoaderAwareInterface;
use Robo\Plugin\Definition\PluginDefinition;
use Symfony\Component\Finder\Finder;

/**
 * Discover plugins given their PSR-4 relative namespace.
 *
 * @package Robo\Plugin\Discovery
 */
class Psr4Discovery extends AbstractDiscovery implements ClassLoaderAwareInterface
{
    use ClassLoaderAwareTrait;

    /**
     * @var string
     */
    protected $relativeNamespace;

    /**
     * @var string
     */
    protected $searchPattern = '*.php';

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
     * @return string
     */
    public function getSearchPattern()
    {
        return $this->searchPattern;
    }

    /**
     * @param string $searchPattern
     */
    public function setSearchPattern($searchPattern)
    {
        $this->searchPattern = $searchPattern;
    }

    /**
     * @inheritDoc
     */
    public function getDefinitions()
    {
        foreach ($this->getClassLoader()->getPrefixesPsr4() as $baseNamespace => $directoryList) {
            $path = $this->getRelativeNamespacePath();
            $directoryList = array_filter($directoryList, function ($basePath) use ($path) {
                return is_dir($basePath.$path);
            });

            foreach ($this->search($directoryList) as $file) {
                $relativePathName = $file->getRelativePathname();
                $className = $baseNamespace.str_replace(['/', '.php'], ['\\', ''], $relativePathName);
                $definition = new PluginDefinition($className, $className, $file->getPath());
                $this->addDefinition($definition);
            }

            return $this->definitions;
        }
    }

    /**
     * @param $dirs
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function search($dirs)
    {
        $finder = new Finder();
        $finder->files()
          ->name($this->searchPattern)
          ->in($dirs);

        return $finder;
    }
}
