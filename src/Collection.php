<?php

/*
 * This file is part of the (c)Lotos framework.
 *
 * (c) McLotos <mclotos@xakep.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lotos\Collection;

use Ds\Sequence as SequenceInterface;

class Collection extends Sequence
{

    public function __construct($item = null)
    {
        if(is_array($item) && (array_keys($item) !== range(0, count($item) - 1)) && count($item)>0) {
            parent::__construct();
            $this->push($item);
        } else {
            parent::__construct($item);
        }
    }

    public function where(...$params) : SequenceInterface
    {
        if(count($params) == 3) {
            list($prop, $symbol, $value) = $params;
        } elseif(count($params) == 2) {
            list($prop, $value) = $params;
        }
        $symbol = ($symbol) ?? '=';
        $filtered = $this->filter(function($elem) use ($prop, $symbol, $value) {
            if(is_array($elem)) {
                return $this->getExpressionResult($elem[$prop], $symbol, $value);
            } elseif(is_object($elem)) {
                $method = 'get' . ucfirst($prop);
                if(method_exists($elem, $method)) {
                    return $this->getExpressionResult($elem->$method(), $symbol, $value);
                } elseif(substr($prop, 0, 2) == 'is') {
                    return $this->getExpressionResult($elem->$prop(), $symbol, $value);
                }
            }
        })->toArray();
        return $this->newInstance($filtered);
    }

    public function whereBetween(string $property, array $values) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($property, $values) {
            if($values[0] > $values[1]) {
                return (($elem[$property] < $values[0]) && ($elem[$property] > $values[1]));
            } elseif($values[0] < $values[1]) {
                return (($elem[$property] < $values[1]) && ($elem[$property] > $values[0]));
            }
        })->toArray());
    }

    public function whereIn(string $property, array $values) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use($property, $values) {
            return (in_array($elem[$property], $values));
        })->toArray());
    }

    private function getExpressionResult($prop, $symbol, $value) : bool
    {
        switch($symbol) {
            case '>':
                return ($prop > $value);
            break;
            case '<':
                return ($prop < $value);
            break;
            case '=':
                return ($prop == $value);
            break;
            case '>=':
                return ($prop >= $value);
            break;
            case '<=':
                return ($prop <= $value);
            break;
            case '<>':
                return ($prop <> $value);
            break;
            case '!=':
                return ($prop != $value);
            break;
        }
    }

    public function newInstance(array $data = []) : Collection
    {
        $class = get_called_class();
        $clone = new $class;
        if(count($data) > 0) {
            foreach($data as $element) {
                $clone->push($element);
            }
        }
        return $clone;
    }

    public function whereNull($property) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($property) {
            if(is_array($elem)) {
                return is_null($elem[$property]);
            } elseif(is_object($elem)) {
                $method = 'get' . ucfirst($property);
                if(method_exists($elem, $method)) {
                    return is_null($elem->$method());
                } elseif(substr($property, 0, 2) == 'is') {
                    return is_null($elem->$property());
                }
                return is_null($elem->$method());
            }
        })->toArray());
    }

    public function whereNotNull($property) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($property) {
            $method = 'get'.$property;
            if(method_exists($elem, $method)) {
                return !is_null($elem->$method());
            } elseif(substr($property, 0, 2) == 'is') {
                return !is_null($elem->$property());
            }
        })->toArray());
    }

    public function whereContain(string $param, string $value) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($param, $value) {
            $length = strlen($value);
            $method = 'get'.ucfirst($param);
            return (substr($elem->$method(), 0, $length) == substr($value, 0, $length));
        })->toArray());
    }

    public function whereNotIn(string $property, array $values) :SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($property, $values) {
            if(is_array($elem) && (array_key_exists($property, $elem))) {
                return (!in_array($elem[$property], $values));
            } elseif(is_object($elem) && property_exists($elem, $property)) {
                $method = 'get'.ucfirst($property);
                return (!in_array($elem->$method(), $values));
            }
        })->toArray());
    }

}
