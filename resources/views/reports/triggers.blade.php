@extends('mainReports')

@section('content')
    <table class="tableInterval">
        <tr>
            <td style="vertical-align: top">
                <table>
                    <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                        <td style="text-align: center;">Узел сети</td>
                        <td style="text-align: center;">Имя триггера</td>
                        <td style="text-align: center;">Важность триггера</td>
                        <td style="text-align: center;">Последнее изменение</td>
                        <td style="text-align: center;">Возраст</td>
                    </tr>
                    @if (0 < count($triggers))
                        @foreach($triggers as $key => $trigger)
                            <tr
                                @if($key % 2 != 0)
                                    style="background-color: #eee;"
                                @endif
                            >
                                <td style="text-align: center;">{!! ($trigger->hostName) ? $trigger->hostName : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trigger->description) ? $trigger->description : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trigger->priority) ? $trigger->priority : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trigger->lastchange) ? strftime('%d.%m.%Y %H:%M', $trigger->lastchange) : '<br />' !!}</td>
                                <td style="text-align: center;">{!! ($trigger->age) ? $trigger->age : '<br />' !!}</td>
                            </tr>
                        @endforeach
                    @else
                        <script>
                            document.getElementById("content").style.display = "block";
                        </script>
                    @endif
                </table>
            </td>
            <td style="border: none; vertical-align: top;">
                    @if (0 < count($triggers))
                        <br /><a href="{{ asset($fileName) }}">Скачать отчёт xls</a><br /><br />
                    @endif
            </td>
        </tr>
    </table>
@endsection