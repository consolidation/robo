<?php
namespace Robo\Task\Assets;

use Robo\Result;

/**
 * Compiles scss files.
 *
 * ```php
 * <?php
 * $this->taskScss([
 *     'scss/default.scss' => 'css/default.css'
 * ])
 * ->importDir('assets/styles')
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
class Scss extends CssPreprocessor
{
    const FORMAT_NAME = 'scss';

    /**
     * @var string[]
     */
    protected $compilers = [
        'scssphp', // https://github.com/leafo/scssphp
    ];

    /**
     * scssphp compiler
     * @link https://github.com/leafo/scssphp
     *
     * @param string $file
     *
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
        if (isset($this->compilerOptions['importDirs'])) {
            $scss->setImportPaths($this->compilerOptions['importDirs']);
        }

        if (isset($this->compilerOptions['formatter'])) {
            $scss->setFormatter($this->compilerOptions['formatter']);
        }

        return $scss->compile($scssCode);
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
     *
     * @param string $formatterName
     *
     * @return $this
     */
    public function setFormatter($formatterName)
    {
        return parent::setFormatter($formatterName);
    }
}
