@extends('mainAdministration')

@section('content')
    <form id="newEdit" method="post">
        {{ csrf_field() }}
        <table>
            @if(count($errors) > 0)
                @foreach($errors->all() as $error)
                    <tr>
                        <td style="color: red; font-weight: bold;" colspan="3">{{ $error }}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td>
                    <fieldset>
                        <label>Наименование: </label>
                        <input type="text" name="name" size="50" value="{{ old('name') }}" autocomplete="off" />
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="create" value="Сохранить" onclick="this.style.display = 'none'" /></td>
            </tr>
        </table>
    </form>
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection