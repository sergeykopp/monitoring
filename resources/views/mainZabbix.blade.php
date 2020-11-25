<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="cache-control" content="no-store"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/apiZabbix.css?version=8"/>
    <title>Zabbix :: {{ $title }}</title>
</head>
<body>
    @yield('body')
	<!-- Снегопад только первые 7 дней и последние 14 дней в году -->
	@if(7 >= strftime("%j") or 351 <= strftime("%j"))
		@if(true === Auth::check() && 'vkraynev' == Auth::user()->login)
			<!-- Снегопад для Крайнева не работает -->
		@else
			<script language="JavaScript" src="/js/snowing.js"></script>
		@endif
	@endif
</body>
</html>
