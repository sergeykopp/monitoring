var requestInfo = new XMLHttpRequest();
var requestProxy = new XMLHttpRequest();
var requestHost = new XMLHttpRequest();
var triggerComments;
var triggerCommentsNew;
var triggerStatus;
var triggerStatusChecked;
var hostName;
var hostStatus;
var hostStatusChecked;
var typeQuery;
var typeQueryChecked;
var target;
var process;
var proxy;
var hostsId = [];
var arrPriority;
var arrState;
var warningFieldBlink;
var warningField;
var counterQuery;

window.onload = function() {
	warningField = document.getElementsByClassName('warning')[0];
	triggerComments = document.getElementsByName('triggerComments')[0];
	triggerCommentsNew = document.getElementsByName('triggerCommentsNew')[0];
	triggerStatus = document.getElementsByName('triggerStatus');
	hostName = document.getElementsByName('hostName')[0];
	hostStatus = document.getElementsByName('hostStatus');
	typeQuery = document.getElementsByName('typeQuery');
	target = document.getElementsByName('target')[0];
	process = document.getElementsByName('process')[0];
	arrPriority = ['Не классифицировано', 'Информация' , 'Предупреждение' , 'Средняя', 'Высокая', 'Чрезвычайная'];
	arrState = ['ОК', 'ПРОБЛЕМА'];
	
	// При изменении типа запроса, мигать предупреждением о правах на запись
	// и разблокировать поле для изменения
	for(var i=0; i<typeQuery.length; i++){
		typeQuery[i].addEventListener('click', function() {
			if('update' == this.value){
				warningFieldBlink = setInterval(function(){
					if('white' == warningField.style.color){
						warningField.style.color = 'red';
					} else {
						warningField.style.color = 'white';
					}
				}, 500);
				triggerCommentsNew.disabled = false;
				triggerCommentsNew.removeAttribute("class");
			}
			if('get' == this.value) {
				clearInterval(warningFieldBlink);
				warningField.style.color = '';
				triggerCommentsNew.disabled = true;
				triggerCommentsNew.setAttribute("class", "disabled");
			}	
		}, false);
	}
};

// Запрос
function query(auth) {
	proxy = [];
	hostsId = [];
	counterQuery = 0;
	if ('' == triggerComments.value){
		process.innerHTML = '';
		alert('Не задана фраза для описания триггеров');
		return;
	}
	// Чтение выбранного статуса триггеров
	for(var i=0; i<triggerStatus.length; i++){
		if(triggerStatus[i].checked == true){
			triggerStatusChecked = triggerStatus[i].value;
			break;
		}
	}
	// Чтение выбранного статуса хостов
	for(var i=0; i<hostStatus.length; i++){
		if(hostStatus[i].checked == true){
			hostStatusChecked = hostStatus[i].value;
			break;
		}
	}
	// Чтение выбранного типа запроса
	for(var i=0; i<typeQuery.length; i++){
		if(typeQuery[i].checked == true){
			typeQueryChecked = typeQuery[i].value;
			break;
		}
	}
	getProxy(auth);
}

// Получение триггеров
function getTriggers(auth) {
	var json_triggers_get = {
		'jsonrpc': '2.0',
		'method': 'trigger.get',
		'params': {
			// 'output': 'extend',
			'output': [
				'triggerid',
				'description',
				'priority',
				'value',
				'status',
				'comments'
			],
			'filter': {
				'status': triggerStatusChecked
			},
			'search': {
				'comments': triggerComments.value.replace(/\n/ig,"\r\n")
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
				'hostname',
				'description',
			],
			'sortorder': 'ASC'
		},
		'auth': auth,
		'id': 4
	};
	
	// Добавление id узлов
	if (0 != hostsId.length) {
		json_triggers_get.params.hostids = hostsId;
	}
	
	// Только в состоянии ПРОБЛЕМА
	if (true == document.getElementsByName('triggerValue')[0].checked){
		json_triggers_get.params.filter.value = 1;
	}
	
	process.innerHTML += '<p>Получение триггеров ...</p>';
	
	requestInfo.onreadystatechange = function() {
		if(4 == requestInfo.readyState) {
			if(200 == requestInfo.status) {
				var triggers = JSON.parse(requestInfo.responseText).result;
				var hostNameLower;
				var hostHostLower;
				var hostNameFieldLower = hostName.value.toLowerCase().trim();
				var html = '';
				var counter = 0;
				var regExp = new RegExp(triggerComments.value.replace(/\n/ig,"\r\n").replace(/([\^\$\<\>\(\)\[\{\\\|\.\*\?\+])/ig,"\\$1"), 'i');
				var newPhrase = triggerCommentsNew.value.replace(/\n/ig,"\r\n");
				// target.innerHTML += JSON.stringify(triggers[0]); return; 
				for (var i=0;i<triggers.length;i++){
					hostNameLower = triggers[i].hosts[0].name.toLowerCase();
					hostHostLower = triggers[i].hosts[0].host.toLowerCase();
					if (((hostNameLower.indexOf(hostNameFieldLower) + 1) || (hostHostLower.indexOf(hostNameFieldLower) + 1)) && (hostStatusChecked == triggers[i].hosts[0].status)) {
						// Изменение описания
						if('update' == typeQueryChecked){
							updateTrigger(auth, triggers[i].triggerid, triggers[i].comments.replace(regExp, newPhrase));
						// Чтение триггеров
						} else{
							if(counter % 2) {
								html += '<tr style="background-color: #eee;">';
							} else {									
								html += '<tr>';
							}
							html += '<td>' + triggers[i].description + '</td>';
							html += '<td>' + arrPriority[triggers[i].priority] + '</td>';
							if(1 == triggers[i].value){
								html += '<td style="color: red">';
							} else {
								html += '<td style="color: green">';
							}
							html += arrState[triggers[i].value] + '</td>';
							html += '<td>' + triggers[i].hosts[0].host + '</td>';
							html += '<td>' + triggers[i].hosts[0].name + '</td>';
							if(0 == triggers[i].hosts[0].proxy_hostid){
								html += '<td>Без прокси</td>';
							} else{
								for(var k=0; k < proxy.length; k++){
									if(triggers[i].hosts[0].proxy_hostid == proxy[k].proxyid){
										html += '<td>' + proxy[k].host + '</td>';
										break;
									}
								}
							}
							html += '<td><a target="_blank" href="https://' + host + '/zabbix/triggers.php?form=update&hostid=' + triggers[i].hosts[0].hostid + '&triggerid=' + triggers[i].triggerid + '">Ссылка на триггер<a/></td>';
							html += '</tr>';
							target.innerHTML = html;
							counter ++;
						}
					}
				}
				if('get' == typeQueryChecked || 0 == triggers.length){
					process.innerHTML += '<p>Всего триггеров: ' + counter + '</p>';
					logout(auth);
				}
			} else {
				process.innerHTML += '<p>Нет ответа</p>';
			}
		}
	};
	
	requestInfo.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestInfo.setRequestHeader('Content-type', 'application/json-rpc');
	requestInfo.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestInfo.send(JSON.stringify(json_triggers_get));
}

// Обновление элементов
function updateTrigger(auth, triggerId, comments) {
	var json_trigger_update = {
		'jsonrpc': '2.0',
		'method': 'trigger.update',
		'params': {
			'triggerid': triggerId,
			'comments': comments,
		},
		'auth': auth,
		'id': 5
	};
	
	process.innerHTML += '<p>Изменение триггера ' + triggerId + '</p>';
	
	counterQuery++;
	
	var requestUpdate = new XMLHttpRequest();
	
	requestUpdate.onreadystatechange = function() {
		if(4 == requestUpdate.readyState) {
			if(200 == requestUpdate.status) {
				process.innerHTML += '<p>Ответ: ' + requestUpdate.responseText + '</p>';
				counterQuery--;
				if(0 == counterQuery) {
					logout(auth);
				}
			} else {
				process.innerHTML += '<p>Нет ответа</p>';
			}
		}
	};
	
	requestUpdate.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestUpdate.setRequestHeader('Content-type', 'application/json-rpc');
	requestUpdate.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestUpdate.send(JSON.stringify(json_trigger_update));
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
				getHostsId(auth);
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

// Получение id узлов
function getHostsId(auth) {
	if('' == hostName.value.trim()) {
		getTriggers(auth);
		return;
	}
	var json_hostsId_get = {
		'jsonrpc': '2.0',
		'method': 'host.get',
		'params': {
			// 'output': 'extend',
			'output': [
				'hostid',
			],
			'search': {
				'name': hostName.value.trim(),
				'host': hostName.value.trim(),
			},
			'searchByAny': true,
		},
		'auth': auth,
		'id': 3
	};
	
	requestHost.onreadystatechange = function() {
		if(4 == requestHost.readyState) {
			if(200 == requestHost.status) {
				var result = JSON.parse(requestHost.responseText).result;
				if(0 != result.length) {
					for(var i=0; i<result.length; i++) {
						hostsId[i] = result[i].hostid;
					}
					getTriggers(auth);
				} else {
					process.innerHTML += '<p style="color: red">Нет узлов с таким именем</p>';
					logout(auth);
				}
			} else {
				process.innerHTML += '<p>Нет ответа</p>';
			}
		}
	};
	
	requestHost.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestHost.setRequestHeader('Content-type', 'application/json-rpc');
	requestHost.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestHost.send(JSON.stringify(json_hostsId_get));
}