<?php

declare(strict_types=1);

namespace Lotos\Collection;

use Ds\{
    Sequence as SequenceInterface,
    Collection as CollectionInterface,
    Deque
};
use \IteratorAggregate;
use \ArrayIterator;
use \Traversable;

/**
 * Класс Sequence реализует базовый интерфейс Последовательности и дополняет его удобным функционалом
 *
 * @author McLotos <mclotos@xakep.ru>
 * @copyright https://github.com/LotosFramework/Collection/COPYRIGHT.md
 * @license https://github.com/LotosFramework/Collection/LICENSE.md
 * @package Lotos\Collection
 * @version 2.0.0
 */
class Sequence implements IteratorAggregate, SequenceInterface
{

    /**
     * @var Deque $items - Элементы коллекции, записанные в очередь
     */
    private $items;

    /**
     *  Конструктор класса Sequence может принять массив, один элемент коллеции или ничего не принимать
     *
     * @method __construct
     * @param mixed|mixed[]|null Элемент или массив элементов для создания коллекции
     */
    public function __construct($items = null)
    {
        $this->items = new Deque($items);
    }

    /**
     * Метод allocate устанавливает вместимость коллекции
     *  ограничивая количество элементов
     *
     * Метод гарантирует, что будет выделено необходимое количество памяти,
     *  c помощью allocate нельзя уменьшать вместимость, можно только увеличивать.
     *
     * @method allocate
     * @param int $capacity Новое значение вместимости
     * @return void
     */
    public function allocate(int $capacity) : void
    {
        $this->items->allocate($capacity);
    }

    /**
     * Метод apply обноляет все элементы коллекции, применяя к ним callback-функцию
     *
     * @method apply
     * @param callable $callback Функция для изменения значения элемента коллекции
     * @return void
     */
    public function apply(callable $callback) : void
    {
        $this->items->apply($callback);
    }

    /**
     * Метод capacity возвращает текущую вместимость коллекции
     *
     * @method capacity
     * @param void
     * @return int Текущая вместимость коллекции
     */
    public function capacity() : int
    {
        return $this->items->capacity();
    }

    /**
     * Метод contains проверяет, все ли переданные значения есть в коллекции
     *
     * @method contains
     * @param mixed $values, ... любое количество элементов, или массив элементов,
     *   которые нужно найти в коллекции
     * @return bool Все ли переданные элементы найдены в коллекции
     * @example contains('a') - проверит есть ли элемент 'a' в коллекции
     * @example contains('a', 'b') - проверит есть ли элементы 'a' и 'b' в коллекции
     * @example contains(['a', 'b']) - проверит есть ли элементы 'a' и 'b' в коллекции
     */
    public function contains(...$values) : bool
    {
        return $this->items->contains(...$values);
    }

    /**
     * Метод filter использует callback-функцию для фильтрации массива
     *
     * @method filter
     * @param callable|null $callback, Функция, возращающая bool для фильтрации коллекции
     * @return Collection Коллекция, с оставшимися после фильтрации элементами
     */
    public function filter(?callable $callback = null) : SequenceInterface
    {
        return $this->newInstance($this->items->filter($callback)->toArray());
    }

    /**
     * Метод find используется для получения индекса элемента, по его значению
     *
     * @method find
     * @param mixed $value, Значение элемента, индекс которого нужно найти
     * @return mixed Индекс найденного элемента коллекции
     */
    public function find(mixed $value) : mixed
    {
        return $this->items->find($value);
    }

    /**
     * Метод first используется для получения первого элемента коллекции
     *
     * @method first
     * @return mixed Элемент коллекции
     */
    public function first() : mixed
    {
        return $this->items->first();
    }

    /**
     * Метод get используется для получения элемента по его индексу
     *
     * @method get
     * @param int Идентификатор элемента коллекции
     * @return mixed Элемент коллекции
     */
    public function get(int $index) : mixed
    {
        return $this->items->get($index);
    }

    /**
     * Метод insert используется для добавления нового элемента в коллекцию
     *
     * @method insert
     * @param int Идентификатор нового элемента коллекции
     * @param mixed $values, ...Новые элементы коллекции
     * @return void
     */
    public function insert(int $index, ...$values) : void
    {
        $params = array_values($values);
        array_unshift($params, $index);
        $this->items->insert(...$params);
    }

    /**
     * Метод join используется для склеивания коллекции в строку
     *
     * @method join
     * @param string $glue Необязательный разделитель элементов
     * @return string
     */
    public function join(?string $glue = null) : string
    {
        return $this->items->join($glue);
    }

    /**
     * Метод last используется для получения последнего элемента коллекции
     *
     * @method last
     * @return mixed
     */
    public function last() : mixed
    {
        return $this->items->last();
    }

    /**
     * Метод map используется для применения callback-функции ко всем элементам коллекции
     *
     * @method map
     * @param callable $callback - Функция, выполняющая какие-то действия с каждым элементом коллекции
     * @return Collection
     */
    public function map(callable $callback) : SequenceInterface
    {
        return $this->items->map($callback);
    }

    /**
     * Метод merge используется для объединения коллекции с новыми элементами
     *
     * @method merge
     * @param mixed $values новые элементы коллекции
     * @return Collection
     */
    public function merge(mixed $values) : SequenceInterface
    {
        return $this->items->merge($values);
    }

    /**
     * Метод pop извлекает последний элемент коллекции
     *
     * @method pop
     * @return mixed
     */
    public function pop() :mixed
    {
        return $this->items->pop();
    }

    /**
     * Метод push добавляет новые элементы в коллекцию
     *
     * @method push
     * @param mixed $values, ... Новые элементы коллекции
     * @return void
     */
    public function push(...$values) : void
    {
       $this->items->push(...$values);
    }

    /**
     * Метод reduce уменьшает коллекцию до одного значения, используя callback-функцию
     *
     * @method reduce
     * @param callable $callback Функция, выполняющая вычисления
     * @param mixed|null $initial стартовый аргумент для функции обратного вызова
     * @return void
     */
    public function reduce(callable $callback, $initial = null) : int
    {
        return $this->items->reduce($callback, $initial);
    }

    /**
     * Метод remove удаляет элемент коллекции по его индексу
     *
     * @method remove
     * @param int $index Индекс удаляемого элемента
     * @return mixed удаленный элемент
     */
    public function remove(int $index) : mixed
    {
        return $this->items->remove($index);
    }

    /**
     * Метод reverse разворачивает текущую коллекцию
     *
     * @method reverse
     * @return void
     * @example
     * $collection = new Collection([1, 2, 3]);
     * $colleciton->reverse();
     * print_r($colleciton->toArray()); //[3, 2, 1]
     */
    public function reverse() : void
    {
        $this->items->reverse();
    }

    /**
     * Метод reversed возвращает развернутую копию коллекции
     *
     * @method reversed
     * @return Collection
     * @example
     * $collection = new Collection([1, 2, 3]);
     * print_r($colleciton->reverse()->toArray()); //[3, 2, 1]
     */
    public function reversed() : SequenceInterface
    {
        return $this->items->reversed();
    }

    /**
     * Метод rotate перематывает последовательность на указанное количество элементов
     *
     * @method rotate
     * @return void
     * @example
     * $collection = new Collection([1, 2, 3, 4]);
     * $collection->rotate(1);
     * print_r($collection->toArray()); //[2, 3, 4, 1]
     * @example
     * $collection = new Collection([1, 2, 3, 4]);
     * $collection->rotate(-1);
     * print_r($collection->toArray()); //[4, 1, 2, 3]
     */
    public function rotate(int $rotations) : void
    {
        $this->items->rotate($rotations);
    }

    /**
     * Метод set устанавливает значение для элемента коллекции по id элемента
     *
     * @method set
     * @param int $index Идентификатор элемента коллекции
     * @param mixed $value Новое значение для элемента
     * @return void
     */
    public function set(int $index, mixed $value) : void
    {
        $this->items->set($index, $value);
    }

    /**
     * Метод shift извлекает первый элемент коллекции
     *
     * @method shift
     * @param int $index Идентификатор элемента коллекции
     * @param mixed $value Новое значение для элемента
     * @return mixed
     */
    public function shift() : mixed
    {
        return $this->items->shift();
    }

    /**
     * Метод slice извлекает срез коллекции
     *
     * @method slice
     * @param int $index Идентификатор первого элемента среза
     * @param int $length Количество элементов среза
     * @return Collection
     * @example
     *  $collection = new Collection([1, 2, 3, 4, 5])
     *  print_r($collection->slice(2)); // [3, 4, 5]
     * @example
     *  $collection = new Collection([1, 2, 3, 4, 5])
     *  print_r($collection->slice(1, 3)); // [2, 3, 4]
     * @example
     *  $collection = new Collection([1, 2, 3, 4, 5])
     *  print_r($collection->slice(-2)); // [4, 5]
     * @example
     *  $collection = new Collection([1, 2, 3, 4, 5])
     *  print_r($collection->slice(1, -1)); // [2, 3, 4]
     */
    public function slice(int $index, ?int $length = null ) : SequenceInterface
    {
        return $this->items->slice($index, $length);
    }

    /**
     * Метод sort применяет к коллекции функцию сортировки
     *
     * @method sort
     * @param callable $comparator Функция сортировки
     * @return void
     */
    public function sort(?callable $comparator = null) : void
    {
        $this->items->sort($comparator);
    }

    /**
     * Метод sorted применяет к копии коллекции функцию сортировки
     *
     * @method sorted
     * @param callable $comparator Функция сортировки
     * @return Collection Отсортированная копия коллекции
     */
    public function sorted(?callable $comparator = null) : SequenceInterface
    {
        return $this->items->sorted($comparator);
    }

    /**
     * Метод sum возвращает сумму всех элементов коллекции
     *
     * @method sum
     * @return int|float Сумма всех элементов коллекции
     */
    public function sum() : int | float
    {
        return $this->items->sum();
    }

    /**
     * Метод unshift добавляет новые элементы в начало коллекции
     *
     * @method unshoft
     * @return void
     */
    public function unshift(...$values) : void
    {
        $this->items->unshift(...$values);
    }

    /**
     * Метод getIterator возвращает ArrayIterator текущей коллекции
     *   чтобы коллекцию можно было перебирать массивом
     *
     * @method getIterator
     * @return ArrayIterator
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this);
    }

    /**
     * Метод clear очищает коллекцию
     *
     * @method clear
     * @return Collection чистая коллекция
     */
    public function clear() : SequenceInterface
    {
        $this->items->clear();
        return $this;
    }

    /**
     * Метод copy возвращает копию коллекции
     *
     * @method copy
     * @return Collection Копия коллекции
     */
    public function copy() : CollectionInterface
    {
        return $this->items->copy();
    }

    /**
     * Метод toArray конвертирует коллекцию в массив
     *
     * @method toArray
     * @return array
     */
    public function toArray() : array
    {
        if($this->items) {
            return $this->items->toArray();
        } else {
            return [];
        }
    }

    /**
     * Метод isEmpty проверяет есть ли значения в коллекции
     *
     * @method isEmpty
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Метод count возвращает количество элементов в коллекции
     *
     * @method count
     * @return int Количество элементов коллекции
     */
    public function count() : int
    {
        if($this->items) {
            return $this->items->count();
        } else {
            return 0;
        }
    }

    /**
     * Метод jsonSerialize возвращает сериализованную коллекцию
     *
     * @method jsonSerialize
     * @return mixed
     */
    public function jsonSerialize() : mixed
    {
        return $this->items->jsonSerialize();
    }

    /**
     * @see set
     */
    public function offsetSet($index, $value) : void
    {
        $this->set($index, $value);
    }

    /**
     * @see get
     */
    public function offsetGet(mixed $index) : mixed
    {
        return $this->get($index);
    }

    /**
     * @see exists
     */
    public function offsetExists($index) : bool
    {
        return ($this->get($index) !== null);
    }

    /**
     * @see remove
     */
    public function offsetUnset($index) : void
    {
        $this->remove($index);
    }
}
