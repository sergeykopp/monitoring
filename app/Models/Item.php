<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'items';
    protected $primaryKey = 'itemid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public static $value_types = [
        0 => 'Числовой (с плавающей точкой)',
        1 => 'Символ',
        2 => 'Журнал (лог)',
        3 => 'Числовой (целое положительное)',
        4 => 'Текст',
    ];

    public static $value_units = ['B', '%', '', 'unixtime', 'sps', 'uptime', 'bps', 'ips', 'мс', 's', 'bit/s',
        'ms', 'qps', 'units', 'b', 'Bps', 'events', 'pps', 'V', 'rps', 'ops', 'kB/s', 'G',
        'mSec.', 'Bytes', 'сек', 'events/s', 'frames', 'errors', 'req', 'sector/request',
        'Mb', 'шт.', 'pct', 'Mbps', '°C', 'мин.', 'KB', 'req/sec', 'sec', 'C°', '/sec', 'шт',
        'kB', 'min', 'days', 'del/s', 'fet/s', 'ckp/s', 'upd/s', 'ins/s', 'min.', 'RowsPerSecond',
        'Gb', 'MB/s', 'words', 'rpm', 'kb', 'M', 'CCS', 'lines', 'calls', 'RPM', 'C', 'loc/s',
        'proc', 'rol/s', 'com/s', 'сессии', 'Гц', 'vps', 'Hz'];

    public function host()
    {
        return $this->hasOne('Kopp\Models\Host', 'hostid', 'hostid');
    }

    public function lastHistory()
    {
        return $this->hasOne('Kopp\Models\History', 'itemid', 'itemid')->orderby('clock', 'desc')->limit(1);
    }

    public function lastHistoryUint()
    {
        return $this->hasOne('Kopp\Models\HistoryUint', 'itemid', 'itemid')->orderby('clock', 'desc')->limit(1);
    }

    public function history()
    {
        return $this->hasMany('Kopp\Models\History', 'itemid', 'itemid')->orderby('clock', 'desc');
    }

    public function historyUint()
    {
        return $this->hasMany('Kopp\Models\HistoryUint', 'itemid', 'itemid')->orderby('clock', 'desc');
    }

    // Связь с таблицей graphs через таблицу graphs_items
    public function graphs()
    {
        return $this->belongsToMany('Kopp\Models\Graph', 'graphs_items', 'itemid', 'graphid');
    }
}
