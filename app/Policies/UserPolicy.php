<?php

namespace Kopp\Policies;

use Kopp\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    // Права на добавление пользователя
    public function add(User $user)
    {
        // Только администратор
        foreach($user->roles as $role){
            if('Administrator' == $role->name){
                return true;
            }
        }
        return false;
    }
}
