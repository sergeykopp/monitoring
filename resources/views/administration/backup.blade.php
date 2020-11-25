@extends('mainAdministration')

@section('content')
    <form id="newEdit" method="post">
        {{ csrf_field() }}
        @if(session()->has('backup'))
            <span style="color: darkgreen; font-weight: bold;">{{ session()->get('backup') }}</span><br /><br />
        @endif
        @if(session()->has('error'))
            <span style="color: darkred; font-weight: bold;">{{ session()->get('error') }}</span><br /><br />
        @endif
        <fieldset>
            <input type="button" name="export" value="Экспорт в XML"
                   onclick="if(confirm('Вы уверены, что хотите экспортировать данные в файл?')) this.setAttribute('type', 'submit');"/>
            <input type="button" name="import" value="Импорт из XML"
                   onclick="if(confirm('Вы уверены, что хотите импортировать данные из файла?')) this.setAttribute('type', 'submit');"/>
        </fieldset>
    </form>
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection