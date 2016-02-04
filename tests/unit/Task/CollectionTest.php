<?php
namespace unit;

// @codingStandardsIgnoreFile
// We do not want NitPick CI to report results about this file,
// as we have a couple of private test classes that appear in this file
// rather than in their own file.

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Contract\CollectedInterface;
use Robo\Collection\Collection;

class CollectionTest extends \Codeception\TestCase\Test
{
    public function testBeforeAndAfterFilters()
    {
        $collection = new Collection();

        $taskA = new CollectionTestTask('a', 'value-a');
        $taskB = new CollectionTestTask('b', 'value-b');

        $parenthesizerA = new CollectionTestFilterTask($collection, 'a-name', 'a', '(', ')');
        $parenthesizerB = new CollectionTestFilterTask($collection, 'b-name', 'b', '{', '}');

        $emphasizerA = new CollectionTestFilterTask($collection, 'a-name', 'a', '*', '*');
        $emphasizerB = new CollectionTestFilterTask($collection, 'b-name', 'b', '__', '__');

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
        verify($result['a-name']['a'])->equals('*(value-a)*');
        verify($result['b-name']['b'])->equals('{__value-b__}');
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

class CollectionTestFilterTask implements TaskInterface
{
    protected $collection;
    protected $fromname;
    protected $key;
    protected $pre;
    protected $post;

    public function __construct($collection, $fromname, $key, $pre, $post)
    {
        $this->collection = $collection;
        $this->fromname = $fromname;
        $this->key = $key;
        $this->pre = $pre;
        $this->post = $post;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function run()
    {
        $incrementalResult = $this->getCollection()->getIncrementalResults();
        $value = isset($incrementalResult[$this->fromname][$this->key]) ? $incrementalResult[$this->fromname][$this->key] : "";
        $incrementalResult[$this->fromname][$this->key] = "{$this->pre}{$value}{$this->post}";
        return $incrementalResult;
    }
}
