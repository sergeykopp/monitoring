<?php

namespace Kopp\Http\Controllers;

use Illuminate\Http\Request;
use Kopp\Drivers\ZabbixDriver;

class ZabbixController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template = 'zabbix.';
    }

    // Неподдерживаемые элементы данных
    public function getNotSupportedItems(Request $request)
    {
        $this->data['title'] = 'Неподдерживаемые элементы данных';
        $this->data['items'] = ZabbixDriver::getNotSupportedItems();
        $this->template .= 'getNotSupportedItems';
        return $this->renderOutput();
    }
}
