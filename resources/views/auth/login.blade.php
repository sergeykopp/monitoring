@extends('mainMonitoring')

@section('content')
    <form id="author" method="post" action="{{ url('/login') }}">
        {{ csrf_field() }}
        <fieldset>
            <label>Имя пользователя</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" autocomplete="off" required autofocus />
        </fieldset>
        @if ($errors->has('login'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('login') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label>Пароль</label>
            <input id="password" type="password" name="password" required />
        </fieldset>
        @if ($errors->has('password'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('password') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <label><input type="checkbox" name="remember" /> Запомнить меня</label>
        </fieldset>
        <fieldset>
            <input type="submit" value="Вход" />
            <a href="{{ url('/password/reset') }}">Забыли пароль?</a>
        </fieldset>
    </form>
	<script>
		document.getElementById("content").style.display = "block";
	</script>
@endsection
