@extends('mainAPIZabbix')

@section('title')
	getHosts
@endsection

@section('body')
	<form>
		<h1>Поиск узлов по прокси</h1>
		<h2>Предназначен для решения проблем с прокси</h2>
		<fieldset class="inline-block">
			<legend>Прокси</legend>
			<fieldset>
				<select name="proxyName"></select>
			</fieldset>
		</fieldset>
		<fieldset class="inline-block">
			<legend>Процесс запроса</legend>
			<label name="process" class="process"></label>
		</fieldset>
		<fieldset>
			<input type="button" onclick="auth()" value="Запрос" />
		</fieldset>
	</form>

	<div id="content">
		<table>
			<thead>
			<tr>
				<td>IP адрес</td>
				<td>Статус</td>
				<td>Имя узла сети</td>
				<td>Видимое имя узла сети</td>
			</tr>
			</thead>
			<tbody name="target">
			</tbody>
		</table>
	</div>
@endsection

@section('script')
	<script language="JavaScript" src="/js/apiZabbix/getHosts.js?version=34"></script>
@endsection