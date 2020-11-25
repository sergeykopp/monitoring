var requestInfo = new XMLHttpRequest();
var requestProxy = new XMLHttpRequest();
var target;
var process;
var proxy;
var arrPriority;
var arrState;

window.onload = function() {
	target = document.getElementsByName('target')[0];
	process = document.getElementsByName('process')[0];
	arrPriority = ['Не классифицировано', 'Информация' , 'Предупреждение' , 'Средняя', 'Высокая', 'Чрезвычайная'];
	arrState = ['ОК', 'ПРОБЛЕМА'];
	auth();
};

// Запрос
function query(auth) {
	proxy = [];
	getProxy(auth);
}

// Получение триггеров
function getTriggers(auth) {
	var now = new Date();
	var dayAgo = new Date();
	dayAgo.setHours(dayAgo.getHours() + (dayAgo.getTimezoneOffset()/60) + 3 - 24);
	var strDate = (10 > dayAgo.getDate() ? '0' + dayAgo.getDate() : dayAgo.getDate()) + '.' +
		(10 > (dayAgo.getMonth() * 1 + 1) ? '0' + (dayAgo.getMonth()  * 1 + 1) : (dayAgo.getMonth()  * 1 + 1)) + '.' + dayAgo.getFullYear() + ' ' +
		(10 > dayAgo.getHours() ? '0' + dayAgo.getHours() : dayAgo.getHours()) + ':' +
		(10 > dayAgo.getMinutes() ? '0' + dayAgo.getMinutes() : dayAgo.getMinutes());
	document.getElementsByName('dateStr')[0].innerHTML = 'Активные триггеры, сработавшие до ' + strDate + ' (МСК)';
	var json_triggers_get = {
		'jsonrpc': '2.0',
		'method': 'trigger.get',
		'params': {
			'output': [
				'triggerid',
				'description',
				'priority',
				'value',
				'status',
				'lastchange'
			],
			'monitored' : true,
			'min_severity' : 2,
			'lastChangeTill' : Math.floor(now.getTime()/1000 - 24 * 60 * 60),
			'filter': {
				'value' : 1,
			},
			'selectHosts': [
				'hostid',
				'host',
				'name',
				'status',
				'proxy_hostid'
			],
			'expandDescription': true,
			'sortfield': [
				'lastchange'
			],
			'sortorder': 'DESC'
		},
		'auth': auth,
		'id': 4
	};
	
	process.innerHTML += '<p>Получение триггеров ...</p>';
	
	requestInfo.onreadystatechange = function() {
		if(4 == requestInfo.readyState) {
			if(200 == requestInfo.status) {
				var triggers = JSON.parse(requestInfo.responseText).result;
				var html = '';
				var counter = 0;
				var date, strDate, age;
				for (var i=0;i<triggers.length;i++){
					if(counter % 2) {
						html += '<tr style="background-color: #eee;">';
					} else {									
						html += '<tr>';
					}
					html += '<td>' + triggers[i].hosts[0].name + '</td>';
					html += '<td>' + triggers[i].description + '</td>';
					html += '<td>' + arrPriority[triggers[i].priority] + '</td>';
					date = new Date(triggers[i].lastchange * 1000);
					age = (new Date().getTime() - date)/1000;
					date.setHours(date.getHours() + (date.getTimezoneOffset()/60) + 3);
					strDate = (10 > date.getDate() ? '0' + date.getDate() : date.getDate()) + '.' +
						(10 > (date.getMonth() * 1 + 1) ? '0' + (date.getMonth()  * 1 + 1) : (date.getMonth()  * 1 + 1)) + '.' + date.getFullYear() + ' ' +
						(10 > date.getHours() ? '0' + date.getHours() : date.getHours()) + ':' +
						(10 > date.getMinutes() ? '0' + date.getMinutes() : date.getMinutes());
					html += '<td>' + strDate + '</td>';
					html += '<td>' + getStrAge(age) + '</td>';					
					html += '</tr>';
					target.innerHTML = html;
					counter ++;
				}
				process.innerHTML += '<p>Всего триггеров: ' + counter + '</p>';
				logout(auth);
			} else {
				process.innerHTML = '<p>Нет ответа</p>';
			}
		}
	};
	
	requestInfo.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestInfo.setRequestHeader('Content-type', 'application/json-rpc');
	requestInfo.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestInfo.send(JSON.stringify(json_triggers_get));
}

// Получение всех прокси
function getProxy(auth) {
	var json_proxy_get = {
		'jsonrpc': '2.0',
		'method': 'proxy.get',
		'params': {
			// 'output': 'extend',
			'output': [
				'proxyid',
				'host'
			],
		},
		'auth': auth,
		'id': 2
	};
	
	requestProxy.onreadystatechange = function() {
		if(4 == requestProxy.readyState) {
			if(200 == requestProxy.status) {
				proxy = JSON.parse(requestProxy.responseText).result;
				getTriggers(auth);
			} else {
				process.innerHTML += '<p>Нет ответа</p>';
			}
		}
	};
	
	requestProxy.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestProxy.setRequestHeader('Content-type', 'application/json-rpc');
	requestProxy.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestProxy.send(JSON.stringify(json_proxy_get));
}

function getStrAge(age){
	var months, days, hours, minutes, seconds;
	var strDate = '';
	days = Math.floor(age/(24*60*60));
	age %= 24*60*60;
	hours = Math.floor(age/(60*60));
	age %= 60*60;
	minutes = Math.floor(age/(60));
	age %= 60;
	seconds = Math.floor(age);
	if(0 < days) {
		strDate += days + 'д ';
	}
	if(0 < hours) {
		strDate += hours + 'ч ';
	}
	if(0 < minutes) {
		strDate += minutes + 'м ';
	}
	if(0 < seconds) {
		strDate += seconds + 'c';
	}
	return strDate;
}