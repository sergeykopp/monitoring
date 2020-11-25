<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="cache-control" content="no-store"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon3.ico"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/reporting_style.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/reporting_media_1500.css?version=4"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/reporting_media_1300.css?version=4"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/reporting_media_1000.css?version=4"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/reporting_media_800.css?version=4"/>
    <title>Отчётность :: {{ $title }}</title>
</head>
<body>
<div id="header">
    <img id="logo" src="/img/logo.jpg?version=4"/>
</div>
<div id="menu">
    <table>
        <tr>
            <td>
                @foreach(config('settings.reports_menu') as $key=>$value)
                    @if('/' == Request::path())
                        @if('/' == $value)
                            <span id="unlink">{{ $key }}</span>
                        @else
                            <a href="{{ $value }}">{{ $key }}</a>
                        @endif
                    @elseif('/' == $value)
                        <a href="{{ $value }}">{{ $key }}</a>
                    @elseif(false !== strpos('/'.Request::path(), $value))
                        <span id="unlink">{{ $key }}</span>
                    @else
                        <a href="{{ $value }}">{{ $key }}</a>
                    @endif
                @endforeach
                @can('backup', new \Kopp\Models\Trouble())
                    <a href="{{ url('/admin') }}">Администрирование</a>
                @endcan
            </td>
            <td style="text-align: right">
                @if(true === Auth::check())
                    <a href="{{ url('/logout') }}"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        {{ Auth::user()->name }} - Выход
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                @else
                    @if(false !== strpos('/'.Request::path(), '/login'))
                        <span id="unlink" style="margin-right: 3%">Вход</span>
                    @else
                        <a href="{{ url('/login') }}">Вход</a>
                    @endif
                @endif
            </td>
        </tr>
    </table>
</div>
<div id="content">
    @yield('content')
    <div id="pageNavigate">
        @yield('pageNavigate')
    </div>
</div>
<div id="footer">
    Управление сервисной поддержки,
	Департамент ИТ-эксплуатации,
	Блок информационные технологии,
    Отдел мониторинга,
    2013 - {{ strftime("%Y") }}.
</div>
<script language="JavaScript" src="/js/menuScroll.js"></script>
<!-- Снегопад только первые 7 дней и последние 14 дней в году -->
@if(7 >= strftime("%j") or 351 <= strftime("%j"))
    <script language="JavaScript" src="/js/snowing.js"></script>
@endif
</body>
</html>
