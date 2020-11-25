@extends('mainMonitoring')

@section('content')
    @if(0 < count($troubles))
		@php
            $reportStartTime = strtotime(strftime('%Y-%m-%d', time() - 24 * 60 * 60) . ' 09:00');
            $reportFinishTime = strtotime(strftime('%Y-%m-%d', time()) . ' 09:00');
        @endphp
        <table id="tableTroubles">
            <tr style="color: #183483; background: #6CAEDF; font-weight: bold;">
                <td style="text-align: center;">Дирекция<br />Филиал<br />Город<br />Подразделение</td>
                <td style="text-align: center;">Время<br />события<br />(МСК)</td>
                <td style="text-align: center;">Источник события</td>
                <td style="text-align: center;">Описание</td>
                <td style="text-align: center;">Решение</td>
                <td style="text-align: center;">Заявка в ОТРС</td>
                <td style="text-align: center;">Сервис</td>
                <td style="text-align: center;">Приоритет события</td>
                @can('update', new \Kopp\Models\Trouble())
                    <td style="text-align: center;">Дежурный</td>
                @endcan
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
                    @can('update', $trouble)
                        @if(true == $trouble->risk and $reportStartTime <= strtotime($trouble->started_at) and $reportFinishTime >= strtotime($trouble->started_at))
                            style="background-color: #e3f0f9;"
                        @else
                            @if($key % 2 != 0)
                                style="background-color: #eee;"
                            @endif
                        @endif
                    @else
                        @if($key % 2 != 0)
                            style="background-color: #eee;"
                        @endif
                    @endcan
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
                                <br />{{ $trouble->office->address }}
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
                    <td style="text-align: center;">{{ $trouble->service->name }}</td>
                    <td style="text-align: center;">{{ $trouble->status->name }}</td>
                    @can('update', $trouble)
                        @php
                            $words = explode(' ', $trouble->user->name);
                            $firstLetter = mb_substr($words[1],0,1,"UTF-8");
                            $lastLetter = mb_substr($words[2],0,1,"UTF-8");
                        @endphp
                        <td style="text-align: center;">{{ $words[0] }} {{ $firstLetter }}.{{ $lastLetter }}.</td>
                    @endcan
                    <td style="text-align: center;">
						<a href="\edit\{{ $trouble->id }}">Правка</a><br /><br />
						<a href="\new\{{ $trouble->id }}">Копия</a>
					</td>
                </tr>
            @endforeach
        </table>
    @else
        <h1>Актуальных проблем нет</h1>
        <script>
            document.getElementById("content").style.display = "block";
        </script>
    @endif
    <script language="JavaScript" src="/js/xmlHttpRequest.js"></script>
    <script language="JavaScript" src="/js/searchFullText.js"></script>
@endsection