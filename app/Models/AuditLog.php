<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'auditlog';
    protected $primaryKey = 'auditid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public static $actions = [
        0 => 'Добавить',
        1 => 'Обновить',
        2 => 'Удалить',
        3 => 'Вход в систему',
        4 => 'Выход из системы',
        5 => 'Активировать',
        6 => 'Отключить',
    ];

    public static $resourcetypes = [
        0 => 'Пользователь',
        2 => 'Настройка Zabbix',
        3 => 'Способ оповещений',
        4 => 'Узел сети',
        5 => 'Действие',
        6 => 'График',
        7 => 'Элемент графика',
        11 => 'Группа пользователей',
        12 => 'Группа элементов данных',
        13 => 'Триггер',
        14 => 'Группа узлов сети',
        15 => 'Элемент данных',
        16 => 'Изображение',
        17 => 'Преобразование значений',
        18 => 'Услуга',
        19 => 'Карта сети',
        20 => 'Комплексный экран',
        22 => 'Веб-сценарий',
        23 => 'Правило обнаружения',
        24 => 'Слайд-шоу',
        25 => 'Скрипт',
        26 => 'Прокси',
        27 => 'Обслуживание',
        28 => 'Регулярное выражение',
        29 => 'Макрос',
        30 => 'Шаблон',
        31 => 'Прототип триггеров',
        32 => 'Сответствия иконок',
    ];

    public function detail()
    {
        return $this->hasOne('Kopp\Models\AuditLogDetail', 'auditid', 'auditid');
    }

    public function user()
    {
        return $this->hasOne('Kopp\Models\UserZabbix', 'userid', 'userid');
    }
}
