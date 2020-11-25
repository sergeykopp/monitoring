<?php

namespace Kopp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kopp\Models\Service;

class StoreServiceRequest extends FormRequest
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
            if($this->request->has('id_service')){
                $service = Service::find($this->request->get('id_service'));
                if($this->request->get('name') == $service->name){
                    return [
                        'id_group_services' => 'exists:groups_services,id',
                    ];
                }
            }
        }
        return [
            'id_group_services' => 'exists:groups_services,id',
            'name' => 'required|unique:services,name',
        ];
    }

    // Сообщения ошибок валидации
    public function messages()
    {
        return [
            'id_group_services.exists' => 'Такой ГРУППЫ СЕРВИСОВ нет в базе',
            'name.required' => 'Поле НАИМЕНОВАНИЕ обязательно к заполнению',
            'name.unique' => 'Такой СЕРВИС уже есть в базе',
        ];
    }
}
