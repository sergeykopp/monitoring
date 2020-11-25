@extends('mainAdministration')

@section('content')
    <form id="author" method="post">
        {{ csrf_field() }}
        @if(session()->has('message'))
            <span style="color: green; font-weight: bold;">{{ session()->get('message') }}</span><br /><br />
        @endif
        <fieldset>
            <label>ФИО</label>
            <input id="name" type="text" name="name" size="40" value="{{ old('name') ?? $user->name }}" autocomplete="off" required autofocus />
        </fieldset>
        @if ($errors->has('name'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('name') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Почтовый адрес</label>
            <input id="email" type="email" name="email" size="40" value="{{ old('email') ?? $user->email }}" autocomplete="off" required />
        </fieldset>
        @if ($errors->has('email'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('email') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Имя пользователя</label>
            <input id="login" type="text" name="login" size="40" value="{{ old('login') ?? $user->login }}" autocomplete="off" required />
        </fieldset>
        @if ($errors->has('login'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('login') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Роли</label>
            @foreach($roles as $role)
                <input type="checkbox" name="id_roles[]" value="{{ $role->id }}"
                    @foreach($user->roles as $userRole)
                        @if($userRole->id == $role->id)
                            checked="checked"
                        @endif
                    @endforeach
                 />{{ $role->name }}
            @endforeach
        </fieldset>
        <fieldset>
            <input type="hidden" name="id_user" value="{{ old('id_user') ?? $user->id }}" />
            <input type="submit" value="Сохранить" onclick="this.style.display = 'none'" />
        </fieldset>
    </form>
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection
