<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="cache-control" content="no-store"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon2.ico"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/monitoring_style.css?version=2"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/monitoring_media_1500.css?version=5"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/monitoring_media_1300.css?version=5"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/monitoring_media_1000.css?version=5"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/monitoring_media_800.css?version=4"/>
    <title>Администрирование :: {{ $title }}</title>
</head>
<body>
<div id="header">
    <img id="logo" src="/img/logo.jpg?version=4"/>
</div>
<nav>
    <ul>
        @foreach(config('settings.administration_menu') as $key1=>$value1)
            @if(is_array($value1))
                <li>{{$key1}}
                    <ul>
                        @foreach($value1 as $key2=>$value2)
                            @if(is_array($value2))
                                <li>{{$key2}}
                                    <ul>
                                        @foreach($value2 as $key3=>$value3)
                                            @if(false == is_array($value3))
                                                <a href="{{$value3}}"><li>{{ $key3 }}</li></a>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <a href="{{$value2}}"><li>{{ $key2 }}</li></a>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @else
                <a href="{{$value1}}"><li>{{ $key1 }}</li></a>
            @endif
        @endforeach
        <a href="{{ url('/logout') }}"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <li class="last">{{ Auth::user()->name }} - Выход</li>
        </a>
    </ul>
</nav>
<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>
<div id="content">
    @if(session()->has('message'))
        <span style="color: green; font-weight: bold;">{{ session()->get('message') }}</span><br /><br />
    @endif
    @yield('content')
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
