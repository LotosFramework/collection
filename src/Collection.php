<?php

declare(strict_types=1);

namespace Lotos\Collection;

use Ds\Sequence as SequenceInterface;

/**
 * Класс Collection расширяет возможности класса Последовательности
 *
 * Collection удобно использовть для работы с многомерными ассоциативными массивами.
 * Синтаксис и схема работы соответствует тому, как происходит работа с базами данных
 * при использовнии QueryBuilder'а.
 *
 * @author McLotos <mclotos@xakep.ru>
 * @copyright https://github.com/LotosFramework/Collection/COPYRIGHT.md
 * @license https://github.com/LotosFramework/Collection/LICENSE.md
 * @package Lotos\Collection
 * @version 2.0.0
 */
class Collection extends Sequence
{

    /**
     *  Конструктор класса Collection может принять массив, один элемент коллеции или ничего не принимать
     *
     * @method __construct
     * @param mixed|mixed[]|null Элемент или массив элементов для создания коллекции
     */
    public function __construct(mixed $item = null)
    {
        if(
            is_array($item) &&
            (array_keys($item) !== range(0, count($item) - 1)) &&
            count($item) > 0
        ) {
            parent::__construct();
            $this->push($item);
        } else {
            parent::__construct($item ?? []);
        }
    }

    /**
     * Метод where производит фильтрацию коллеции и возвращает результат
     *   фильтрации по условию
     *
     * Первым аргументом всегда передается название свойства (ключа),
     *   по которому будет производиться поиск.
     * Вторым аргументом можно передавать знаки >, <, =, >=, <=, <>, !=
     *  или (внезапное нарушение стандартов, просто так исторически сложилось)
     *  можно передать сразу значение, по которому будет производиться фильтрация
     * Если вторым аргументом передан знак, то третьим передаете значение
     *
     * @method where
     * @param mixed $params, ... Имя свойства, символ и значение для поиска
     * @example where('name', 'Alice')
     * @example where('age', '>', 18)
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function where(...$params) : SequenceInterface
    {
        match (count($params)) {
            3 => list($prop, $symbol, $value) = $params,
            2 => list($prop, $value) = $params,
            default => null,
        };
        return $this->getFilteredInstance(property: $prop, symbol: ($symbol) ?? '=', value: $value);
    }

    /**
     * Метод whereBetween возвращает результат фильтрации по диапазону значений
     *
     * Метод проверяет значение на вхождение в диапазон
     *  и возвращает коллекцию, заполненную элементами,
     *  соответствующими результатам выборки.
     *
     * @method whereBetween
     * @param string $property, Свойство, которое будет проверяться на вхождение в диапазон
     * @param array $values, Диапазон возможных значений свойства
     * @example whereBetween('age', [18, 25]) age >= 18 && <=25
     * @example whereBetween('age', [25, 18]) age >= 18 && <=25
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereBetween(string $property, array $values) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($property, $values) {
            return match(true) {
                $values[0] >= $values[1] => (($elem[$property] <= $values[0]) && ($elem[$property] >= $values[1])),
                $values[0] <= $values[1] => (($elem[$property] <= $values[1]) && ($elem[$property] >= $values[0]))
            };
        })->toArray());
    }

    /**
     * Метод whereIn возвращает результат фильтрации по списку искомых значений
     *
     * Метод проверяет значение на соответствие переданным значениям
     *  и возвращает коллекцию, заполненную элементами,
     *  соответствующими результатам выборки.
     *
     * @method whereIn
     * @param string $property, Свойство, которое будет проверяться на соответствие значениям
     * @param array $values, Возможные значения свойства
     * @example whereIn('age', [18, 25]) age=18 or age=25
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereIn(string $property, array $values) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use($property, $values) {
            return (in_array($elem[$property], $values));
        })->toArray());
    }

    /**
     * Метод whereNotIn возвращает результат фильтрации по списку исключающих значений
     *
     * Метод проверяет значение на соответствие переданным значениям
     *  и возвращает коллекцию, заполненную элементами,
     *  не соответствующими результатам выборки.
     *
     * @method whereNotIn
     * @param string $property, Свойство, которое будет проверяться на соответствие значениям
     * @param array $values, Исключающие значения свойства
     * @example whereNotIn('age', [18, 25]) age !== 18 and age !== 25
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereNotIn(string $property, array $values) : SequenceInterface
    {
        $method = 'get'.ucfirst($property);
        return $this->newInstance($this->filter(function($elem) use ($property, $values, $method) {
            return match(true) {
                is_array($elem) && (array_key_exists($property, $elem)) => (!in_array($elem[$property], $values)),
                is_object($elem) && property_exists($elem, $property) => (!in_array($elem->$method(), $values))
            };
        })->toArray());
    }

    /**
     * Метод whereContain возвращает результат фильтрации по частичному совпадению
     *
     * Метод проверяет значение на частичное совпадение переданным значениям
     *  и возвращает коллекцию, заполненную элементами,
     *  у которых значение выбранного свойства содержит искомое слово.
     *
     * @method whereContain
     * @param string $param, Свойство, которое будет проверяться на частичное совпадение
     * @param string $value, Текст, с которым проводится сверка
     * @example whereContain('name', 'la') age like '%la%'
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereContain(string $param, string $value) : SequenceInterface
    {
        return $this->newInstance($this->filter(function($elem) use ($param, $value) {
            $length = strlen($value);
            $method = 'get'.ucfirst($param);
            return (substr($elem->$method(), 0, $length) == substr($value, 0, $length));
        })->toArray());
    }

    /**
     * Метод whereNull возвращает результат фильтрации по списку значений
     *
     * Метод проверяет значение на пустоту
     *  и возвращает коллекцию, заполненную элементами,
     *  у которых указанное свойство пустое.
     *
     * @method whereNull
     * @param string $property, Свойство, которое будет проверяться на пустоту
     * @example whereNull('age') age === null
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereNull($property) : SequenceInterface
    {
        return $this->getFilteredInstance(property: $property, symbol: '===', value: null);
    }

    /**
     * Метод whereNotNull возвращает результат фильтрации по списку значений
     *
     * Метод проверяет значение на пустоту
     *  и возвращает коллекцию, заполненную элементами,
     *  у которых указанное свойство не пустое.
     *
     * @method whereNotNull
     * @param string $property, Свойство, которое будет проверяться на пустоту
     * @example whereNotNull('age') age !== null
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    public function whereNotNull($property) : SequenceInterface
    {
        return $this->getFilteredInstance(property: $property, symbol: '!==', value: null);
    }

    /**
     * Метод для сопоставления символа с операцией
     *
     * @method getExpressionResult
     * @param mixed $val1, Значение которое будет проверяться на соответствие
     * @param string $symbol, Используемый символ
     * @param mixed $val2 Значение, с которым производится сравнение
     * @return bool True если значение выражения верно или false
     */
    private function getExpressionResult(mixed $val1, string $symbol, mixed $val2) : bool
    {
        return match($symbol) {
            '>' => ($val1 > $val2),
            '<' => ($val1 < $val2),
            '=', '==', => ($val1 == $val2),
            '===' => ($val1 === $val2),
            '>=', '=>' => ($val1 >= $val2),
            '<=', '=<' => ($val1 <= $val2),
            '<>', '!=' => ($val1 != $val2),
            '!==' => ($val1 !== $val2)
        };
    }

    /**
     * Метод для получения новой коллеции
     *
     * Метод можно использовать из уже заполненной коллеции,
     *  чтобы создать новую коллекцию на основе того же класса
     *  или если нужно просто очистить коллекцию
     *
     * @method newInstance
     * @param array $data, Новые элементы
     * @return Collection Новая коллекция
     */
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

    /**
     * Метод getFilteredInstance производит фильтрацию коллеции и возвращает результат
     *   фильтрации по условию
     *
     * Первым аргументом всегда передается название свойства (ключа),
     *   по которому будет производиться поиск.
     * Вторым аргументом можно передавать знаки >, <, =, >=, <=, <>, !=
     * Третьим передаете значение
     *
     * @method getFilteredInstance
     * @param string $property Свойство, по которому производится фильтрация,
     * @param string $symbol Условие фильтрации
     * @param mixed $value Значение, с которым проводится сравнение
     * @example getFilteredInstance('name', 'Alice')
     * @example getFilteredInstance('age', '>', 18)
     * @return SequenceInterface Коллекция, заполненная результатами выборки
     */
    private function getFilteredInstance(string $property, string $symbol, mixed $value) : SequenceInterface
    {
        $method = 'get' . ucfirst($property);
        return $this->newInstance($this->filter(function($elem) use ($property, $symbol, $value, $method) {
            return match(true) {
                is_array($elem) => $this->getExpressionResult($elem[$property], $symbol, $value),
                is_object($elem) => match(true) {
                    method_exists($elem, $method) => $this->getExpressionResult($elem->$method(), $symbol, $value),
                    substr($property, 0, 2) == 'is' => $this->getExpressionResult($elem->$property(), $symbol, $value)
                }
            };
        })->toArray());
    }
}
