<?php
namespace Robo\Common;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Robo Log Styler.
 */
class LogStyler implements LogStyleInterface
{
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    const TASK_STYLE_INFO = 'fg=white;bg=cyan;options=bold';
    const TASK_STYLE_SUCCESS = 'fg=white;bg=green;options=bold';
    const TASK_STYLE_WARNING = 'fg=black;bg=yellow;options=bold;';
    const TASK_STYLE_ERROR = 'fg=white;bg=red;options=bold';

    /**
     * {@inheritdoc}
     */
    public function defaultStyles()
    {
        return ['*' => 'info'];
    }

    /**
     * {@inheritdoc}
     *
     * TODO: Move to a BaseLogStyler?
     */
    public function style($context)
    {
        $context += ['_style' => []];
        $context['_style'] += $this->defaultStyles();
        foreach ($context as $key => $value) {
            $styleKey = $key;
            if (!isset($context['_style'][$styleKey])) {
                $styleKey = '*';
            }
            if (is_string($value) && isset($context['_style'][$styleKey])) {
                $style = $context['_style'][$styleKey];
                $context[$key] = $this->wrapFormatString($context[$key], $style);
            }
        }
        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function text($message, $context)
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function success($message, $context)
    {
        return $this->text($this->formatMessage($message, $context, self::TASK_STYLE_SUCCESS), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, $context)
    {
        return $this->text($this->formatMessage($message, $context, self::TASK_STYLE_ERROR, self::TASK_STYLE_ERROR), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, $context)
    {
        return $this->text($this->formatMessage($message, $context, self::TASK_STYLE_WARNING), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function note($message, $context)
    {
        return $this->text($this->formatMessage($message, $context, self::TASK_STYLE_INFO), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function caution($message, $context)
    {
        return $this->warning($message, $context);
    }

    /**
     * Wrap a string in a format element.
     */
    protected function wrapFormatString($string, $style)
    {
        if ($style) {
            return "<{$style}>$string</>";
        }
        return $string;
    }

    /**
     * Apply styling for one of the style methods.
     */
    protected function formatMessage($message, $context, $taskNameStyle, $messageStyle = '')
    {
        if (!empty($messageStyle)) {
            $message = $this->wrapFormatString(" $message ", $messageStyle);
        }
        if (array_key_exists('name', $context)) {
            $message = ' ' . $this->wrapFormatString("[{$context['name']}]", $taskNameStyle) . ' ' . $message;
        }
        if (array_key_exists('time', $context) && array_key_exists('timer-label', $context)) {
            $message .= ' ' . $context['timer-label'] . ' ' . $this->wrapFormatString($context['time'], 'fg=yellow');
        }

        return $message;
    }
}
