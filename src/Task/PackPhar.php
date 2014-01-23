<?php
namespace Robo\Task;
use Robo\TaskInterface;

class PackPhar implements TaskInterface {

    /**
     * @var \Phar
     */
    protected $phar;
    protected $compileDir = null;
    protected $filename;
    protected $compress = false;
    protected $stub;
    protected $bin;

    protected $files = [];

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->phar = new \Phar($filename, 0);
    }

    /**
     * @param null $compileDir
     * @return $this
     */
    public function saveTo($compileDir)
    {
        $this->compileDir = $compileDir;
        return $this;
    }

    /**
     * @param boolean $compress
     * @return $this
     */
    public function compressFile($compress = true)
    {
        $this->compress = $compress;
        return $this;
    }

    public function stub($stub)
    {
        $this->phar->setStub(file_get_contents($stub));
        return $this;
    }

    public function bin($executable)
    {
        $this->bin = $executable;
        return $this;
    }

    public function run()
    {
        $this->phar->setSignatureAlgorithm(\Phar::SHA1);
        $this->phar->startBuffering();

        foreach ($this->files as $path => $content) {
            $this->phar->addFromString($path, $content);
        }
        $this->phar->stopBuffering();

        if($this->compress and in_array('GZ', \Phar::getSupportedCompression())) {
            //do not use compressFiles as it has issue with temporary file when adding large amount of files
//            $phar->compressFiles(\Phar::GZ);
            $this->phar->compressFiles(\Phar::GZ);
        } else {
            $this->phar->compress(\Phar::NONE);
        }
        unset($this->phar);
    }


    public function addStripped($path, $file)
    {
        $this->files[$path] = $this->stripWhitespace(file_get_contents($file));
    }

    public function addFile($path, $file)
    {
        $this->files[$path] = file_get_contents($file);
    }

    /**
     * Strips whitespace from source. Taken from composer
     * @param $source
     * @return string
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                // $output .= $token[1];
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}

 