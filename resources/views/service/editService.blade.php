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
                        <label>Группа сервисов: </label>
                        <select name="id_group_services">
                            @foreach($groupsServices as $groupServices)
                                <option value="{{ $groupServices->id }}"
                                    @if((old('id_group_services') ?? $service->id_group_services) == $groupServices->id)
                                        selected="selected"
                                    @endif
                                >
                                    {{ $groupServices->name }}
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset>
                        <label>Наименование: </label>
                        <input type="text" name="name" size="50" value="{{ old('name') ?? $service->name }}" autocomplete="off" />
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="update" value="Сохранить" onclick="this.style.display = 'none'" /></td>
                <td>
                    <input type="hidden" name="id_service" value="{{ old('id_service') ?? $service->id }}" />
                </td>
                <td style="text-align: right;">
                    <input type="button" name="delete" value="Удалить" onclick="if(confirm('Вы уверены, что хотите удалить запись?')) this.setAttribute('type', 'submit');" />
                </td>
            </tr>
        </table>
    </form>
    <script>
        document.getElementById("content").style.display = "block";
    </script>
@endsection