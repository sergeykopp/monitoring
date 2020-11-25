@extends('mainAdministration')

@section('content')
    <form id="author" method="post" action="{{ url('/register') }}">
        {{ csrf_field() }}
        <fieldset>
            <label>ФИО</label>
            <input id="name" type="text" name="name" size="40" value="{{ old('name') }}" autocomplete="off" required autofocus />
        </fieldset>
        @if ($errors->has('name'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('name') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Почтовый адрес</label>
            <input id="email" type="email" name="email" size="40" value="{{ old('email') }}" autocomplete="off" required />
        </fieldset>
        @if ($errors->has('email'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('email') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Имя пользователя</label>
            <input id="login" type="text" name="login" size="40" value="{{ old('login') }}" autocomplete="off" required />
        </fieldset>
        @if ($errors->has('login'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('login') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Пароль</label>
            <input id="password" type="password" size="40" name="password" required />
        </fieldset>
        @if ($errors->has('password'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('password') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Подтверждение пароля</label>
            <input id="password_confirmation" type="password" size="40" name="password_confirmation" required />
        </fieldset>
        @if ($errors->has('password_confirmation'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('password_confirmation') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <input type="submit" value="Зарегистрировать" />
        </fieldset>
    </form>
	<script>
		document.getElementById("content").style.display = "block";
	</script>
@endsection
