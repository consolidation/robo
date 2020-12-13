<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Collection\CollectionBuilder;
use Robo\State\Data;
use Robo\Traits\TestTasksTrait;

class ForEachTest extends TestCase
{
    use TestTasksTrait;
    use Collection\Tasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
    }

    public function tearDown(): void
    {
        $this->fixtures->cleanup();
    }

    /**
     * @return array
     */
    public function examples()
    {
        return [
            'without items' => [
                'expected' => [],
                'items' => [],
            ],
            'with items' => [
                'expected' => [
                    'a = b',
                    'c = d',
                    'e = f',
                ],
                'items' => [
                    'a' => 'b',
                    'c' => 'd',
                    'e' => 'f',
                ],
            ],
        ];
    }

    /**
     * @dataProvider examples
     */
    public function testSetIterableInConstructor($expected, $items)
    {
        $actual = [];

        // set iterable in the __constructor
        $result = $this
            ->taskForEach($items)
            ->withBuilder(function (CollectionBuilder $builder, $key, $value) use (&$actual) {
                $builder->addCode(function () use ($key, $value, &$actual) {
                    $actual[] = "$key = $value";

                    return 0;
                });
            })
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider examples
     */
    public function testSetIterableWithDeferTaskConfiguration($expected, $items)
    {
        $actual = [];

        // set iterable with deferTaskConfiguration()
        $result = $this
            ->collectionBuilder()
            ->addCode(function (Data $data) use ($items) {
                $data['items'] = $items;

                return 0;
            })
            ->addTask(
                $this
                    ->taskForEach()
                    ->deferTaskConfiguration('setIterable', 'items')
                    ->withBuilder(function (CollectionBuilder $builder, $key, $value) use (&$actual) {
                        $builder->addCode(function () use ($key, $value, &$actual) {
                            $actual[] = "$key = $value";

                            return 0;
                        });
                    })
            )
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertEquals($expected, $actual);
    }

    public function testUninitializedIterable()
    {
        $actual = 0;
        // call the __constructor() without argument
        $result = $this
            ->taskForEach()
            ->withBuilder(function (CollectionBuilder $builder, $key, $value) use (&$actual) {
                $builder->addCode(function () use ($key, $value, &$actual) {
                    $actual++;

                    return 0;
                });
            })
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertEquals(0, $actual);
    }
}
