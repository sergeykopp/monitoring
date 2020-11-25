<?php

namespace Kopp\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kopp\Drivers\MailDriver;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Связь с таблицей troubles
    public function troubles()
    {
        return $this->hasMany('Kopp\Trouble', 'id_user', 'id');
    }

    // Связь с таблицей roles через таблицу role_user
    public function roles()
    {
        return $this->belongsToMany('Kopp\Models\Role', 'role_user', 'id_user', 'id_role');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $to = $this->email;
        MailDriver::resetPassword($to, $token);
    }
}
