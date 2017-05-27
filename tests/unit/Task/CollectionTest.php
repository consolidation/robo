<?php
namespace unit;

// @codingStandardsIgnoreFile
// We do not want NitPick CI to report results about this file,
// as we have a couple of private test classes that appear in this file
// rather than in their own file.

use Robo\Robo;
use Robo\Result;
use Robo\State\Data;
use Robo\Task\BaseTask;
use Robo\Collection\Collection;
use Robo\Task\ValueProviderTask;
use Robo\Task\CollectionTestTask;
use Robo\Task\CountingTask;

class CollectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    public function testAfterFilters()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

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
        $collection->setLogger(Robo::logger());

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
        $collection->setLogger(Robo::logger());

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

    public function testStateWithAddCode()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

        $result = $collection
            ->addCode(
                function (Data $state) {
                    $state['one'] = 'first';
                })
            ->addCode(
                function (Data $state) {
                    $state['two'] = 'second';
                })
            ->addCode(
                function (Data $state) {
                    $state['three'] = "{$state['one']} and {$state['two']}";
                })
            ->run();

        $state = $collection->getState();
        verify($state['three'])->equals('first and second');
    }

    public function testStateWithTaskResult()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

        $first = new ValueProviderTask();
        $first->provideData('one', 'First');

        $second = new ValueProviderTask();
        $second->provideData('two', 'Second');

        $result = $collection
            ->add($first)
            ->add($second)
            ->addCode(
                function (Data $state) {
                    $state['three'] = "{$state['one']} and {$state['two']}";
                })
            ->run();

        $state = $collection->getState();
        verify($state['one'])->equals('First');
        verify($state['three'])->equals('First and Second');
    }

    public function testDeferredInitialization()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

        $first = new ValueProviderTask();
        $first->provideData('one', 'First');

        $second = new ValueProviderTask();
        $second->provideData('two', 'Second');

        $third = new ValueProviderTask();

        $result = $collection
            ->add($first)
            ->add($second)
            ->add($third)
                ->defer(
                    $third,
                    function ($task, $state) {
                        $task->provideData('three', "{$state['one']} and {$state['two']}");
                    }
                )
            ->run();

        $state = $collection->getState();
        verify($state['one'])->equals('First');
        verify($state['three'])->equals('First and Second');
    }

    public function testDeferredInitializationWithMessageStorage()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

        $first = new ValueProviderTask();
        $first->provideMessage('1st');

        $second = new ValueProviderTask();
        $second->provideData('other', '2nd');

        $third = new ValueProviderTask();

        $result = $collection
            ->add($first)
                ->storeState($first, 'one')
            ->add($second)
                ->storeState($second, 'two', 'other')
            ->add($third)
                ->defer(
                    $third,
                    function ($task, $state) {
                        $task->provideData('three', "{$state['one']} and {$state['two']}");
                    }
                )
            ->run();

        $state = $collection->getState();
        verify($state['one'])->equals('1st');
        verify($state['three'])->equals('1st and 2nd');
    }
    public function testDeferredInitializationWithChainedInitialization()
    {
        $collection = new Collection();
        $collection->setLogger(Robo::logger());

        // This task sets the Result message to '1st'
        $first = new ValueProviderTask();
        $first->provideMessage('1st');

        $second = new ValueProviderTask();
        $second->provideMessage('2nd');

        $third = new ValueProviderTask();

        $result = $collection
            // $first will set its Result's message to '1st' at `run()` time
            ->add($first)
                // This will copy the message from $first's result to $state['one'] after $first runs.
                // Note that it does not matter what order the `storeState` messages are called in;
                // their first parameter determines when they run. This differs from CollectionBuilder,
                // which manages order.
                ->storeState($first, 'one')
            ->add($second)
                // This will copy the message from $second's result to $state['two']
                ->storeState($second, 'two')
            ->add($third)
                ->deferTaskConfiguration($third, 'provideItem', 'one')
                ->deferTaskConfiguration($third, 'provideMessage', 'two')
                ->storeState($third, 'final')
            ->progressMessage('The final result is {final}')
            ->run();

        $state = $collection->getState();
        verify($state['one'])->equals('1st');
        verify($state['item'])->equals('1st');
        verify($state['final'])->equals('2nd');

        $this->guy->seeInOutput("The final result is 2nd");
    }
}

