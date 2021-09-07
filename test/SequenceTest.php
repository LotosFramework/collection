<?php

declare(strict_types=1);

namespace LotosTest\Collection;

use Lotos\Collection\{Sequence, CollectionFactory};
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::capacity()
     * @covers Sequence::allocate()
     * @dataProvider provideCapacity
     */
    public function changeCapacity(int $newCapacity, int $resultCapacity) : void
    {
        $collection = CollectionFactory::createCollection();
        $collection->allocate($newCapacity);
        $this->assertEquals(
            $resultCapacity,
            $collection->capacity(),
            'Capacity не совпадают'
        );
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::capacity()
     * @covers Sequence::allocate()
     * @dataProvider provideCapacity
     */
    public function execAllocate(int $newCapacity, int $resultCapacity) : void
    {
        $collection = CollectionFactory::createCollection();
        $collection->allocate($newCapacity);
        $this->assertEquals(
            $resultCapacity,
            $collection->capacity(),
            'Capacity не совпадают'
        );
    }

    public function provideCapacity() : array
    {
        //новое capacity,
        //ожидаемый ответ
        return [
            [1, 8],
            [2, 8],
            [3, 8],
            [5, 8],
            [8, 8],
            [13, 16],
            [21, 32],
            [34, 64],
            [55, 64],
            [89, 128],
            [144, 256]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::toArray()
     * @dataProvider provideArrays
     */
    public function execToArray(array $elements) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $elements,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideArrays() : array
    {
        return [
            [[1, 2, 3, 4]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::apply()
     * @dataProvider provideFunctions
     */
    public function execApply(array $elements, callable $function, array $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $collection->apply($function);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideFunctions() : array
    {
        return [
            [[1, 2, 3], function($a) { return $a + 1;}, [2, 3, 4]],
            [[1, 2, 3], function($a) { return $a - .5;}, [.5, 1.5, 2.5]],
            [[1, 2, 3], function($a) { return $a * 2;}, [2, 4, 6]],
            [[1, 2, 3], function($a) { return $a / 2;}, [.5, 1, 1.5]],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::contains()
     * @dataProvider provideArraysForContains
     */
    public function checkContains(array $elements, mixed $forSearch, bool $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->contains($forSearch),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForContains() : array
    {
        return [
            [['foo', 'bar', 'baz', 1, 2, 3], 'foo', true],
            [['foo', 'bar', 'baz', 1, 2, 3], 'bar', true],
            [['foo', 'bar', 'baz', 1, 2, 3], 'baz', true],
            [['foo', 'bar', 'baz', 1, 2, 3], 1, true],
            [['foo', 'bar', 'baz', 1, 2, 3], 2, true],
            [['foo', 'bar', 'baz', 1, 2, 3], 3, true],
            [['foo', 'bar', 'baz', 1, 2, 3], '1', false],
            [['foo', 'bar', 'baz', 1, 2, 3], '2', false],
            [['foo', 'bar', 'baz', 1, 2, 3], '3', false],

        ];
    }


    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::contains()
     * @dataProvider provideVssArraysForContains
     */
    public function checkContainsVss(array $elements, mixed $forSearch, bool $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->contains(...$forSearch),
            'Массивы результата не совпадают'
        );
    }

    public function provideVssArraysForContains() : array
    {
        return [
            [['foo', 'bar', 'baz', 1, 2, 3], ['foo', 'bar'], true],
            [['foo', 'bar', 'baz', 1, 2, 3], ['bar', 'baz'], true],
            [['foo', 'bar', 'baz', 1, 2, 3], ['baz'], true],
            [['foo', 'bar', 'baz', 1, 2, 3], [1, 'foo'], true],
            [['foo', 'bar', 'baz', 1, 2, 3], [2, 'baz', 3], true],
            [['foo', 'bar', 'baz', 1, 2, 3], [3, 'foo', 'baz'], true],
            [['foo', 'bar', 'baz', 1, 2, 3], ['1', 'foo'], false],
            [['foo', 'bar', 'baz', 1, 2, 3], [2, 'baz', '3'], false],
            [['foo', 'bar', 'baz', 1, 2, 3], ['3', 'foo', 'baz'], false],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::filter()
     * @dataProvider provideArraysForFilter
     */
    public function checkFilter(array $elements, callable $forSearch, array $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->filter($forSearch)->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForFilter()
    {
        return [
            [[1, 2, 3], function($a) { return $a > 1;}, [2, 3]],
            [[1, 2, 3], function($a) { return $a < 3;}, [1, 2]],
            [[1, 2, 3, 4], function($a) { return $a % 2 === 0;}, [2, 4]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::find()
     * @dataProvider provideArraysForFind
     */
    public function checkFind(array $elements, mixed $forSearch, mixed $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->find($forSearch),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForFind()
    {
        return [
            [[1, 2, 3], 1, 0],
            [[1, 2, 3], 3, 2],
            [[1, 2, 3, 4], 'a', false]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::first()
     * @dataProvider provideArraysForFirst
     */
    public function checkFirst(array $elements, mixed $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->first(),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForFirst()
    {
        return [
            [[1, 2, 3], 1],
            [[2, 3], 2],
            [['foo', 'bar', 3, 4], 'foo']
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::get()
     * @covers Sequence::offsetGet()
     * @dataProvider provideArraysForGet
     */
    public function checkGet(array $elements, int $index, mixed $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->get($index),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForGet()
    {
        return [
            [[1, 2, 3], 1, 2],
            [[2, 3], 1, 3],
            [['foo', 'bar', 3, 4], 0, 'foo']
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::insert()
     * @dataProvider provideArraysForInsert
     */
    public function checkInsert(array $elements, mixed $element, int $index, array $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $collection->insert($index, $element);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForInsert()
    {
        return [
            [[1, 2, 3], 'foo', 0, ['foo', 1, 2, 3]],
            [[2, 3], 1, 1, [2, 1, 3]],
            [['foo', 'bar', 3, 4], 'baz', 2, ['foo', 'bar', 'baz', 3, 4]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::join()
     * @dataProvider provideArraysForJoin
     */
    public function checkJoin(array $elements, string $glue, string $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        ;
        $this->assertEquals(
            $result,
            $collection->join($glue),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForJoin()
    {
        return [
            [[1, 2, 3], '-', '1-2-3'],
            [[2, 3, 1], '.', '2.3.1'],
            [['foo', 'bar', 3, 4], ' ', 'foo bar 3 4']
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::last()
     * @dataProvider provideArraysForLast
     */
    public function checkLast(array $elements, mixed $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        ;
        $this->assertEquals(
            $result,
            $collection->last(),
            'Массивы результата не совпадают'
        );
    }

    public function provideArraysForLast()
    {
        return [
            [[1, 2, 3, 'foo'], 'foo'],
            [[2, 3], 3],
            [['foo', 'bar', [3, 4]], [3, 4]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::map()
     * @dataProvider provideFunctions
     */
    public function checkMap(array $elements, callable $forMap, array $result) : void
    {
        $collection = CollectionFactory::createCollection($elements);
        $this->assertEquals(
            $result,
            $collection->map($forMap)->toArray(),
            'Массивы результата не совпадают'
        );
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::merge()
     * @dataProvider provideMerge
     */
    public function checkMerge(array $arr, array $elems, array $result) : void
    {
        $collection = CollectionFactory::createCollection($arr);
        $this->assertEquals(
            $result,
            $collection->merge($elems)->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideMerge()
    {
        return [
            [
                [1, 2, 3],
                ['foo'],
                [1, 2, 3, 'foo']
            ],
            [
                [[1, 2, 3], 'foo'],
                [[4, 5, 6], 'bar'],
                [[1, 2, 3], 'foo', [4, 5, 6], 'bar']
            ]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::pop()
     * @dataProvider providePop
     */
    public function checkPop(array $arr) : void
    {
        $collection = CollectionFactory::createCollection($arr);
        $count = count($arr);
        $this->assertEquals(
            array_pop($arr),
            $collection->pop(),
            'Массивы результата не совпадают'
        );
    }

    public function providePop() : array
    {
        return [
            [[1, 2, 3]],
            [['foo', 'bar', 'baz']],
            [[1, 'foo', 'qux']]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::push()
     * @dataProvider providePush
     */
    public function checkPush(array $origin, mixed $elem, array $added) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->push($elem);
        $result = $origin;
        array_push($result, $elem);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
        array_push($result, $added);
        $collection->push($added);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function providePush() : array
    {
        return [
            [[1, 2, 3], 4, [5, 6, 7]],
            [['foo', 'bar', 'baz'], 'qux', ['fooo', 'baar', 'baaz', 'quux']],
            [[1, 'foo', 'bar'], 2, ['baz', 3, 4, 5]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::reduce()
     * @dataProvider provideReduce
     */
    public function checkReduce(array $origin, callable $reduce, mixed $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $result,
            $collection->reduce($reduce),
            'Результат неправильный'
        );
    }

    public function provideReduce() : array
    {
        return [
            [[1, 2, 3, 4, 5, 6], function($carry, $item) {return $carry += $item + 2;},  33],
            [[1, 2, 3, 4, 5, 6], function($carry, $item) {return $carry *= $item * 2;},  0],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::remove()
     * @dataProvider provideRemove
     */
    public function checkRemove(array $origin, int $index, mixed $elem, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $elem,
            $collection->remove($index),
            'Результат неправильный'
        );

        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideRemove() : array
    {
        return [
            [[1, 2, 3], 0, 1, [2, 3]],
            [[1, 2, 3], 1, 2, [1, 3]],
            [[1, 2, 3], 2, 3, [1, 2]],
            [['foo', 'bar', 'baz'], 0, 'foo', ['bar', 'baz']],
            [['foo', 'bar', 'baz'], 1, 'bar', ['foo', 'baz']],
            [['foo', 'bar', 'baz'], 2, 'baz', ['foo', 'bar']],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::reverse()
     * @dataProvider provideReverse
     */
    public function checkReverse(array $origin, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->reverse();
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideReverse() : array
    {
        return [
            [[1, 2, 3], [3, 2, 1]],
            [['foo', 'bar', 'baz'], ['baz', 'bar', 'foo']],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::reversed()
     * @dataProvider provideReverse
     */
    public function checkReversed(array $origin, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $result,
            $collection->reversed()->toArray(),
            'Массивы результата не совпадают'
        );
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::rotate()
     * @dataProvider provideRotate
     */
    public function checkRotate(array $origin, int $index, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->rotate($index);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideRotate() : array
    {
        return [
            [[1, 2, 3], 1, [2, 3, 1]],
            [[1, 2, 3], 2, [3, 1, 2]],
            [[1, 2, 3], 3, [1, 2, 3]],
            [[1, 2, 3], -1, [3, 1, 2]],
            [[1, 2, 3], -2, [2, 3, 1]],
            [[1, 2, 3], -3, [1, 2, 3]],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::set()
     * @covers Sequence::offsetSet()
     * @dataProvider provideSet
     */
    public function checkSet(array $origin, int $index, mixed $value, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->set($index, $value);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideSet() : array
    {
        return [
            [['foo', 'bar', 'baz'], 0, 'qux', ['qux', 'bar', 'baz']],
            [['foo', 'bar', 'baz'], 1, 'qux', ['foo', 'qux', 'baz']],
            [['foo', 'bar', 'baz'], 2, 'qux', ['foo', 'bar', 'qux']],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::slice()
     * @dataProvider provideSlice
     */
    public function checkSlice(array $origin, int $index, array $result, ?int $length = null) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $result,
            $collection->slice($index, $length)->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideSlice() : array
    {
        return [
            [[1, 2, 3, 4, 5], 0, [1, 2, 3, 4, 5], null],
            [[1, 2, 3, 4, 5], 1, [2, 3, 4, 5], null],
            [[1, 2, 3, 4, 5], 2, [3, 4, 5], null],
            [[1, 2, 3, 4, 5], 3, [4, 5], null],
            [[1, 2, 3, 4, 5], 0, [1, 2, 3, 4, 5], 5],
            [[1, 2, 3, 4, 5], 0, [1, 2, 3, 4], 4],
            [[1, 2, 3, 4, 5], 0, [1, 2, 3], 3],
            [[1, 2, 3, 4, 5], 0, [1, 2], 2],
            [[1, 2, 3, 4, 5], 1, [2, 3], 2],
            [[1, 2, 3, 4, 5], 2, [3, 4, 5], 3],
            [[1, 2, 3, 4, 5], 3, [4, 5], 4],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::sorted()
     * @dataProvider provideSort
     */
    public function checkSorted(array $origin, array $result, ?callable $sorter = null) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $result,
            $collection->sorted($sorter)->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideSorted() : array
    {
        return [
            [[4, 5, 3, 2, 1, 0], [0, 1, 2, 3, 4, 5]]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::sort()
     * @dataProvider provideSort
     */
    public function checkSort(array $origin, array $result, ?callable $sorter = null) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->sort($sorter);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideSort() : array
    {
        return [
            [[4, 5, 3, 2, 0, 1], [0, 1, 2, 3, 4, 5], null],
            [
                [4, 5, 3, 2, 0, 1],
                [0, 1, 2, 3, 4, 5],
                function($a, $b) {return $a <=> $b;}
            ],
            [
                [4, 5, 3, 2, 0, 1],
                [5, 4, 3, 2, 1, 0],
                function($a, $b) {return $b <=> $a;}
            ],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::sum()
     * @dataProvider provideSum
     */
    public function checkSum(array $origin, float $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $result,
            $collection->sum(),
            'Результат неправильный'
        );
    }

    public function provideSum() : array
    {
        return [
            [[1, 2, 3, 4, 5], 15],
            [[1, 2, 3, 4, -5], 5],
            [[1, 2, 3, 4, .5], 10.5]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::unshift()
     * @dataProvider provideUnshift
     */
    public function checkUnshift(array $origin, mixed $elem, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->unshift($elem);
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideUnshift() : array
    {
        return [
            [[1, 2, 3, 4, 5], 0, [0, 1, 2, 3, 4, 5]],
            [['foo', 'bar', 'baz'], 'qux', ['qux', 'foo', 'bar', 'baz']],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::clear()
     * @dataProvider provideClear
     */
    public function checkClear(array $origin, array $result) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $collection->clear();
        $this->assertEquals(
            $result,
            $collection->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideClear() : array
    {
        return [
            [[1, 2, 3, 4, 5], []],
            [['foo', 'bar', 'baz'], []],
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::copy()
     * @dataProvider provideCopy
     */
    public function checkCopy(array $origin) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $copy = $collection->copy();
        $this->assertEquals(
            $collection->toArray(),
            $copy->toArray(),
            'Массивы результата не совпадают'
        );
    }

    public function provideCopy() : array
    {
        return [
            [[1, 2, 3, 4, 5]],
            [['foo', 'bar', 'baz', 'qux']]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::isEmpty()
     * @dataProvider provideIsEmpty
     */
    public function checkIsEmpty(array $origin, bool $answer) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $answer,
            $collection->isEmpty(),
            'Результат неправильный'
        );
    }

    public function provideIsEmpty() : array
    {
        return [
            [[1, 2, 3, 4, 5], false],
            [['foo', 'bar', 'baz', 'qux'], false],
            [[], true]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::count()
     * @dataProvider provideCount
     */
    public function checkCount(array $origin, int $answer) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $answer,
            $collection->count(),
            'Результат неправильный'
        );
    }

    public function provideCount() : array
    {
        return [
            [[1, 2, 3, 4, 5], 5],
            [['foo', 'bar', 'baz', 'qux'], 4],
            [[], 0]
        ];
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers Sequence::jsonSerialize()
     * @dataProvider provideJsonSerialize
     */
    public function checkJsonSerialize(array $origin, string $answer) : void
    {
        $collection = CollectionFactory::createCollection($origin);
        $this->assertEquals(
            $answer,
            json_encode($collection->jsonSerialize()),
            'Результат неправильный'
        );
    }

    public function provideJsonSerialize() : array
    {
        return [
            [[1, 2, 3, 4, 5], json_encode([1, 2, 3, 4, 5])],
            [['foo', 'bar', 'baz', 'qux'], json_encode(['foo', 'bar', 'baz', 'qux'])]
        ];
    }
}

