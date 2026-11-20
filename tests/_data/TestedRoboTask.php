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
     * Untyped parameter with a boolean default
     *
     * @param bool $preserve
     *
     * @return $this
     */
    public function preserve($preserve = true)
    {
        return $this;
    }

    /**
     * Untyped parameter with an array default
     *
     * @param array $exclude
     *
     * @return $this
     */
    public function exclude($exclude = [])
    {
        return $this;
    }

    /**
     * Parameters with null and constant defaults
     *
     * @param string|null $name
     * @param int $mode
     *
     * @return $this
     */
    public function name(?string $name = null, $mode = \PHP_INT_MAX)
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
