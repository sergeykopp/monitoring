<html>
<head>
    <style>
        body {
            font-family: Verdana;
            font-size: 12px;
            margin: 0px;
        }

        td {
            text-align: center;
            margin: 5px 10px;
            padding: 5px;
            border: 1px dotted #999;
            empty-cells: show;
        }
    </style>
</head>
<body>

    <p>{{ $subject }}</p><br />
    @if(0 < count($troubles))
        <table>
            <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                <td style="text-align: center;">Дирекция<br/>Филиал<br/>Город<br/>Подразделение</td>
                <td style="text-align: center;">Время<br/>события<br/>(МСК)</td>
                <td style="text-align: center;">Время<br/>завершения<br/>(МСК)</td>
                <td style="text-align: center;">Интервал</td>
                <td style="text-align: center;">Описание</td>
                <td style="text-align: center;">Решение</td>
                <td style="text-align: center;">Сервис</td>
                <td style="text-align: center;">Приоритет события</td>
                <td style="text-align: center;">Заявка в ОТРС</td>
            </tr>
            @foreach($troubles as $key => $trouble)
                <tr
                    @if($key % 2 != 0)
                        style="background-color: #eee;"
                    @endif
                >
                    <td style="text-align: center;">
                        @if(null != $trouble->directorate)
                            {{ $trouble->directorate->name }} дирекция
                            @if(null != $trouble->filial)
                                <br/>{{ $trouble->filial->name }} филиал
                            @endif
                            @if(null != $trouble->city)
                                <br/>г. {{ $trouble->city->name }}
                            @endif
                            @if(null != $trouble->office)
                                <br/>{{ $trouble->office->name }}
                                <br/>{{ $trouble->office->address }}
                            @endif
                        @else
                            Все дирекции
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $trouble->started_at }}</td>
                    <td style="text-align: center;">{!! ($trouble->finished_at ?? '<br />') !!}</td>
                    <td style="text-align: center;">{!! ($trouble->interval ?? '<br />') !!}</td>
                    <td style="max-width: 600px;">{!! ($trouble->description ?? '<br />') !!}</td>
                    <td style="max-width: 600px;">{!! ($trouble->action ?? '<br />') !!}</td>
                    <td style="text-align: center;">{{ $trouble->service->name }}</td>
                    <td style="text-align: center;">{{ $trouble->status->name }}</td>
                    <td style="text-align: center;">{!! ($trouble->incident ?? '<br />') !!}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>Критичных событий за отчётный период не зафиксировано</p><br />
    @endif
    <p>С Уважением, Отдел мониторинга ИТ инфраструктуры ДИТ</p>

</body>
</html>