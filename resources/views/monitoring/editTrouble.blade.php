@extends('mainMonitoring')

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
                <td style="width: 15%;">
                    <fieldset>
                        <label>Дирекция: </label>
                        <select name="id_directorate" onchange="changeDirectorate(false)">
                            <option value="">Все дирекции</option>
                            @foreach($directorates as $directorate)
                                <option value="{{ $directorate->id }}"
                                    @if((old('id_directorate') ?? $trouble->id_directorate) == $directorate->id)
                                        selected="selected"
                                    @endif
                                >
                                    {{ $directorate->name }}
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset>
                        <label>Филиал: </label>
                        @if(null != (old('id_directorate') ?? $trouble->id_directorate))
                            <select name="id_filial" onchange="changeFilial(false)">
                                <option value=""></option>
                                @foreach($filials as $filial)
                                    @if((old('id_directorate') ?? $trouble->id_directorate) == $filial->id_directorate)
                                        <option value="{{ $filial->id }}"
                                            @if((old('id_filial') ?? $trouble->id_filial) == $filial->id)
                                                selected="selected"
                                            @endif
                                        >
                                            {{ $filial->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <select name="id_filial" onchange="changeFilial(false)" disabled="disabled">
                            </select>
                        @endif
                    </fieldset>
                    <fieldset>
                        <label>Город: </label>
                        @if(null != (old('id_filial') ?? $trouble->id_filial))
                            <select name="id_city" onchange="changeCity(false)">
                                <option value=""></option>
                                @foreach($cities as $city)
                                    @if((old('id_filial') ?? $trouble->id_filial) == $city->id_filial)
                                        <option value="{{ $city->id }}"
                                            @if((old('id_city') ?? $trouble->id_city) == $city->id)
                                                selected="selected"
                                            @endif
                                        >
                                            {{ $city->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <select name="id_city" onchange="changeCity(false)" disabled="disabled">
                            </select>
                        @endif
                    </fieldset>
                    <fieldset>
                        <label>Подразделение: </label>
                        @if(null != (old('id_city') ?? $trouble->id_city))
                            <select name="id_office" onchange="changeOffice(false)">
                                <option value=""></option>
                                @foreach($offices as $office)
                                    @if((old('id_city') ?? $trouble->id_city) == $office->id_city)
                                        <option value="{{ $office->id }}"
                                            @if((old('id_office') ?? $trouble->id_office) == $office->id)
                                                selected="selected"
                                            @endif
                                        >
                                            {{ $office->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <select name="id_office" onchange="changeOffice(false)" disabled="disabled">
                            </select>
                        @endif
                        <br /><span name="buttonEditOffice" class="actionButton" style="display: none">Править подразделение</span>
                    </fieldset>
                    <fieldset>
                        <label name="address"></label>
                        <!--<p name="notes" style="color: green"></p>-->
                    </fieldset>
                    <fieldset>
                        <label>Время события: </label>
                        <input type="text" name="started_at" placeholder="00.00.0000 00:00" value="{{ old('started_at') ?? $trouble->started_at }}" autocomplete="off" />
                        <br /><span class="actionButton" onclick="minus4Hours('started_at')">- 4 часа</span>
                    </fieldset>
                    <fieldset>
                        <label>Заявка в ОТРС: </label>
                        <input type="text" name="incident" placeholder="00000" value="{{ old('incident') ?? $trouble->incident }}" autocomplete="off" />
                    </fieldset>
                    <fieldset>
                        <label>Время завершения: </label>
                        <input type="text" name="finished_at" placeholder="00.00.0000 00:00" value="{{ old('finished_at') ?? $trouble->finished_at }}" autocomplete="off" />
                        <br /><span class="actionButton" onclick="minus4Hours('finished_at')">- 4 часа</span>
                        <span class="actionButton" onclick="getNow(false)">Вставить текущее</span>
                    </fieldset>
                    <fieldset>
                        <label>Сервис: </label>
                        <select name="id_service">
                            <option value=""></option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}"
                                    @if((old('id_service') ?? $trouble->id_service) == $service->id)
                                        selected="selected"
                                    @endif
                                >
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset>
                        <label>Приоритет события: </label>
                        <select name="id_status">
                            <option value=""></option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}"
                                    @if((old('id_status') ?? $trouble->id_status) == $status->id)
                                        selected="selected"
                                    @endif
                                >
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                </td>
                <td>
                    <table>
                        <tr>
                            <td style="width: 50%">
                                <fieldset>
                                    <label>Источник события: </label>
                                    <select name="id_source">
                                        <option value=""></option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source->id }}"
                                                @if((old('id_source') ?? $trouble->id_source) == $source->id)
                                                    selected="selected"
                                                @endif
                                            >
                                                {{ $source->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label>Справочник городов: </label>
                                    <select name="catalogue_cities" onchange="changeCatalogueCities()">
                                        <option value=""></option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" id_filial="{{ $city->filial->id }}" id_directorate="{{ $city->filial->directorate->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label>Справочник подразделений: </label>
                                    <select name="catalogue_offices" onchange="changeCatalogueOffices()">
                                        <option value=""></option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}" id_city="{{ $office->city->id }}" id_filial="{{ $office->city->filial->id }}" id_directorate="{{ $office->city->filial->directorate->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </td>
                            <td>
                                @can('risk', new \Kopp\Models\Trouble())
                                    <fieldset style="border: 1px dotted #6CAEDF; border-radius: 10px;">
                                        <legend>Для операционных рисков</legend>
                                        <fieldset>
                                            <label>Причина: </label>
                                            <select name="id_cause">
                                                <option value=""></option>
                                                @foreach($causes as $cause)
                                                    <option value="{{ $cause->id }}"
                                                        @if((old('id_cause') ?? $trouble->id_cause) == $cause->id)
                                                            selected="selected"
                                                        @endif
                                                    >
                                                        {{ $cause->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                        <fieldset>
                                            <label>Детализация: </label>
                                            <input type="text" name="detail" value="{{ old('detail') ?? $trouble->detail }}" style="width: 95%" autocomplete="off" />
                                        </fieldset>
                                        <fieldset>
                                            <label><input type="checkbox" name="risk"
                                                @if(true == (old('risk') ?? $trouble->risk))
                                                    checked="checked"
                                                @endif
                                                /> Технический риск</label>
                                        </fieldset>
                                    </fieldset>
                                @endcan
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <fieldset>
                                    <label>Описание: </label>
                                    <textarea name="description" placeholder="Описание проблемы">{{ old('description') ?? $trouble->description }}</textarea>
                                </fieldset>
                                <fieldset>
                                    <label>Решение: </label>
                                    <textarea name="action" placeholder="Предпринятые действия для решения проблемы" ondblclick='this.value = this.value + "\r\nИнцидент эскалирован на группу Сети."'>{{ old('action') ?? $trouble->action }}</textarea>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="update" value="Сохранить" onclick="this.style.display = 'none'" /></td>
                <td>
                    <a href="/new/{{ old('id') ?? $trouble->id }}">Создать копию</a>
                    <input type="button" name="btn_history" value="Посмотреть историю изменений" onclick="getHistory({{ $trouble->id }}, true)" />
                    <input type="hidden" name="id_trouble" value="{{ old('id') ?? $trouble->id }}" />
                </td>
                <td style="text-align: right;"><input type="button" name="delete" value="Удалить" onclick="if(confirm('Вы уверены, что хотите удалить запись?')) this.setAttribute('type', 'submit');" /></td>
            </tr>
            <tr name="fieldHistory" style="display: none">
                <td colspan="2">
                    <fieldset>
                        <label>История изменения проблемы: </label>
                        <textarea name="history" placeholder="История изменений по данной проблеме" style="height: 500px"></textarea>
                    </fieldset>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <script language="JavaScript" src="/js/xmlHttpRequest.js"></script>
    <script language="JavaScript" src="/js/changeListOffices.js?version=4"></script>
    <script language="JavaScript" src="/js/getDateTime.js?version=2"></script>
    <script language="JavaScript" src="/js/getHistory.js"></script>
    @if(null != (old('id_office') ?? $trouble->id_office))
        <script>changeOffice(false);</script>
    @endif
@endsection