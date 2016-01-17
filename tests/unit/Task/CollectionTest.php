<?php
namespace unit;

// @codingStandardsIgnoreFile
// We do not want NitPick CI to report results about this file,
// as we have a couple of private test classes that appear in this file
// rather than in their own file.

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\AfterTaskInterface;
use Robo\TaskCollection\Collection;

class CollectionTest extends \Codeception\TestCase\Test
{
    public function testBeforeAndAfterFilters()
    {
        $collection = new Collection();

        $taskA = new CollectionTestTask('a', 'value-a');
        $taskB = new CollectionTestTask('b', 'value-b');

        $parenthesizerA = new CollectionTestFilterTask('a', '(', ')');
        $parenthesizerB = new CollectionTestFilterTask('b', '{', '}');

        $emphasizerA = new CollectionTestFilterTask('a', '*', '*');
        $emphasizerB = new CollectionTestFilterTask('b', '__', '__');

        $collection
            ->add('a-name', $taskA)
            ->add('b-name', $taskB);

        $taskKeys = $collection->taskNames();
        verify(implode(',', $taskKeys))->equals('a-name,b-name');

        $collection
            ->after('a-name', $parenthesizerA)
            ->before('b-name', $emphasizerA)
            ->after('b-name', $emphasizerB)
            ->after('b-name', $parenthesizerB);

        $result = $collection->run();

        // Verify that all of the after tasks ran in
        // the correct order.
        verify($result['a'])->equals('*(value-a)*');
        verify($result['b'])->equals('{__value-b__}');
    }
}

class CollectionTestTask extends BaseTask
{
    protected $key;
    protected $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function run()
    {
        $result = Result::success($this);
        $result[$this->key] = $this->value;

        return $result;
    }
}

class CollectionTestFilterTask implements AfterTaskInterface
{
    protected $key;
    protected $pre;
    protected $post;

    public function __construct($key, $pre, $post)
    {
        $this->key = $key;
        $this->pre = $pre;
        $this->post = $post;
    }

    public function run(Result $incrementalResult)
    {
        $value = isset($incrementalResult[$this->key]) ? $incrementalResult[$this->key] : "";
        $incrementalResult[$this->key] = "{$this->pre}{$value}{$this->post}";
        return $incrementalResult;
    }
}
