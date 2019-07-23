<?php
namespace Robo\Common;

use Robo\Symfony\IOStorage;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait IO
{
    use InputAwareTrait {
        input as parentInput;
    }
    use OutputAwareTrait {
        output as parentOutput;
    }

    /**
     * @var Robo\Symfony\IOStorage
     */
    protected $ioStorage;

    public function setIOStorage(IOStorage $ioStorage)
    {
        $this->ioStorage = $ioStorage;
    }

    public function resetIO(InputInterface $input, OutputInterface $output)
    {
        if (!$this->ioStorage) {
            $this->ioStorage = new IOStorage();
        }
        $this->ioStorage->create($input, $output);
    }

    protected function output()
    {
        $result = null;
        if ($this->ioStorage) {
            $result = $this->ioStorage->output();
        }
        return $result ?: $this->parentOutput();
    }

    protected function input()
    {
        $result = null;
        if ($this->ioStorage) {
            $result = $this->ioStorage->input();
        }
        return $result ?: $this->parentInput();
    }

    /**
     * Provide access to SymfonyStyle object.
     *
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     *
     * @see http://symfony.com/blog/new-in-symfony-2-8-console-style-guide
     */
    protected function io()
    {
        if (!$this->ioStorage) {
            $this->ioStorage = new IOStorage();
        }
        return $this->ioStorage->get($this->input, $this->output);
    }

    /**
     * @param string $nonDecorated
     * @param string $decorated
     *
     * @return string
     */
    protected function decorationCharacter($nonDecorated, $decorated)
    {
        if (!$this->output()->isDecorated() || (strncasecmp(PHP_OS, 'WIN', 3) == 0)) {
            return $nonDecorated;
        }
        return $decorated;
    }

    /**
     * @param string $text
     */
    protected function say($text)
    {
        $char = $this->decorationCharacter('>', '➜');
        $this->writeln("$char  $text");
    }

    /**
     * @param string $text
     * @param int $length
     * @param string $color
     */
    protected function yell($text, $length = 40, $color = 'green')
    {
        $char = $this->decorationCharacter(' ', '➜');
        $format = "$char  <fg=white;bg=$color;options=bold>%s</fg=white;bg=$color;options=bold>";
        $this->formattedOutput($text, $length, $format);
    }

    /**
     * @param string $text
     * @param int $length
     * @param string $format
     */
    protected function formattedOutput($text, $length, $format)
    {
        $lines = explode("\n", trim($text, "\n"));
        $maxLineLength = array_reduce(array_map('strlen', $lines), 'max');
        $length = max($length, $maxLineLength);
        $len = $length + 2;
        $space = str_repeat(' ', $len);
        $this->writeln(sprintf($format, $space));
        foreach ($lines as $line) {
            $line = str_pad($line, $length, ' ', STR_PAD_BOTH);
            $this->writeln(sprintf($format, " $line "));
        }
        $this->writeln(sprintf($format, $space));
    }

    /**
     * @param string $question
     * @param bool $hideAnswer
     *
     * @return string
     */
    protected function ask($question, $hideAnswer = false)
    {
        if ($hideAnswer) {
            return $this->askHidden($question);
        }
        return $this->doAsk(new Question($this->formatQuestion($question)));
    }

    /**
     * @param string $question
     *
     * @return string
     */
    protected function askHidden($question)
    {
        $question = new Question($this->formatQuestion($question));
        $question->setHidden(true);
        return $this->doAsk($question);
    }

    /**
     * @param string $question
     * @param string $default
     *
     * @return string
     */
    protected function askDefault($question, $default)
    {
        return $this->doAsk(new Question($this->formatQuestion("$question [$default]"), $default));
    }

    /**
     * @param string $question
     * @param bool $default
     *
     * @return string
     */
    protected function confirm($question, $default = false)
    {
        return $this->doAsk(new ConfirmationQuestion($this->formatQuestion($question . ' (y/n)'), $default));
    }

    /**
     * @param \Symfony\Component\Console\Question\Question $question
     *
     * @return string
     */
    protected function doAsk(Question $question)
    {
        return $this->getDialog()->ask($this->input(), $this->output(), $question);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function formatQuestion($message)
    {
        return  "<question>?  $message</question> ";
    }

    /**
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected function getDialog()
    {
        return new QuestionHelper();
    }

    /**
     * @param $text
     */
    protected function writeln($text)
    {
        $this->output()->writeln($text);
    }
}
