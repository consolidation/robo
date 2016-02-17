<?php
namespace Robo\Common;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Custom Log Styler. Styles may be overridden via constructor parameters.
 */
class CustomLogStyle implements LogStyleInterface
{
    const TASK_STYLE_INFO = 'fg=white;bg=cyan;options=bold';
    const TASK_STYLE_SUCCESS = 'fg=white;bg=green;options=bold';
    const TASK_STYLE_WARNING = 'fg=black;bg=yellow;options=bold;';
    const TASK_STYLE_ERROR = 'fg=white;bg=red;options=bold';

    protected $output;
    protected $defaultStyles = [
        '*' => 'info',
    ];
    protected $labelStyles = [
        'error' => self::TASK_STYLE_ERROR,
        'warning' => self::TASK_STYLE_WARNING,
        'caution' => self::TASK_STYLE_WARNING,
        'note' => self::TASK_STYLE_INFO,
        'success' => self::TASK_STYLE_SUCCESS,
    ];
    protected $messageStyles = [
        'error' => self::TASK_STYLE_ERROR,
        'warning' => '',
        'caution' => '',
        'note' => '',
        'success' => '',
    ];

    public function __construct(OutputInterface $output, $labelStyles = [], $messageStyles = [])
    {
        $this->output = $output;
        $this->labelStyles = $labelStyles + $this->labelStyles;
        $this->messageStyles = $messageStyles + $this->messageStyles;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultStyles()
    {
        return $this->defaultStyles;
    }

    /**
     * {@inheritdoc}
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
        return $this->text($this->formatLevel(__FUNCTION__, $message, $context), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, $context)
    {
        return $this->text($this->formatLevel(__FUNCTION__, $message, $context), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, $context)
    {
        return $this->text($this->formatLevel(__FUNCTION__, $message, $context), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function note($message, $context)
    {
        return $this->text($this->formatLevel(__FUNCTION__, $message, $context), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function caution($message, $context)
    {
        return $this->text($this->formatLevel(__FUNCTION__, $message, $context), $context);
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
     * Look up the label and message styles for the specified log level,
     * and use the log level as the label for the log message.
     */
    protected function formatLevel($level, $message, $context)
    {
        $label = $level;
        return $this->formatMessage($label, $message, $context, $this->labelStyles[$level], $this->messageStyles[$level]);
    }

    /**
     * Apply styling for one of the style methods.
     */
    protected function formatMessage($label, $message, $context, $labelStyle, $messageStyle = '')
    {
        if (!empty($messageStyle)) {
            $message = $this->wrapFormatString(" $message ", $messageStyle);
        }
        if (!empty($label)) {
            $message = ' ' . $this->wrapFormatString("[$label]", $labelStyle) . ' ' . $message;
        }

        return $message;
    }
}
