@extends('mainAPIZabbix')

@section('title')
    getTriggersRoutersDependency
@endsection

@section('body')
    <form>
		<h1>Поиск неправильных провайдеров по каналам</h1>
		<h2>Предназначен для восстановления соответствия</h2>
		<h2>провайдера в наименовании и в описании</h2>
		<fieldset class="inline-block">
			<legend>Процесс запроса</legend>
			<label name="process" class="process"></label>
		</fieldset>
	</form>
	
	<div id="content">
		<table>
			<thead>
				<tr>
					<td>Имя триггера</td>
					<td>Видимое имя узла сети</td>
				</tr>
			</thead>
			<tbody name="target">
			</tbody>
		</table>
	</div>
@endsection

@section('script')
    <script language="JavaScript" src="/js/apiZabbix/getTriggersInvalidProviders.js?version=11"></script>
@endsection