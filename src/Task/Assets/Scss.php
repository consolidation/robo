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
 * Use one of the scss compilers in your project:
 *
 * ```
 * "scssphp/scssphp": "^2.1",
 * "bugo/scss-php": "^0.8",
 * "bugo/sass-embedded-php": "^0.9"
 * ```
 *
 * Specify directory (string or array) for scss imports lookup:
 *
 * ```php
 * <?php
 * $this->taskScss([
 *     'scss/default.scss' => 'css/default.css'
 * ])
 * ->importDir('scss')
 * ->compiler('scss-php')
 * ->run();
 * ?>
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
        'scssphp', // https://github.com/scssphp/scssphp
        'scss', // https://github.com/dragomano/scss-php
        'sass-embedded', // https://github.com/dragomano/sass-embedded-php
    ];

    /**
     * scssphp compiler
     * @link https://github.com/scssphp/scssphp
     *
     * @param string $file
     *
     * @return string
     */
    protected function scssphp($file)
    {
        if (!class_exists('\ScssPhp\ScssPhp\Compiler')) {
            return Result::errorMissingPackage($this, 'scssphp', 'scssphp/scssphp');
        }

        $scss = new \ScssPhp\ScssPhp\Compiler();

        if (isset($this->compilerOptions['importDirs'])) {
            $scss->setImportPaths($this->compilerOptions['importDirs']);
        }

        if (isset($this->compilerOptions['formatter'])) {
            $scss->setOutputStyle($this->normalizeScssPhpOutputStyle($this->compilerOptions['formatter']));
        }

        return $scss->compileFile($file)->getCss();
    }

    /**
     * bugo/scss-php compiler
     * @link https://github.com/dragomano/scss-php
     *
     * @param string $file
     *
     * @return string|\Robo\Result
     */
    protected function scss($file)
    {
        if (!class_exists('\Bugo\SCSS\Compiler')) {
            return Result::errorMissingPackage($this, 'Bugo\\SCSS\\Compiler', 'bugo/scss-php');
        }

        $loader = isset($this->compilerOptions['importDirs'])
            ? new \Bugo\SCSS\Loader($this->compilerOptions['importDirs'])
            : new \Bugo\SCSS\Loader();

        $compiler = new \Bugo\SCSS\Compiler(
            new \Bugo\SCSS\CompilerOptions(
                style: $this->normalizeBugoOutputStyle($this->compilerOptions['formatter'] ?? null)
            ),
            $loader
        );

        return $compiler->compileFile($file);
    }

    /**
     * bugo/sass-embedded-php compiler (native Dart Sass bridge)
     * @link https://github.com/dragomano/sass-embedded-php
     *
     * @param string $file
     *
     * @return string|\Robo\Result
     */
    protected function sassEmbedded($file)
    {
        if (!class_exists('\Bugo\Sass\Compiler')) {
            return Result::errorMissingPackage($this, 'Bugo\\Sass\\Compiler', 'bugo/sass-embedded-php');
        }

        $compiler = new \Bugo\Sass\Compiler();

        return $compiler->compileFile($file, new \Bugo\Sass\Options(
            loadPaths: $this->compilerOptions['importDirs'] ?? null,
            style: $this->normalizeFormatter($this->compilerOptions['formatter'] ?? null),
        ));
    }

    /**
     * @param string|null $formatter
     *
     * @return \ScssPhp\ScssPhp\OutputStyle
     */
    protected function normalizeScssPhpOutputStyle($formatter)
    {
        return \ScssPhp\ScssPhp\OutputStyle::fromString($this->normalizeFormatter($formatter));
    }

    /**
     * @param string|null $formatter
     *
     * @return \Bugo\SCSS\Style
     */
    protected function normalizeBugoOutputStyle($formatter)
    {
        return $this->normalizeFormatter($formatter) === 'compressed'
            ? \Bugo\SCSS\Style::COMPRESSED
            : \Bugo\SCSS\Style::EXPANDED;
    }

    /**
     * Maps legacy formatter names from scssphp 1.x to the output styles
     * supported by the modern compilers.
     *
     * @param string|null $formatter
     *
     * @return string
     */
    protected function normalizeFormatter($formatter)
    {
        $formatter = ltrim((string) $formatter, '\\');

        $formatters = [
            '' => 'expanded',
            'expanded' => 'expanded',
            'compressed' => 'compressed',
            'ScssPhp\ScssPhp\Formatter\Expanded' => 'expanded',
            'ScssPhp\ScssPhp\Formatter\Nested' => 'expanded',
            'ScssPhp\ScssPhp\Formatter\Compact' => 'expanded',
            'ScssPhp\ScssPhp\Formatter\Compressed' => 'compressed',
            'ScssPhp\ScssPhp\Formatter\Crunched' => 'compressed',
        ];

        if (!isset($formatters[$formatter])) {
            throw new \InvalidArgumentException(sprintf('Invalid scss formatter %s!', $formatter));
        }

        return $formatters[$formatter];
    }

    /**
     * Resolves the compiler method name. The "sass-embedded" compiler key is
     * not a valid PHP method name, so it is mapped to the actual method.
     *
     * @param string $file
     *
     * @return bool|mixed|\Robo\Result
     */
    protected function compile($file)
    {
        $method = $this->compiler === 'sass-embedded' ? 'sassEmbedded' : $this->compiler;

        if (is_callable($method)) {
            return call_user_func($method, $file, $this->compilerOptions);
        }

        if (method_exists($this, $method)) {
            return $this->{$method}($file);
        }

        return false;
    }

    /**
     * Sets the formatter for scss compilers.
     *
     * `scssphp/scssphp` 2.x, `bugo/scss-php` and `bugo/sass-embedded-php` support
     * `expanded` and `compressed` output styles. Legacy formatter class names from
     * scssphp 1.x are also accepted and mapped to the closest supported output style.
     *
     * @link https://scssphp.github.io/scssphp/docs/#output-formatting
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
