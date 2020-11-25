@extends('mainMonitoring')

@section('content')
    <form id="author" method="post" action="{{ url('/password/reset') }}">
        {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $token }}" />
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
            <label>Подтверждение пароля</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required />
        </fieldset>
        @if ($errors->has('password_confirmation'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('password_confirmation') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <input type="submit" value="Сохранить" />
        </fieldset>
    </form>
	<script>
		document.getElementById("content").style.display = "block";
	</script>
@endsection
