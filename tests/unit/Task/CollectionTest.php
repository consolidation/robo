<?php
namespace unit;

// @codingStandardsIgnoreFile
// We do not want NitPick CI to report results about this file,
// as we have a couple of private test classes that appear in this file
// rather than in their own file.

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Collection\Collection;
use Robo\Robo;

class CollectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    public function testAfterFilters()
    {
        $collection = new Collection();

        $taskA = new CollectionTestTask('a', 'value-a');
        $taskB = new CollectionTestTask('b', 'value-b');

        $collection
            ->add($taskA, 'a-name')
            ->add($taskB, 'b-name');

        // We add methods of our task instances as before and
        // after tasks. These methods have access to the task
        // class' fields, and may modify them as needed.
        $collection
            ->after('a-name', [$taskA, 'parenthesizer'])
            ->after('a-name', [$taskA, 'emphasizer'])
            ->after('b-name', [$taskB, 'emphasizer'])
            ->after('b-name', [$taskB, 'parenthesizer'])
            ->after('b-name', [$taskB, 'parenthesizer'], 'special-name');

        $result = $collection->run();

        // verify(var_export($result->getData(), true))->equals('');

        // Ensure that the results have the correct key values
        verify(implode(',', array_keys($result->getData())))->equals('a-name,b-name,special-name,time');

        // Verify that all of the after tasks ran in
        // the correct order.
        verify($result['a-name']['a'])->equals('*(value-a)*');
        verify($result['b-name']['b'])->equals('(*value-b*)');

        // Note that the last after task is added with a special name;
        // its results therefore show up under the name given, rather
        // than being stored under the name of the task it was added after.
        verify($result['special-name']['b'])->equals('((*value-b*))');
    }

    public function testBeforeFilters()
    {
        $collection = new Collection();

        $taskA = new CollectionTestTask('a', 'value-a');
        $taskB = new CollectionTestTask('b', 'value-b');

        $collection
            ->add($taskA, 'a-name')
            ->add($taskB, 'b-name');

        // We add methods of our task instances as before and
        // after tasks. These methods have access to the task
        // class' fields, and may modify them as needed.
        $collection
            ->before('b-name', [$taskA, 'parenthesizer'])
            ->before('b-name', [$taskA, 'emphasizer'], 'special-before-name');

        $result = $collection->run();

        // Ensure that the results have the correct key values
        verify(implode(',', array_keys($result->getData())))->equals('a-name,b-name,special-before-name,time');

        // The result from the 'before' task is attached
        // to 'b-name', since it was called as before('b-name', ...)
        verify($result['b-name']['a'])->equals('(value-a)');
        // When a 'before' task is given its own name, then
        // its results are attached under that name.
        verify($result['special-before-name']['a'])->equals('*(value-a)*');
    }

    public function testAddCodeRollbackAndCompletion()
    {
        $collection = new Collection();
        $rollback1 = new CountingTask();
        $rollback2 = new CountingTask();
        $completion1 = new CountingTask();
        $completion2 = new CountingTask();

        $collection
            ->progressMessage("start collection tasks")
            ->rollback($rollback1)
            ->completion($completion1)
            ->rollbackCode(function() use($rollback1) { $rollback1->run(); } )
            ->completionCode(function() use($completion1) { $completion1->run(); } )
            ->addCode(function () { return 42; })
            ->progressMessage("not reached")
            ->rollback($rollback2)
            ->completion($completion2)
            ->addCode(function () { return 13; });

        $collection->setLogger($this->guy->logger());

        $result = $collection->run();
        // Execution stops on the first error.
        // Confirm that status code is converted to a Result object.
        verify($result->getExitCode())->equals(42);
        verify($rollback1->getCount())->equals(2);
        verify($rollback2->getCount())->equals(0);
        verify($completion1->getCount())->equals(2);
        verify($completion2->getCount())->equals(0);
        $this->guy->seeInOutput('start collection tasks');
        $this->guy->doNotSeeInOutput('not reached');
    }
}

class CountingTask extends BaseTask
{
    protected $count = 0;

    public function run()
    {
        $this->count++;
        return Result::success($this);
    }

    public function getCount()
    {
        return $this->count;
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
        return $this->getValue();
    }

    protected function getValue()
    {
        $result = Result::success($this);
        $result[$this->key] = $this->value;

        return $result;
    }

    // Note that by returning a value with the same
    // key as the result, we overwrite the value generated
    // by the primary task method ('run()').  If we returned
    // a result with a different key, then both values
    // would appear in the result.
    public function parenthesizer()
    {
        $this->value = "({$this->value})";
        return $this->getValue();
    }

    public function emphasizer()
    {
        $this->value = "*{$this->value}*";
        return $this->getValue();
    }
}
