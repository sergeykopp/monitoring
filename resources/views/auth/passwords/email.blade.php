@extends('mainMonitoring')

<!-- Main Content -->
@section('content')
    <form id="author" method="post" action="{{ url('/password/email') }}">
        {{ csrf_field() }}
        @if (session('status'))
            <span style="color: green; font-weight: bold;">{{ session('status') }}</span>
        @endif
        <fieldset>
            <label>Почтовый адрес</label>
            <input id="email" type="email" name="email" size="40" value="{{ old('email') }}" autocomplete="off" required autofocus />
        </fieldset>
        @if ($errors->has('email'))
            <fieldset>
                <span style="color: red; font-weight: bold;">{{ $errors->first('email') }}</span>
            </fieldset>
        @endif
        <fieldset>
            <input type="submit" value="Отправить ссылку для восстановления пароля" />
        </fieldset>
    </form>
	<script>
		document.getElementById("content").style.display = "block";
	</script>
@endsection
