<?php

namespace Kopp\Drivers;

use Kopp\Models\Item;

class ZabbixDriver
{
    // Неподдерживаемые элементы данных
    public static function getNotSupportedItems()
    {
        $items = Item::select('itemid', 'hostid', 'name')->with('host')
            ->where('state', 1)
            ->where('status', 0)
//            ->orderBy('name')
            ->get()
            ->sortBy('host.name');
        $items = $items->values();
        return $items;
    }
}