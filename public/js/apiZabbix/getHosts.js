var requestInfo = new XMLHttpRequest();
var requestProxy = new XMLHttpRequest();
var proxyName;
var target;
var process;
var queryType = 'proxy';

window.onload = function() {
    proxyName = document.getElementsByName('proxyName')[0];
    target = document.getElementsByName('target')[0];
    process = document.getElementsByName('process')[0];
    auth();
};

// Запрос
function query(auth) {
    if('proxy' == queryType) {
        getProxy(auth);
        queryType = 'hosts';
    } else {
        target.innerHTML = '';
        getHosts(auth);
    }
}

// Получение элементов
function getHosts(auth) {
    var json_hosts_get = {
		'jsonrpc': '2.0',
		'method': 'host.get',
		'params': {
			// 'output': 'extend',
			'output': [
				'host',
				'name',
				'status',
				'proxy_hostid'
			],
			// 'filter': {
				// 'status': 0,
				// 'proxy_hostid': proxyName.options[proxyName.selectedIndex].value
			// },
			'selectInterfaces' : [
				'ip'
			],
			'sortfield': [
				'status',
				'name',
			],
			'sortorder': 'ASC'
		},
		'auth': auth,
		'id': 2
	};

    process.innerHTML += '<p>Получение узлов ...</p>';

    requestInfo.onreadystatechange = function() {
        if(4 == requestInfo.readyState) {
            if(200 == requestInfo.status) {
                var hosts = JSON.parse(requestInfo.responseText).result;
                var html = '';
                var proxyId = proxyName.options[proxyName.selectedIndex].value;
                counter = 0;
                // target.innerHTML += JSON.stringify(hosts[0]);
                for (var i=0; i<hosts.length; i++){
                    if(proxyId == hosts[i].proxy_hostid) {
                        if(counter % 2) {
                            html += '<tr style="background-color: #eee;">';
                        } else {
                            html += '<tr>';
                        }
						html += '<td>' + hosts[i].interfaces[0].ip + '</td>';
						if(0 == hosts[i].status) {
							html += '<td>Активирован</td>';
						} else {
							html += '<td>Деактивирован</td>';
						}
                        html += '<td>' + hosts[i].host + '</td>';
                        html += '<td>' + hosts[i].name + '</td>';
                        html += '<td><a target="_blank" href="https://' + host + '/zabbix/hosts.php?form=update&hostid=' + hosts[i].hostid + '">Ссылка на узел<a/></td>';
                        html += '</tr>';
                        target.innerHTML = html;
                        counter++;
                    }
                }
                process.innerHTML += '<p>Всего узлов: ' + counter + '</p>';
                logout(auth);
            } else {
                process.innerHTML = '<p>Нет ответа</p>';
            }
        }
    };

    requestInfo.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestInfo.setRequestHeader('Content-type', 'application/json-rpc');
    requestInfo.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestInfo.send(JSON.stringify(json_hosts_get));
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
            'sortfield': 'host',
            'sortorder': 'ASC'
        },
        'auth': auth,
        'id': 2
    };

    process.innerHTML += '<p>Получение прокси-хостов ...</p>';

    requestProxy.onreadystatechange = function() {
        if(4 == requestProxy.readyState) {
            if(200 == requestProxy.status) {
                var proxy = JSON.parse(requestProxy.responseText).result;
                proxyName.innerHTML = '<option value="0" selected="selected">Без прокси</option>';
                for(var i=0; i<proxy.length; i++){
                    proxyName.innerHTML += '<option value="' + proxy[i].proxyid + '">' + proxy[i].host + '</option>';
                }
                process.innerHTML += '<p>Всего прокси-хостов: ' + proxy.length + '</p>';
				getHosts(auth);
            } else {
                process.innerHTML = '<p>Нет ответа</p>';
            }
        }
    };

    requestProxy.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestProxy.setRequestHeader('Content-type', 'application/json-rpc');
    requestProxy.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestProxy.send(JSON.stringify(json_proxy_get));
}