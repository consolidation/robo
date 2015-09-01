<?php

namespace Robo\Task\Assets;

use Robo\Result;
use Robo\Task\BaseTask;

abstract class CssPreprocessor extends BaseTask
{
    const FORMAT_NAME = '';

    /**
     * Default compiler to use.
     *
     * @var string
     */
    protected $compiler;

    /**
     * Available compilers list
     * @var array
     */
    protected $compilers = [];

    /**
     * Compiler options.
     *
     * @var array
     */
    protected $compilerOptions = [];

    /**
     * @var array $file
     */
    protected $files = [];

    /**
     * Constructor. Accepts array of file paths.
     *
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->files = $input;
        foreach ($this->files as $in => $out) {
            if (!file_exists($in)) {
                return Result::error($this, 'File %s not found.', $in);
            }
        }

        $this->setDefaultCompiler();
    }

    protected function setDefaultCompiler()
    {
        if (isset($this->compilers[0])) {
            //set first compiler as default
            $this->compiler = $this->compilers[0];
        }
    }

    /**
     * Sets import directories
     *
     * @param array|string $dirs
     * @return $this
     */
    public function importDir($dirs)
    {
        return $this->setImportPaths($dirs);
    }

    /**
     * Adds import directory
     *
     * @param string $dir
     *
     * @return $this
     */
    public function addImportPath($dir)
    {
        if (!isset($this->compilerOptions['importDirs'])) {
            $this->compilerOptions['importDirs'] = [];
        }

        if (!in_array($dir, $this->compilerOptions['importDirs'], true)) {
            $this->compilerOptions['importDirs'][] = $dir;
        }

        return $this;
    }

    /**
     * Sets import directories
     *
     * @param array|string $dirs
     * @return $this
     */
    public function setImportPaths($dirs)
    {
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }

        $this->compilerOptions['importDirs'] = $dirs;

        return $this;
    }

    /**
     * @param string $formatterName
     *
     * @return $this
     */
    public function setFormatter($formatterName)
    {
        $this->compilerOptions['formatter'] = $formatterName;

        return $this;
    }

    /**
     * Sets the compiler.
     *
     * @param string $compiler
     * @param array $options
     * @return $this
     */
    public function compiler($compiler, array $options = [])
    {
        if (!in_array($compiler, $this->compilers) && !is_callable($compiler)) {
            $this->say(
                sprintf(
                    'Invalid ' . static::FORMAT_NAME . ' compiler %s!',
                    $compiler
                )
            );

            return false;
        }

        $this->compiler = $compiler;
        $this->compilerOptions = array_merge($this->compilerOptions, $options);

        return $this;
    }

    /**
     * Compiles file
     * @param $file
     *
     * @return bool|mixed
     */
    protected function compile($file)
    {
        if (is_callable($this->compiler)) {
            return call_user_func($this->compiler, $file, $this->compilerOptions);
        }

        if (method_exists($this, $this->compiler)) {
            return $this->{$this->compiler}($file);
        }

        return false;
    }

    /**
     * Writes the result to destination.
     *
     * @return Result
     */
    public function run()
    {
        foreach ($this->files as $in => $out) {
            $css = $this->compile($in);

            if ($css instanceof Result) {
                return $css;
            } elseif (false === $css) {
                return Result::error(
                    $this,
                    ucfirst(static::FORMAT_NAME) . ' compilation failed for %s.',
                    $in
                );
            }

            $dst = $out . '.part';
            $write_result = file_put_contents($dst, $css);
            rename($dst, $out);

            if (false === $write_result) {
                return Result::error($this, 'File write failed: %s', $out);
            }

            $this->printTaskSuccess(
                sprintf(
                    'Wrote CSS to <info>%s</info>',
                    $out
                )
            );
        }

        return Result::success($this, 'All ' . static::FORMAT_NAME . ' files compiled.');
    }
}
