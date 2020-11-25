<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackedEvent extends Model
{
    use SoftDeletes; // Использование мягкого удаления (поле deleted_at)

    protected $table = 'tracked_events';
    public $timestamps = false; // Не использовать поля created_at и updated_at

    // Связь с таблицей configuration_items через таблицу configuration_item_tracked_event
    public function configurationItems()
    {
        return $this->belongsToMany('Kopp\Models\ConfigurationItem', 'configuration_item_tracked_event', 'id_tracked_event', 'id_configuration_item');
    }

    // Связь с таблицей statuses
    public function status()
    {
        return $this->belongsTo('Kopp\Models\Status', 'id_status', 'id');
    }
}
