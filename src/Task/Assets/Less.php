<?php
namespace Robo\Task\Assets;

use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Compiles less files.
 *
 * ```php
 * <?php
 * $this->taskLess([
 *     'less/default.less' => 'css/default.css'
 * ])
 * ->run();
 * ?>
 * ```
 *
 * Use one of both less compilers in your project:
 *
 * ```
 * "leafo/lessphp": "~0.5",
 * "oyejorge/less.php": "~1.5"
 * ```
 *
 * You can implement additional compilers by extending this task and adding a
 * method named after them and overloading the lessCompilers() method to
 * inject the name there.
 */
class Less extends BaseTask
{
    /**
     * The less compiler to use.
     *
     * @var string
     */
    protected $lessCompiler = 'less';

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
     * Constructor. Accepts array of less file paths.
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
     * Returns a list of supported less compilers.
     *
     * Overload this method and call parent to merge additional compilers in.
     *
     * List of supported less compilers.
     *
     * @link https://github.com/leafo/lessphp
     * @link https://github.com/oyejorge/less.php,
     * @return array
     */
    protected function lessCompilers()
    {
        return [
            'lessphp', //https://github.com/leafo/lessphp
            'less', // https://github.com/oyejorge/less.php,
        ];
    }

    /**
     * Sets the less compiler.
     *
     * For additional infos see the links in this doc block.
     *
     * @param string $compiler
     * @param array $options
     * @return $this
     */
    public function compiler($compiler, array $options = [])
    {
        if (!in_array($compiler, $this->lessCompilers()) && !is_callable($compiler)) {
            $this->say(sprintf('Invalid less compiler %s!', $compiler));
            return false;
        }
        $this->lessCompiler = $compiler;
        $this->compilerOptions = $options;
        return $this;
    }

    /**
     * Compiles the less files.
     *
     * @return string|bool
     */
    protected function compile($file)
    {
        if (is_callable($this->lessCompiler)) {
            return $this->lessCompiler($file, $this->compilerOptions);
        }
        if (method_exists($this, $this->lessCompiler)) {
            return $this->{$this->lessCompiler}($file);
        }
        return false;
    }

    /**
     * lessphp compiler
     *
     * @link https://github.com/leafo/lessphp
     * @return string
     */
    protected function lessphp($file)
    {
        $lessCode = file_get_contents($file);
        $less = new \lessc();
        return $less->compile($lessCode);
    }

    /**
     * less compiler
     *
     * @link https://github.com/oyejorge/less.php
     * @return string
     */
    protected function less($file)
    {
        $lessCode = file_get_contents($file);
        $parser = new \Less_Parser($this->compilerOptions);
        $parser->parse($lessCode);
        return $parser->getCss();
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

            if (false === $css) {
                return \Result::error($this, 'Less compilation failed for %s.', $in);
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
        return Result::success($this, 'All less files compiled.');
    }
}
