<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kopp\Models\Filial;

class StoreFilialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // Подготовка перед валидацией
    public function prepareForValidation()
    {

    }

    // Правила валидации
    public function rules()
    {
        if ($this->request->has('delete')){
            return [];
        } elseif ($this->request->has('update')){
            if($this->request->has('id_filial')){
                $filial = Filial::find($this->request->get('id_filial'));
                if($this->request->get('name') == $filial->name){
                    return [
                        'id_directorate' => 'exists:directorates,id',
                    ];
                }
            }
        }
        return [
            'id_directorate' => 'exists:directorates,id',
            'name' => 'required|unique:filials,name',
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'id_directorate.exists' => 'Такой ДИРЕКЦИИ нет в базе',
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'name.unique' => 'Такой ФИЛИАЛ уже есть в базе',
        ];
    }
}
