<?php
namespace Robo\Task\Assets;

use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Compiles scss files.
 *
 * ```php
 * <?php
 * $this->taskScss([
 *     'scss/default.scss' => 'css/default.css'
 * ])
 * ->run();
 * ?>
 * ```
 *
 * Use the following scss compiler in your project:
 *
 * ```
 * "leafo/scssphp": "~0.1",
 * ```
 *
 * You can implement additional compilers by extending this task and adding a
 * method named after them and overloading the scssCompilers() method to
 * inject the name there.
 */
class Scss extends BaseTask
{
    /**
     * The scss compiler to use.
     *
     * @var string
     */
    protected $scssCompiler = 'scssphp';

    /**
     * Compiler options.
     *
     * @var array
     */
    protected $compilerOptions = [];

    /** @var array $file */
    protected $files = [];

    /** @var string $dst */
    protected $dst;

    /**
     * Constructor. Accepts array of scss file paths.
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
    }


    /**
     * Returns a list of supported scss compilers.
     *
     * Overload this method and call parent to merge additional compilers in.
     *
     * List of supported scss compilers.
     *
     * @link https://github.com/leafo/scssphp
     * @return array
     */
    protected function scssCompilers()
    {
        return [
            'scssphp', // https://github.com/leafo/scssphp
        ];
    }

    /**
     * Sets the scss compiler.
     *
     * For additional infos see the links in this doc block.
     *
     * @param string $compiler
     * @param array $options
     * @return $this
     */
    public function compiler($compiler, array $options = [])
    {
        if (!in_array($compiler, $this->scssCompilers()) && !is_callable($compiler)) {
            $this->say(sprintf('Invalid scss compiler %s!', $compiler));
            return false;
        }
        $this->scssCompiler = $compiler;
        $this->compilerOptions = $options;
        return $this;
    }

    /**
     * Compiles the scss files.
     *
     * @return string|bool
     */
    protected function compile($file)
    {
        if (is_callable($this->scssCompiler)) {
            return $this->scssCompiler($file, $this->compilerOptions);
        }
        if (method_exists($this, $this->scssCompiler)) {
            return $this->{$this->scssCompiler}($file);
        }
        return false;
    }

    /**
     * scssphp compiler
     *
     * @link https://github.com/leafo/scssphp
     * @return string
     */
    protected function scssphp($file)
    {
        if (!class_exists('\Leafo\ScssPhp\Compiler')) {
            return Result::errorMissingPackage($this, 'scssphp', 'leafo/scssphp');
        }

        $scssCode = file_get_contents($file);
        $scss = new \Leafo\ScssPhp\Compiler();
        // set options for the scssphp compiler
        if (isset($this->compilerOptions['importPaths'])) {
            $scss->setImportPaths($this->compilerOptions['importPaths']);
        }
        if (isset($this->compilerOptions['formatter'])) {
            $scss->setFormatter($this->compilerOptions['formatter']);
        }
        return $scss->compile($scssCode);
    }

    /**
     * Adds path to the importPath for scssphp
     *
     * @link http://leafo.github.io/scssphp/docs/#import-paths
     * @param string $path
     * @return $this
     */
    public function addImportPath($path)
    {
        if (! isset($this->compilerOptions['importPaths']) || ! in_array($path, $this->compilerOptions['importPaths'])) {
            $this->compilerOptions['importPaths'][] = $path;
        }
        return $this;
    }

    /**
     * Sets the importPath for scssphp
     *
     * @param array $paths
     * @return $this
     */
    public function setImportPaths($paths)
    {
        $this->compilerOptions['importPaths'] = (array)$path;
        return $this;
    }

    /**
     * Sets the formatter for scssphp
     *
     * The method setFormatter($formatterName) sets the current formatter to $formatterName,
     * the name of a class as a string that implements the formatting interface. See the source
     * for Leafo\ScssPhp\Formatter\Expanded for an example.
     * 
     * Five formatters are included with leafo/scssphp:
     * - Leafo\ScssPhp\Formatter\Expanded
     * - Leafo\ScssPhp\Formatter\Nested (default)
     * - Leafo\ScssPhp\Formatter\Compressed
     * - Leafo\ScssPhp\Formatter\Compact
     * - Leafo\ScssPhp\Formatter\Crunched
     *
     * @link http://leafo.github.io/scssphp/docs/#output-formatting
     * @param string $formatterName
     * @return $this
     */
    public function setFormatter($formatterName)
    {
        $this->compilerOptions['formatter'] = $formatterName;
        return $this;
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
                return Result::error($this, 'Scss compilation failed for %s.', $in);
            }

            $dst = $out . '.part';
            $write_result = file_put_contents($dst, $css);
            rename($dst, $out);

            if (false === $write_result) {
                return Result::error($this, 'File write failed: %s', $out);
            }
            $this->printTaskSuccess(
                sprintf(
                    'Wrote CSS to <info>%s</info>', $out
                )
            );
        }
        return Result::success($this, 'All scss files compiled.');
    }
}
