<?php

declare(strict_types=1);

namespace LotosTest\Collection;

use Lotos\Collection\{Collection, CollectionFactory};
use PHPUnit\Framework\TestCase;

class TestCollectionClass
{
    public function __construct(
        private int|string $foo,
        private int|string $bar,
        private ?bool $isBaz = null,
        private ?bool $isQux = null
    )
    {}

    public function getFoo() : int|string
    {
        return $this->foo;
    }

    public function getBar() : int|string
    {
        return $this->bar;
    }

    public function isBaz() : ?bool
    {
        return $this->isBaz;
    }

    public function isQux() : ?bool
    {
        return $this->isQux;
    }
}

class CollectionTest extends TestCase
{

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::toArray()
     * @dataProvider provideArgs
     */
    public function checkToArrayReturnValidArray(?array $args) : void
    {
        $this->assertEquals(
            CollectionFactory::createCollection($args)->toArray(),
            $args,
            'Возвращаемый массив не совпадает с первоначальным'
        );
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::newInstance()
     * @dataProvider provideArgs
     */
    public function checkNewInstanceReturnsClearEntity(?array $args) : void
    {
        $this->assertEquals(
            CollectionFactory::createCollection($args)->newInstance(),
            CollectionFactory::createCollection(),
            'Не удалось получить новый объект'
        );
    }

    public function provideArgs() : array
    {
        return [
            [[null]],
            [['1','2','3']],
            [[0, '1', false, true, []]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::where()
     * @dataProvider provideArraysForSingleWhere
     */
    public function checkSingleWhereWithoutSymbolForArrays(array $data, string $property, mixed $value, array $result) : void
    {
        $this->assertEquals(
            CollectionFactory::createCollection($data)->where($property, $value)->toArray(),
            $result,
            'Не удалось получить правильный ответ'
        );
    }

    public function provideArraysForSingleWhere() : array
    {
        return [
            [
                'Поиск по foo без учета типа' =>
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => '0', 'bar' => '3', 'baz' => '0'],
                    ['foo' => 1, 'bar' => 0, 'baz' => 2],
                    ['foo' => '1', 'bar' => '3', 'baz' => '3'],
                    (new TestCollectionClass(foo: 0, bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ],'foo', 0,
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => '0', 'bar' => '3', 'baz' => '0'],
                    (new TestCollectionClass(foo: 0, bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ]
            ],
            [
                'Поиск по bar без учета типа' =>
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => '0', 'bar' => '3', 'baz' => '0'],
                    ['foo' => 1, 'bar' => 0, 'baz' => 2],
                    ['foo' => '1', 'bar' => '3', 'baz' => '3'],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ],'bar', 0,
                [
                    ['foo' => 1, 'bar' => 0, 'baz' => 2],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                ]
            ],
            [
                'Поиск по baz === true' =>
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => true],
                    ['foo' => '0', 'bar' => '3', 'baz' => false],
                    ['foo' => 1, 'bar' => 0, 'baz' => false],
                    ['foo' => '1', 'bar' => '3', 'baz' => null],
                ],'baz', true,
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => true],
                ]
            ],
            [
                'Поиск по baz === true' =>
                [
                    ['foo' => 0, 'bar' => 3, 'baz' => true],
                    ['foo' => '0', 'bar' => '3', 'baz' => false],
                    ['foo' => 1, 'bar' => 0, 'baz' => false],
                ],'baz', false,
                [
                    ['foo' => '0', 'bar' => '3', 'baz' => false],
                    ['foo' => 1, 'bar' => 0, 'baz' => false],
                ]
            ]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::where()
     * @dataProvider provideArraysForDoubleWhere
     */
    public function checkDoubleWhereWithoutSymbolForArrays(
        array $data,
        string $property1,
        mixed $value1,
        string $property2,
        mixed $value2,
        mixed $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->where($property1, $value1)
                ->where($property2, $value2)
                ->toArray(),
            $result,
        );
    }

    public function provideArraysForDoubleWhere() : array
    {
        return [
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ],'foo', 0, 'bar', 0,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: '0', isBaz: true)),
                ],'foo', 1, 'bar', '0',
                [
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: '0', isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => false],
                    ['foo' => 0, 'bar' => 0, 'baz' => true],
                    ['foo' => 0, 'bar' => 1, 'baz' => false],
                    ['foo' => 0, 'bar' => 1, 'baz' => true],
                    ['foo' => 1, 'bar' => 0, 'baz' => false],
                    ['foo' => 1, 'bar' => 0, 'baz' => true],
                    ['foo' => 1, 'bar' => 1, 'baz' => false],
                    ['foo' => 1, 'bar' => 1, 'baz' => true],
                ],'foo', 0, 'baz', true,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => true],
                    ['foo' => 0, 'bar' => 1, 'baz' => true],
                ]
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::where()
     * @dataProvider provideArraysWithSymbol
     */
    public function checkSingleWhereWithSymbolForArrays(
        array $data,
        string $property,
        string $symbol,
        mixed $value,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->where($property, $symbol, $value)
                ->toArray(),
            $result,
        );
    }

    public function provideArraysWithSymbol() : array
    {
        return [
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ], 'foo', '<', 1,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),

                ], 'foo', '=', 1,
                [
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ], 'foo', '>', 0,
                [
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ], 'foo', '<', '1',
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                    (new TestCollectionClass(foo: '1', bar: 1)),
                ], 'bar', '<', 1,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                    (new TestCollectionClass(foo: '1', bar: '1')),
                ], 'bar', '=', 1,
                [
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: '1', bar: '1')),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                    (new TestCollectionClass(foo: '1', bar: '1')),
                ], 'bar', '>', 0,
                [
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: '1', bar: '1')),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: '0', bar: 1)),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                    (new TestCollectionClass(foo: '1', bar: '1')),
                ], 'bar', '<', 1,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0)),
                    (new TestCollectionClass(foo: 0, bar: '0')),
                    (new TestCollectionClass(foo: 1, bar: 0)),
                    (new TestCollectionClass(foo: 1, bar: '0')),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => '0', 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ], 'bar', '===', 0,
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => '0', 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '0', bar: 1, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: 0, isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: '1', bar: 1, isBaz: true)),
                ], 'bar', '===', '0',
                [
                    ['foo' => 0, 'bar' => '0', 'baz' => 1],
                    (new TestCollectionClass(foo: 0, bar: '0', isBaz: true)),
                    (new TestCollectionClass(foo: 1, bar: '0', isBaz: true)),
                ]
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::whereBetween()
     * @dataProvider provideForWhereBetween
     */
    public function checkWhereBetween(
        array $data,
        string $property,
        array $values,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->whereBetween($property, $values)
                ->toArray(),
            $result,
        );
    }

    public function provideForWhereBetween() : array
    {
        return [
            [
                [
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 1],
                    ['foo' => 0, 'bar' => 1],
                    ['foo' => 1, 'bar' => 0],
                    ['foo' => 1, 'bar' => 0],
                    ['foo' => 1, 'bar' => 1],
                    ['foo' => 1, 'bar' => 1],
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 2],
                    ['foo' => 0, 'bar' => 2],
                    ['foo' => 2, 'bar' => 0],
                    ['foo' => 2, 'bar' => 0],
                    ['foo' => 2, 'bar' => 2],
                    ['foo' => 2, 'bar' => 2],
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 0],
                    ['foo' => 0, 'bar' => 3],
                    ['foo' => 0, 'bar' => 3],
                    ['foo' => 3, 'bar' => 0],
                    ['foo' => 3, 'bar' => 0],
                    ['foo' => 3, 'bar' => 3],
                    ['foo' => 3, 'bar' => 3],
                ],
                'foo',
                [1, 2],
                [
                    ['foo' => 1, 'bar' => 0],
                    ['foo' => 1, 'bar' => 0],
                    ['foo' => 1, 'bar' => 1],
                    ['foo' => 1, 'bar' => 1],
                    ['foo' => 2, 'bar' => 0],
                    ['foo' => 2, 'bar' => 0],
                    ['foo' => 2, 'bar' => 2],
                    ['foo' => 2, 'bar' => 2],
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3],
                ],
                'bar',
                [0, 2],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                ]
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::whereIn()
     * @dataProvider provideForWhereIn
     */
    public function checkWhereIn(
        array $data,
        string $property,
        array $values,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->whereIn($property, $values)
                ->toArray(),
            $result,
        );
    }

    public function provideForWhereIn() : array
    {
        return [
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'foo', [0, 2],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3]
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'bar', [1, 2],
                [
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2]
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'baz', [1, 2, 3],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ]
            ],

        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::whereNotIn()
     * @dataProvider provideForWhereNotIn
     */
    public function checkWhereNotIn(
        array $data,
        string $property,
        array $values,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->whereNotIn($property, $values)
                ->toArray(),
            $result,
        );
    }

    public function provideForWhereNotIn() : array
    {
        return [
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'foo', [1, 3],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'bar', [1, 2, 3],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3]
                ]
            ],
            [
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 0],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 0],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 0],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 0],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ], 'baz', [0],
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => 0, 'baz' => 2],
                    ['foo' => 0, 'bar' => 2, 'baz' => 2],
                    ['foo' => 2, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 3],
                    ['foo' => 0, 'bar' => 3, 'baz' => 3],
                    ['foo' => 3, 'bar' => 0, 'baz' => 3],
                    ['foo' => 3, 'bar' => 3, 'baz' => 3]
                ]
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::whereNull()
     * @dataProvider provideForWhereNull
     */
    public function checkWhereNull(
        array $data,
        string $property,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->whereNull($property)
                ->toArray(),
            $result,
        );
    }

    public function provideForWhereNull() : array
    {
        return [
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                ], 'foo',
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 1],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                ]
            ],
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => null, 'baz' => 1],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                ], 'bar',
                [
                    ['foo' => null, 'bar' => null, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                ]
            ],
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => null, 'baz' => null],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0],
                ], 'baz',
                [
                    ['foo' => null, 'bar' => null, 'baz' => null],
                ]
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Collection::whereNotNull()
     * @dataProvider provideForWhereNotNull
     */
    public function checkWhereNotNull(
        array $data,
        string $property,
        array $result
    ) : void {
        $this->assertEquals(
            CollectionFactory::createCollection($data)
                ->whereNotNull($property)
                ->toArray(),
            $result,
        );
    }

    public function provideForWhereNotNull() : array
    {
        return [
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => null, 'baz' => null],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ], 'foo',
                [
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ]
            ],
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => null, 'baz' => null],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ], 'bar',
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ]
            ],
            [
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => null, 'baz' => null],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ], 'baz',
                [
                    ['foo' => null, 'bar' => 0, 'baz' => 0],
                    ['foo' => 0, 'bar' => 0, 'baz' => 1],
                    ['foo' => 0, 'bar' => 1, 'baz' => 0],
                    ['foo' => 0, 'bar' => 1, 'baz' => 1],
                    ['foo' => 1, 'bar' => 0, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 0],
                    ['foo' => 1, 'bar' => 1, 'baz' => 1],
                    ['foo' => 0, 'bar' => null, 'baz' => 0],
                    ['foo' => 2, 'bar' => 0, 'baz' => 0],
                    ['foo' => null, 'bar' => 0, 'baz' => 2],
                    ['foo' => 2, 'bar' => 2, 'baz' => 0],
                    ['foo' => 2, 'bar' => 2, 'baz' => 2],
                    ['foo' => 0, 'bar' => 0, 'baz' => 0]
                ]
            ]
        ];
    }
}
