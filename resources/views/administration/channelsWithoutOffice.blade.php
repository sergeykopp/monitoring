@extends('mainAdministration')

@section('content')
    @if(0 < count($troubles))
        <table id="tableTroubles">
            <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                <td style="text-align: center;">Дирекция<br />Филиал<br />Город<br />Подразделение</td>
                <td style="text-align: center;">Время<br />события<br />(МСК)</td>
                <td style="text-align: center;">Источник события</td>
                <td style="text-align: center;">Описание</td>
                <td style="text-align: center;">Решение</td>
                <td style="text-align: center;">Инцидент</td>
                <td style="text-align: center;">Время<br />завершения<br />(МСК)</td>
                <td style="text-align: center;">Сервис</td>
                <td style="text-align: center;">Приоритет события</td>
            </tr>
            @foreach($troubles as $key => $trouble)
                <tr
                    @if($trouble->finished_at == null)
                        @if($trouble->status->name == 'Чрезвычайный')
                            class="supercritical"
                        @elseif($trouble->status->name == 'Высокий')
                            class="critical"
                        @elseif($trouble->status->name == 'Средний')
                            class="warning"
                        @endif
                    @endif
                    @if($key % 2 != 0)
                        style="background-color: #eee;"
                    @endif
                >
                    <td style="text-align: center;">
                        @if(null != $trouble->directorate)
                            {{ $trouble->directorate->name }} дирекция
                            @if(null != $trouble->filial)
                                <br />{{ $trouble->filial->name }} филиал
                            @endif
                            @if(null != $trouble->city)
                                <br />г. {{ $trouble->city->name }}
                            @endif
                            @if(null != $trouble->office)
                                <br />{{ $trouble->office->name }}
                                <br />{!! $trouble->office->address !!}
                            @endif
                        @else
                            Все дирекции
                        @endif

                    </td>
                    <td style="text-align: center;">{{ $trouble->started_at }}</td>
                    <td style="text-align: center;">{{ $trouble->source->name }}</td>
                    <td style="max-width: 600px;">{!! (('' != $trouble->description) ? $trouble->description : '<br />') !!}</td>
                    <td style="max-width: 600px;">{!! (('' != $trouble->action) ? $trouble->action : '<br />') !!}</td>
                    <td style="text-align: center;">{!! ($trouble->incident ?? '<br />') !!}</td>
                    <td style="text-align: center;">{!! ($trouble->finished_at ?? '<br />') !!}</td>
                    <td style="text-align: center;">{{ $trouble->service->name }}</td>
                    <td style="text-align: center;">{{ $trouble->status->name }}</td>
                    <td style="text-align: center;"><a href="\edit\{{ $trouble->id }}" target="_blank">Правка</a></td>
                </tr>
            @endforeach
        </table>
    @else
        <script>
            document.getElementById("content").style.display = "block";
        </script>
    @endif
@endsection
