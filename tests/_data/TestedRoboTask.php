<?php

use Robo\Result;
use Robo\Task\BaseTask;

/**
 * A test task file. Used for testig documentation generation.
 *
 * ``` php
 * <?php
 * $this->taskTestedRoboTask([
 *      'web/assets/screen.css',
 *      'web/assets/print.css',
 *      'web/assets/theme.css'
 *  ])
 *  ->to('web/assets/style.css')
 *  ->run()
 * ?>
 * ```
 */
class TestedRoboTask extends BaseTask
{
    /**
     * @var array|Iterator files
     */
    protected $files;

    /**
     * @var string dst
     */
    protected $dst;

    /**
     * Constructor. This should not be documented
     *
     * @param array|Iterator $files
     */
    public function __construct()
    {
    }

    /**
     * Set the destination file
     *
     * @param string $dst
     *
     * @return Concat The current instance
     */
    public function to($dst)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return Result::success($this);
    }
}
