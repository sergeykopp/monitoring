<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogDetail extends Model
{
    protected $connection = 'zabbixdb';
    protected $table = 'auditlog_details';
    protected $primaryKey = 'auditdetailid';
    public $timestamps = false; // Не использовать поля created_at и updated_at

}
