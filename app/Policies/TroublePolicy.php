<?php

namespace Kopp\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Kopp\Models\Trouble;
use Kopp\Models\User;

class TroublePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // Права на добавление проблемы
    public function add(User $user)
    {
        // Только дежурный
        foreach ($user->roles as $role) {
            if ('Dutyman' == $role->name) {
                return true;
            }
        }
        return false;
    }

    // Права на изменение проблемы
    public function update(User $user, Trouble $trouble)
    {
        // Либо автор записи
        /*if($trouble->id_user == $user->id){
            return true;
        }*/
        // Либо администратор
        /*foreach($user->roles as $role){
            if('Administrator' == $role->name){
                return true;
            }
        }*/
        // Только дежурный
        foreach ($user->roles as $role) {
            if ('Dutyman' == $role->name) {
                return true;
            }
        }
        return false;
    }

    // Права на изменение риск-параметров
    public function risk(User $user, Trouble $trouble)
    {
        // Только риск-координотор
        foreach ($user->roles as $role) {
            if ('Risk' == $role->name) {
                return true;
            }
        }
        return false;
    }

    // Права на резервное копирование базы данных
    public function backup(User $user)
    {
        // Только администратор
        foreach ($user->roles as $role) {
            if ('Administrator' == $role->name) {
                return true;
            }
        }
        return false;
    }
}