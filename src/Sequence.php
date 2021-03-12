<?php

/*
 * This file is part of the (c)Lotos framework.
 *
 * (c) McLotos <mclotos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lotos\Collection;

use Ds\{
    Sequence as SequenceInterface,
    Collection as CollectionInterface,
    Deque
};
use \UnderflowException;
use \IteratorAggregate;
use \ArrayIterator;

class Sequence implements IteratorAggregate, SequenceInterface {

    private $items;

    public function __construct($items = null)
    {
        $this->items = (is_null($items))
            ? null
            : new Deque($items);
    }

    public function allocate(int $capacity) : void
    {
        $this->items->allocate($capacity);
    }

    public function apply(callable $callback) : void
    {
        $this->items->apply($callback);
    }

    public function capacity() : int
    {
        return $this->items->capacity();
    }

    public function contains(...$values) : bool
    {
        if($this->items) {
            return call_user_func_array([$this->items, 'contains'], array_values($values));
        } else {
            return false;
        }
    }

    public function filter(?callable $callback = null) : SequenceInterface
    {
        if($this->items) {
            return $this->items->filter($callback);
        }
        return new self();
    }

    public function find($value)
    {
        if($this->items) {
            return $this->items->find($value);
        }
        return null;
    }

    public function first()
    {
        if($this->items) {
            return $this->items->first();
        }
        throw new UnderflowException('No items in collection');
    }

    public function get(int $index)
    {
        return $this->items->get($index);
    }

    public function insert(int $index, ...$values) : void
    {
        $params = array_values($values);
        array_unshift($params, $index);
        call_user_func_array([$this->items, 'insert'], array_values($params));
    }

    public function join(string $glue = null) : string
    {
        return $this->items->join($glue);
    }

    public function last()
    {
        if($this->items) {
            return $this->items->last();
        } else {
            return null;
        }
    }

    public function map(callable $callback) : SequenceInterface
    {
        if($this->items) {
            return $this->items->map($callback);
        }
        throw new UnderflowException('Not items in collection');
    }

    public function merge($values) : SequenceInterface
    {
        return $this->items->merge($values);
    }

    public function pop()
    {
        return $this->items->pop();
    }

    public function push(...$values) : void
    {
        if($this->items) {
            call_user_func_array([$this->items, 'push'], array_values($values));
        } else {
            $this->items = new Deque();
            call_user_func_array([$this->items, 'push'], array_values($values));
        }
    }

    public function reduce(callable $callback, $initial = null)
    {
        return $this->items->reduce($callback, $initial);
    }

    public function remove(int $index)
    {
        return $this->items->remove($index);
    }

    public function reverse() : void
    {
        $this->items->reverse();
    }

    public function reversed() : SequenceInterface
    {
        return $this->items->reversed();
    }

    public function rotate(int $rotations) : void
    {
        $this->items->rotate($rotations);
    }

    public function set(int $index, $value) : void
    {
        $this->items->set($index, $value);
    }

    public function shift()
    {
        return $this->items->shift();
    }

    public function slice(int $index, ?int $length = null ) : SequenceInterface
    {
        return $this->items->slice($index, $length);
    }

    public function sort(?callable $comparator = null) : void
    {
        $this->items->sort($comparator);
    }

    public function sorted(?callable $comparator = null) : SequenceInterface
    {
        return $this->items->sorted($comparator);
    }

    public function sum() : int
    {
        return $this->items->sum();
    }

    public function unshift(...$values) : void
    {
        call_user_func_array([$this->items, 'unshift'], array_values($values));
    }

    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this);
    }

    public function clear() : SequenceInterface
    {
        return $this->items->clear();
    }

    public function copy() : CollectionInterface
    {
        return $this->items->copy();
    }

    public function toArray() : array
    {
        if($this->items) {
            return $this->items->toArray();
        } else {
            return [];
        }
    }

    public function isEmpty() : bool
    {
        return $this->items->isEmpty();
    }

    public function count() : int
    {
        if($this->items) {
            return $this->items->count();
        } else {
            return 0;
        }
    }

    public function jsonSerialize()
    {
        return $this->items->jsonSerialize();
    }

    public function offsetSet($index, $value) : void
    {
        $this->set($index, $value);
    }

    public function offsetGet($index)
    {
        return $this->get($index);
    }

    public function offsetExists($index) : bool
    {
        return ($this->get($index) !== null);
    }

    public function offsetUnset($index) : void
    {
        $this->remove($index);
    }
}
