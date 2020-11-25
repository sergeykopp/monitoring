<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class UserZabbix extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'users';
    protected $primaryKey = 'userid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    public function audit()
    {
        return $this->hasMany('Kopp\Models\AuditLog', 'userid', 'userid');
    }

}
