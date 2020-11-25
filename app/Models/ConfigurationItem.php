<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationItem extends Model
{
    use SoftDeletes; // Использование мягкого удаления (поле deleted_at)

    protected $table = 'configuration_items';

    // Связь с таблицей tracked_events через таблицу configuration_item_tracked_event
    public function trackedEvents()
    {
        return $this->belongsToMany('Kopp\Models\TrackedEvent', 'configuration_item_tracked_event', 'id_configuration_item', 'id_tracked_event');
    }

    // Связь с таблицей users (Владелец КЕ)
    public function configurationItemOwner()
    {
        return $this->belongsTo('Kopp\Models\User', 'id_ci_owner', 'id');
    }

    // Связь с таблицей users (Владелец сервиса)
    public function serviceOwner()
    {
        return $this->belongsTo('Kopp\Models\User', 'id_service_owner', 'id');
    }

    // Связь с таблицей services
    public function service()
    {
        return $this->belongsTo('Kopp\Models\Service', 'id_service', 'id');
    }

    // Изменение created_at после чтения из БД
    public function getCreatedAtAttribute($value)
    {
        if (null != $value) {
            preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/u", $value, $regs);
            return "$regs[3].$regs[2].$regs[1] $regs[4]:$regs[5]";
        }
        return $value;
    }

    // Изменение updated_at после чтения из БД
    public function getUpdatedAtAttribute($value)
    {
        if (null != $value) {
            preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/u", $value, $regs);
            return "$regs[3].$regs[2].$regs[1] $regs[4]:$regs[5]";
        }
        return $value;
    }

    // Изменение created_at перед записью в БД
    public function setCreatedAtAttribute($value)
    {
        if ('' != $value) {
            preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $value, $regs);
            $this->attributes['started_at'] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:00";
        } else {
            $this->attributes['started_at'] = null;
        }
    }

    // Изменение updated_at перед записью в БД
    public function setUpdatedAtAttribute($value)
    {
        if ('' != $value) {
            preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $value, $regs);
            $this->attributes['finished_at'] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:00";
        } else {
            $this->attributes['finished_at'] = null;
        }
    }
}
