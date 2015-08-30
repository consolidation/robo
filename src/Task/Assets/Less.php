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
 * Specify directory (string or array) for less imports lookup:
 * ```php
 * <?php
 * $this->taskLess([
 *     'less/default.less' => 'css/default.css'
 * ])
 * ->importDir('less')
 * ->compiler('lessphp')
 * ->run();
 * ?>
 * ````
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
        $this->compilerOptions = array_merge($this->compilerOptions, $options);
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
            return call_user_func($this->lessCompiler, $file, $this->compilerOptions);
        }
        if (method_exists($this, $this->lessCompiler)) {
            return $this->{$this->lessCompiler}($file);
        }
        return false;
    }

    /**
     * Sets import dir option for less compilers
     * @param string|array $dirs
     *
     * @return Less
     */
    public function importDir($dirs)
    {
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }

        //this one is for lessphp compiler
        $this->compilerOptions['importDir'] = $dirs;

        //and this is for Less_Parser
        $importDirs = [];
        foreach ($dirs as $dir) {
            $importDirs[$dir] = $dir;
        }
        $this->compilerOptions['import_dirs'] = $importDirs;

        return $this;
    }

    /**
     * lessphp compiler
     *
     * @link https://github.com/leafo/lessphp
     * @return string
     */
    protected function lessphp($file)
    {
        if (!class_exists('\lessc')) {
            return Result::errorMissingPackage($this, 'lessc', 'leafo/lessphp');
        }

        $lessCode = file_get_contents($file);

        $less = new \lessc();
        if (isset($this->compilerOptions['importDir'])) {
            $less->setImportDir($this->compilerOptions['importDir']);
        }

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
        if (!class_exists('\Less_Parser')) {
            return Result::errorMissingPackage($this, 'Less_Parser', 'oyejorge/less.php');
        }

        $lessCode = file_get_contents($file);

        $parser = new \Less_Parser();
        $parser->SetOptions($this->compilerOptions);
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

            if ($css instanceof Result) {
                return $css;
            } elseif (false === $css) {
                return Result::error($this, 'Less compilation failed for %s.', $in);
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
