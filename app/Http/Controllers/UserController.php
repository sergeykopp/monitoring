<?php

namespace Kopp\Http\Controllers;

use Illuminate\Http\Request;
use Kopp\Drivers\LogDriver;
use Kopp\Drivers\MailDriver;
use Kopp\Http\Requests\StoreUserRequest;
use Kopp\Models\Role;
use Kopp\Models\Trouble;
use Kopp\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template = 'user.';
    }

    // Просмотр всех пользователей
    public function users(Request $request)
    {
        if (true === $request->user()->cannot('backup', new Trouble())) {
            return response()->view('errors.403', [], 403);
        }

        $users = User::orderBy('name')->get();

        $this->data['title'] = 'Все пользователи';
        $this->data['users'] = $users;
        $this->template .= 'users';
        return $this->renderOutput();
    }

    // Просмотр всех пользователей с ролями
    public function usersWithRoles(Request $request)
    {
        if (true === $request->user()->cannot('backup', new Trouble())) {
            return response()->view('errors.403', [], 403);
        }

        $users = User::has('roles')
            ->orderBy('name')->
            get();

        $this->data['title'] = 'Пользователи с ролями';
        $this->data['users'] = $users;
        $this->template .= 'users';
        return $this->renderOutput();
    }

    // Редактирование пользователя
    public function editUser(Request $request, $id)
    {
        if (true === $request->user()->cannot('backup', new Trouble())) {
            return response()->view('errors.403', [], 403);
        }

        $user = User::find($id);
        if (null == $user) {
            return response()->view('errors.404', [], 404);
        }
        $roles = Role::all();

        $this->data['title'] = 'Редактор пользователя';
        $this->data['user'] = $user;
        $this->data['roles'] = $roles;
        $this->template .= 'editUser';
        return $this->renderOutput();
    }

    // Сохранение пользователя
    // StoreUserRequest для верификации данных
    public function storeUser(StoreUserRequest $request)
    {
        if ($request->has('id_user')) {
            $user = User::find($request->input('id_user'));
            if (null == $user) {
                return response()->view('errors.404', [], 404);
            }
            LogDriver::storeUser("Пользователь id=$user->id до редактирования", $user);
            MailDriver::storeUser("Пользователь id=$user->id до редактирования", $user);
            self::setUserParameters($user, $request);
            $user->save();
            $user = User::find($user->id);
            LogDriver::storeUser("Пользователь id=$user->id после редактирования", $user);
            MailDriver::storeUser("Пользователь id=$user->id после редактирования", $user);
            return redirect()->route('editUser', ['id' => $user->id])->with('message', 'Информация сохранена');
        }
        return redirect()->route('admin');
    }

    // Заполнение полей Пользователя
    private static function setUserParameters(User $user, $request)
    {
        $parameters = $request->all();
        $user->name = $parameters['name'];
        $user->login = $parameters['login'];
        $user->email = $parameters['email'];
        if(isset($parameters['id_roles'])){
            $user->roles()->sync($parameters['id_roles']);
        } else {
            $user->roles()->detach();
        }
    }
}