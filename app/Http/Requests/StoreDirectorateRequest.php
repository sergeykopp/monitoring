<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kopp\Models\Directorate;

class StoreDirectorateRequest extends FormRequest
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
        }
        if ($this->request->has('update')){
            if($this->request->has('id_directorate')){
                $directorate = Directorate::find($this->request->get('id_directorate'));
                if($this->request->get('name') == $directorate->name){
                    return [];
                }
            }
        }
        return [
            'name' => 'required|unique:directorates,name',
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'name.unique' => 'Такая ДИРЕКЦИЯ уже есть в базе',
        ];
    }
}
