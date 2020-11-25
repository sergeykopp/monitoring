<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kopp\Models\City;

class StoreCityRequest extends FormRequest
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
            if($this->request->has('id_city')){
                $city = City::find($this->request->get('id_city'));
                if($this->request->get('name') == $city->name){
                    return [
                        'id_filial' => 'exists:filials,id',
                    ];
                }
            }
        }
        return [
            'id_filial' => 'exists:filials,id',
            'name' => 'required|unique:cities,name',
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'id_filial.exists' => 'Такого ФИЛИАЛА нет в базе',
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'name.unique' => 'Такой ГОРОД уже есть в базе',
        ];
    }
}
