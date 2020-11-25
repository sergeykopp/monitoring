<?php

namespace Kopp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trouble extends Model
{
    use SoftDeletes; // Использование мягкого удаления (поле deleted_at)

    protected $table = 'troubles'; // Имя таблицы
    public $timestamps = false; // Не использовать поля created_at и updated_at

    // Связь с таблицей directorates
    public function directorate()
    {
        return $this->belongsTo('Kopp\Models\Directorate', 'id_directorate', 'id');
    }

    // Связь с таблицей filials
    public function filial()
    {
        return $this->belongsTo('Kopp\Models\Filial', 'id_filial', 'id');
    }

    // Связь с таблицей cities
    public function city()
    {
        return $this->belongsTo('Kopp\Models\City', 'id_city', 'id');
    }

    // Связь с таблицей offices
    public function office()
    {
        return $this->belongsTo('Kopp\Models\Office', 'id_office', 'id');
    }

    // Связь с таблицей sources
    public function source()
    {
        return $this->belongsTo('Kopp\Models\Source', 'id_source', 'id');
    }

    // Связь с таблицей services
    public function service()
    {
        return $this->belongsTo('Kopp\Models\Service', 'id_service', 'id');
    }

    // Связь с таблицей statuses
    public function status()
    {
        return $this->belongsTo('Kopp\Models\Status', 'id_status', 'id');
    }

    // Связь с таблицей users
    public function user()
    {
        return $this->belongsTo('Kopp\Models\User', 'id_user', 'id');
    }

    // Связь с таблицей causes
    public function cause()
    {
        return $this->belongsTo('Kopp\Models\Cause', 'id_cause', 'id');
    }

    // Изменение started_at после чтения из БД
    public function getStartedAtAttribute($value)
    {
        if (null != $value) {
            preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/u", $value, $regs);
            return "$regs[3].$regs[2].$regs[1] $regs[4]:$regs[5]";
        }
        return $value;
    }

    // Изменение finished_at после чтения из БД
    public function getFinishedAtAttribute($value)
    {
        if (null != $value) {
            preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/u", $value, $regs);
            return "$regs[3].$regs[2].$regs[1] $regs[4]:$regs[5]";
        }
        return $value;
    }

    // Изменение id_directorate перед записью в БД
    public function setIdDirectorateAttribute($value)
    {
        if ('' == $value) {
            $this->attributes['id_directorate'] = null;
        } else {
            $this->attributes['id_directorate'] = $value;
        }
    }

    // Изменение id_filial перед записью в БД
    public function setIdFilialAttribute($value)
    {
        if ('' == $value) {
            $this->attributes['id_filial'] = null;
        } else {
            if ($this->attributes['id_directorate'] != Filial::find($value)->directorate->id) {
                $this->attributes['id_filial'] = null;
            } else {
                $this->attributes['id_filial'] = $value;
            }
        }
    }

    // Изменение id_city перед записью в БД
    public function setIdCityAttribute($value)
    {
        if ('' == $value) {
            $this->attributes['id_city'] = null;
        } else {
            if ($this->attributes['id_filial'] != City::find($value)->filial->id) {
                $this->attributes['id_city'] = null;
            } else {
                $this->attributes['id_city'] = $value;
            }
        }
    }

    // Изменение id_office перед записью в БД
    public function setIdOfficeAttribute($value)
    {
        if ('' == $value) {
            $this->attributes['id_office'] = null;
        } else {
            if ($this->attributes['id_city'] != Office::find($value)->city->id) {
                $this->attributes['id_office'] = null;
            } else {
                $this->attributes['id_office'] = $value;
            }
        }
    }

    // Изменение incident перед записью в БД
    public function setIncidentAttribute($value)
    {
        if ('' == $value) {
            $this->attributes['incident'] = null;
        } else {
            $this->attributes['incident'] = $value;
        }
    }

    // Изменение started_at перед записью в БД
    public function setStartedAtAttribute($value)
    {
        if ('' != $value) {
            preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $value, $regs);
            $this->attributes['started_at'] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:00";
        } else {
            $this->attributes['started_at'] = null;
        }
    }

    // Изменение finished_at перед записью в БД
    public function setFinishedAtAttribute($value)
    {
        if ('' != $value) {
            preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2})\:([0-9]{1,2})/u", $value, $regs);
            $this->attributes['finished_at'] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:00";
        } else {
            $this->attributes['finished_at'] = null;
        }
    }

    // Изменение description перед записью в БД
    public function setDescriptionAttribute($value)
    {
        if ((preg_match('/(^")/u', $value)) && (preg_match('/("\s*$)/u', $value))) {
            $value = preg_replace('/(^")|("\s*$)/u', '', $value); // Удаление " в начале и в конце текста
        }
		$value = mb_ereg_replace('Обнаружена проблема: ', '', $value); // Удаление фразы 'Обнаружена проблема: '
        $value = preg_replace('/\t/u', ' ', $value); // Замена табуляции на пробел
        $value = preg_replace('/([" ]|\r\n)\1+/u', '$1', $value); // Удаление повторяющихся пробелов, кавычек и переводов строк
		$value = preg_replace("/\r\n /u", "\r\n", $value); // Удаление пробела в начале каждой строки
		$value = trim($value);
		$value = preg_replace('/(\.)([а-яёй])/u', '$1 $2', $value); // Добавление пробела между точкой и буквой
        $this->attributes['description'] = preg_replace('/(^\r\n)|(\r\n\s*$)/u', '', $value); // Удаление переводов строк в начале и в конце текста
    }

    // Изменение action перед записью в БД
    public function setActionAttribute($value)
    {
        if ((preg_match('/(^")/u', $value)) && (preg_match('/("\s*$)/u', $value))) {
            $value = preg_replace('/(^")|("\s*$)/u', '', $value); // Удаление " в начале и в конце текста
        }
        // Извлечение инцидента из поля РЕШЕНИЕ если инцидент явно не задан
        /*if (null == $this->attributes['incident']) {
            if (preg_match('/(инц[\.а-яёй №:]*[\d\,\. ]+)/iu', $value, $regs)) {
                $this->attributes['incident'] = preg_replace('/\D/u', '', $regs[1]); // Извлечение инцидента из поля РЕШЕНИЕ и удаление всего, кроме цифр
                $length = strlen($this->attributes['incident']);
                if ($length < 5 or $length > 10) {
                    $this->attributes['incident'] = null;
                } else {
                    $value = preg_replace('/инц[\.а-яёй №:]*[\d\,\. ]+/iu', '', $value); // Удаление инцидента из поля РЕШЕНИЕ
                }
            }
        }*/
		$value = mb_ereg_replace('Обнаружена проблема: ', '', $value); // Удаление фразы 'Обнаружена проблема: '
		$value = preg_replace('/\t/u', ' ', $value); // Замена табуляции на пробел
        $value = preg_replace('/([" ]|\r\n)\1+/u', '$1', $value); // Удаление повторяющихся пробелов, кавычек и переводов строк
		$value = preg_replace("/\r\n /u", "\r\n", $value); // Удаление пробела в начале каждой строки
		$value = trim($value);
        $value = preg_replace('/(\.)([а-яёй])/u', '$1 $2', $value); // Добавление пробела между точкой и буквой
        $this->attributes['action'] = preg_replace('/(^\r\n)|(\r\n\s*$)/u', '', $value); // Удаление переводов строк в начале и в конце текста
    }
}
