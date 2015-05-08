<?php
namespace Robo\Task\Assets;

use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Finder\Finder;

/**
 * Minifies asset file (CSS or JS).
 *
 * ``` php
 * <?php
 * $this->taskMinify( 'web/assets/theme.css' )
 *      ->run()
 * ?>
 * ```
 * Please install additional dependencies to use:
 *
 * ```
 * "patchwork/jsqueeze": "~1.0",
 * "natxet/CssMin": "~3.0"
 * ```
 *
 */
class Minify extends BaseTask
{
    /** @var array $types */
    protected $types = ['css', 'js'];

    /** @var string $text */
    protected $text;

    protected $files = array();
    protected $overwrite = False;

    /** @var string $dst */
    protected $dst;

    /** @var string $type css|js */
    protected $type;

    /** @var array $squeezeOptions */
    protected $squeezeOptions = [
        'singleLine' => true,
        'keepImportantComments' => true,
        'specialVarRx' => false,
    ];

    /**
     * Constructor. Accepts asset file path or string source.
     *
     * @param bool|string $input
     */
    public function __construct($input)
    {
        if (file_exists($input)) 
        {
            $this->files[] = $input;
            return $this;
        }
        else
        {
            // by wildcard
            $finder = new Finder();
            $finder->exclude('dev');

            $wildcardIdx = strpos($input, '*');
            $assetType = substr($input, $wildcardIdx + 2);

            if( ! in_array( $assetType , $this->types ) ){
                throw new \Robo\Exception\TaskException($this,'Invalid file type, must be '.implode(' or ',$this->types) );
            }

            $this->type = $assetType;

            if( false !== $wildcardIdx ){
                $path = substr($input, 0,$wildcardIdx);
                if( !is_dir($path) )
                     throw new \Robo\Exception\TaskException($this,'directory '.$path.' not found.');

                $finder->name( substr($input, $wildcardIdx) );
                $iterator = $finder->in( $path );
            }else{
                throw new \Robo\Exception\TaskException($this,'file or path not found.');
            }
            
            foreach ($iterator as $file) {
                $this->files[] = $file->getRealpath();
            }

            return $this;
        }

        return $this->fromText($input);
    }

    /**
     * Sets destination. Tries to guess type from it.
     *
     * @param string $dst
     *
     * @return $this
     */
    public function to($dst)
    {
        $this->dst = $dst;

        if (!empty($this->dst) && empty($this->type)) {
            $this->type($this->getExtension($this->dst));
        }

        return $this;
    }

    /**
     * Sets type with validation.
     *
     * @param string $type css|js
     *
     * @return $this
     */
    public function type($type)
    {
        $type = strtolower($type);

        if (in_array($type, $this->types)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Sets text from string source.
     *
     * @param string $text
     *
     * @return $this
     */
    protected function fromText($text)
    {
        $this->text = (string)$text;
        unset($this->type);

        return $this;
    }

    /**
     * Sets text from asset file path. Tries to guess type and set default destination.
     *
     * @param string $path
     *
     * @return $this
     */
    protected function fromFile($path)
    {
        $this->text = file_get_contents($path);

        unset($this->type);
        $this->type($this->getExtension($path));

        if (empty($this->dst) && !empty($this->type)) {
            $ext_length = strlen($this->type) + 1;

            if( False === $this->overwrite )
                $this->dst = substr($path, 0, -$ext_length) . '.min.' . $this->type;
            else
                $this->dst = $path;
        }

        return $this;
    }

    public function overwrite()
    {
        $this->overwrite = True;
        return $this;
    }

    /**
     * Gets file extension from path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Minifies and returns text.
     *
     * @return string|bool
     */
    protected function getMinifiedText()
    {
        switch ($this->type) {

            case 'css':
                return \CssMin::minify($this->text);
                break;

            case 'js':
                if (class_exists('\JSqueeze')) {
                    $jsqueeze = new \JSqueeze();
                } else {
                    $jsqueeze = new \Patchwork\JSqueeze();
                }
                return $jsqueeze->squeeze(
                    $this->text,
                    $this->squeezeOptions['singleLine'],
                    $this->squeezeOptions['keepImportantComments'],
                    $this->squeezeOptions['specialVarRx']
                );
                break;
        }

        return false;
    }

    /**
     * Single line option for the JS minimisation.
     *
     * @return $this;
     */
    public function singleLine($singleLine)
    {
        $this->squeezeOptions['singleLine'] = (bool)$singleLine;
        return $this;
    }

    /**
     * keepImportantComments option for the JS minimisation.
     *
     * @return $this;
     */
    public function keepImportantComments($keepImportantComments)
    {
        $this->squeezeOptions['keepImportantComments'] = (bool)$keepImportantComments;
        return $this;
    }

    /**
     * specialVarRx option for the JS minimisation.
     *
     * @return $this;
     */
    public function specialVarRx($specialVarRx)
    {
        $this->squeezeOptions['specialVarRx'] = (bool)$specialVarRx;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMinifiedText();
    }

    /**
     * internal run
     *
     * @return Result
     */
    protected function _run()
    {
        if (empty($this->type)) {
            return Result::error($this, 'Unknown asset type.');
        }

        if (empty($this->dst)) {
            return Result::error($this, 'Unknown file destination.');
        }

        $size_before = strlen($this->text);
        $minified = $this->getMinifiedText();

        if (false === $minified) {
            return Result::error($this, 'Minification failed.');
        }

        $size_after = strlen($minified);
        $dst = $this->dst . '.part';
        $write_result = file_put_contents($dst, $minified);
        rename($dst, $this->dst);

        if (false === $write_result) {
            return Result::error($this, 'File write failed.');
        }

        $minified_percent = number_format(100 - ($size_after / $size_before * 100), 1);
        $this->printTaskSuccess(
            sprintf(
                'Wrote <info>%s</info> (reduced by <info>%s%%</info>)', $this->dst,
                $minified_percent
            )
        );

        return true;
    }

    /**
     * Writes minified result to destination.
     *
     * @return Result
     */
    public function run()
    {
        if( count($this->files) > 0 )
        {
            foreach ($this->files as $file) 
            {
                $this->fromFile($file);
                $result = $this->_run();
                unset($this->dst);      
            }
            return Result::success($this, 'Asset minified.');
        }
        else
        {
            if( $this->_run() )
                return Result::success($this, 'Asset minified.');
        }
    }
}