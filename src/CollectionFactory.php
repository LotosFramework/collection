<?php

declare(strict_types=1);

namespace Lotos\Collection;

/**
 * Класс CollectionFactory используется в качестве статического фабричного фасада
 *
 * @author McLotos <mclotos@xakep.ru>
 * @copyright https://github.com/LotosFramework/Collection/COPYRIGHT.md
 * @license https://github.com/LotosFramework/Collection/LICENSE.md
 * @package Lotos\Collection
 * @version 1.5.1
 */
class CollectionFactory
{
    public static function createCollection(?array $entities = null) : Collection
    {
        return new Collection($entities);
    }
}
