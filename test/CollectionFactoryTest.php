<?php

declare(strict_types=1);

namespace LotosTest\Collection;

use Lotos\Collection\{Collection, CollectionFactory};
use PHPUnit\Framework\TestCase;

class CollectionFactoryTest extends TestCase
{

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers CollectionFactory::createCollection()
     * @dataProvider provideArgs
     */
    public function createCollectionFromFactory(?array $args) : void
    {
        $this->assertInstanceOf(
            Collection::class,
            CollectionFactory::createCollection($args),
            'Не удалось получить класс Collection из фабрики'
        );
    }

    /**
     * @test
     * @requires PHP >= 8.0
     * @covers CollectionFactory::createCollection()
     * @dataProvider provideArgs
     */
    public function compareCollectionFromFactory(?array $args) : void
    {
        $this->assertEquals(
            new Collection($args),
            CollectionFactory::createCollection($args),
            'Не удалось получить класс Collection из фабрики'
        );
    }

    public function provideArgs() : array
    {
        return [
            [null],
            [['1','2','3']],
            [[0, '1', false, true, []]]
        ];
    }
}

