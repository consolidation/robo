<?php

use Codeception\Example;
use Robo\Collection\CollectionBuilder;
use Robo\State\Data;

class ForEachCest
{

    /**
     * @return array
     */
    protected function examples()
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
    public function setIterableInConstructor(CliGuy $I, Example $example)
    {
        $actual = [];

        $I->wantTo('set iterable in the __constructor');
        $I
            ->taskForEach($example['items'])
            ->withBuilder(function (CollectionBuilder $builder, $key, $value) use (&$actual) {
                $builder->addCode(function () use ($key, $value, &$actual) {
                    $actual[] = "$key = $value";

                    return 0;
                });
            })
            ->run();

        $I->assertEquals($example['expected'], $actual);
    }

    /**
     * @dataProvider examples
     */
    public function setIterableWithDeferTaskConfiguration(CliGuy $I, Example $example)
    {
        $actual = [];

        $I->wantTo('set iterable with deferTaskConfiguration()');
        $I
            ->collectionBuilder()
            ->addCode(function (Data $data) use ($example) {
                $data['items'] = $example['items'];

                return 0;
            })
            ->addTask(
                $I
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

        $I->assertEquals($example['expected'], $actual);
    }

    public function uninitializedIterable(CliGuy $I)
    {
        $actual = 0;
        $I->wantTo('call the __constructor() without argument');
        $I
            ->taskForEach()
            ->withBuilder(function (CollectionBuilder $builder, $key, $value) use (&$actual) {
                $builder->addCode(function () use ($key, $value, &$actual) {
                    $actual++;

                    return 0;
                });
            })
            ->run();

        $I->assertEquals(0, $actual);
    }
}
