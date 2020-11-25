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
                        <label>Город: </label>
                        <select name="id_city">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}"
                                    @if(old('id_city') == $city->id)
                                        selected="selected"
                                    @endif
                                >
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset>
                        <label>Наименование: </label>
                        <input type="text" name="name" size="50" value="{{ old('name') }}" autocomplete="off" />
                    </fieldset>
                    <fieldset>
                        <label>Адрес: </label>
                        <input type="text" name="address" size="50" value="{{ old('address') }}" autocomplete="off" />
                    </fieldset>
                    <fieldset>
                        <label>Заметки: </label>
                        <textarea name="notes" placeholder="Заметки">{{ old('notes') }}</textarea>
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