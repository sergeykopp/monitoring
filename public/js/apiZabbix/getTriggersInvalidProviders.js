var requestInfo = new XMLHttpRequest();
var target;
var process;

window.onload = function() {
	target = document.getElementsByName('target')[0];
	process = document.getElementsByName('process')[0];
	auth();
};

// Запрос
function query(auth) {
	getTriggers(auth);
}

// Получение элементов
function getTriggers(auth) {
	var json_triggers_get = {
		'jsonrpc': '2.0',
		'method': 'trigger.get',
		'params': {
			// 'output': 'extend',
			'output': [
				'triggerid',
				'description',
				'comments'
			],
			'active': true,
			'search': {
				'description': 'канал'
			},
			'selectHosts': [
				'hostid',
				'name',
				'status'
			],
			'expandDescription': true,
			'sortfield': [
				'hostname',
				'description',
			],
			'sortorder': 'ASC'
		},
		'auth': auth,
		'id': 2
	};
	
	process.innerHTML += '<p>Получение триггеров ...</p>';
	
	requestInfo.onreadystatechange = function() {
		if(4 == requestInfo.readyState) {
			if(200 == requestInfo.status) {
				var triggers = JSON.parse(requestInfo.responseText).result;
				// target.innerHTML += JSON.stringify(triggers[0]); return; 
				var html = '';
				var counter = 0;
				var provider;
				var regExp = /(недоступен канал |потери на канале )([a-zа-яё]+)(.+)/i;
				for (var i=0;i<triggers.length;i++){
					provider = triggers[i].description.match(regExp);
					if ((null != provider) && (-1 == triggers[i].comments.toLowerCase().indexOf(provider[2].toLowerCase()))) {
						if(counter % 2) {
							html += '<tr style="background-color: #eee;">';
						} else {									
							html += '<tr>';
						}
						html += '<td>' + triggers[i].description + '</td>';
						html += '<td>' + triggers[i].hosts[0].name + '</td>';
						html += '<td><a target="_blank" href="https://' + host + '/zabbix/triggers.php?form=update&hostid=' + triggers[i].hosts[0].hostid + '&triggerid=' + triggers[i].triggerid + '">Ссылка на триггер<a/></td>';
						html += '</tr>';
						target.innerHTML = html;
						counter ++;
					}
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