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

        .supercritical{
            font-weight: bold;
            color: white;
        }

        .supercritical td{
            background-color: red;
        }

        .critical{
            font-weight: bold;
            color: red;
        }

        .warning{
            font-weight: bold;
            color: #F78115;
        }
    </style>
</head>
<body>
<table>
    <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
        <td style="text-align: center;">Дирекция<br/>Филиал<br/>Город<br/>Подразделение</td>
        <td style="text-align: center;">Время<br/>события<br/>(МСК)</td>
        <td style="text-align: center;">Источник события</td>
        <td style="text-align: center;">Описание</td>
        <td style="text-align: center;">Решение</td>
        <td style="text-align: center;">Заявка в ОТРС</td>
        <td style="text-align: center;">Время<br/>завершения<br/>(МСК)</td>
        <td style="text-align: center;">Сервис</td>
        <td style="text-align: center;">Приоритет события</td>
        <td style="text-align: center;">Дежурный</td>
    </tr>
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
        <td style="text-align: center;">{{ $trouble->source->name }}</td>
        <td style="max-width: 600px;">{!! ($trouble->description ?? '<br />') !!}</td>
        <td style="max-width: 600px;">{!! ($trouble->action ?? '<br />') !!}</td>
        <td style="text-align: center;">{!! ($trouble->incident ?? '<br />') !!}</td>
        <td style="text-align: center;">{!! ($trouble->finished_at ?? '<br />') !!}</td>
        <td style="text-align: center;">{{ $trouble->service->name }}</td>
        <td style="text-align: center;">{{ $trouble->status->name }}</td>
        @php
            $words = explode(' ', $trouble->user->name);
            $firstLetter = mb_substr($words[1],0,1,"UTF-8");
            $lastLetter = mb_substr($words[2],0,1,"UTF-8");
        @endphp
        <td style="text-align: center;">{{ $words[0] }} {{ $firstLetter }}.{{ $lastLetter }}.</td>
    </tr>
</table>
</body>
</html>