var requestInfo = new XMLHttpRequest();
var requestProxy = new XMLHttpRequest();
var requestHost = new XMLHttpRequest();
var itemName;
var itemStatus;
var itemStatusChecked;
var hostName;
var hostStatus;
var hostStatusChecked;
var typeQuery;
var typeQueryChecked;
var target;
var process;
var proxy;
var hostsId = [];
var warningFieldBlink = 0;
var warningField;
var counterQuery;

window.onload = function() {
    warningField = document.getElementsByClassName('warning')[0];
    itemName = document.getElementsByName('itemName')[0];
    itemStatus = document.getElementsByName('itemStatus');
    hostName = document.getElementsByName('hostName')[0];
    hostStatus = document.getElementsByName('hostStatus');
    typeQuery = document.getElementsByName('typeQuery');
    target = document.getElementsByName('target')[0];
    process = document.getElementsByName('process')[0];

    // При изменении типа запроса, мигать предупреждением о правах на запись
    for(var i=0; i<typeQuery.length; i++){
        typeQuery[i].addEventListener('click', function() {
            if('get' != this.value && 0 == warningFieldBlink){
                warningFieldBlink = setInterval(function(){
                    if('white' == warningField.style.color){
                        warningField.style.color = 'red';
                    } else {
                        warningField.style.color = 'white';
                    }
                }, 500);
            }
            if('get' == this.value) {
                clearInterval(warningFieldBlink);
                warningFieldBlink = 0;
                warningField.style.color = '';
            }
        }, false);
    }
};

// Запрос
function query(auth) {
    proxy = [];
    hostsId = [];
	counterQuery = 0;
    if ('' == itemName.value){
        process.innerHTML = '';
        alert('Не задана фраза для имён элеменов');
        return;
    }
    // Чтение выбранного статуса элементов
    for(var i=0; i<itemStatus.length; i++){
        if(itemStatus[i].checked == true){
            itemStatusChecked = itemStatus[i].value;
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

// Получение элементов
function getItems(auth) {
    var json_items_get = {
        'jsonrpc': '2.0',
        'method': 'item.get',
        'params': {
            // 'output': 'extend',
            'output': [
                'itemid',
                'name',
                'status'
            ],
            'filter': {
                'status': itemStatusChecked
            },
            'search': {
                'name': itemName.value
            },
            'selectHosts': [
                'hostid',
                'host',
                'name',
                'status',
                'proxy_hostid'
            ],
            'sortfield': 'name',
            'sortorder': 'ASC'
        },
        'auth': auth,
        'id': 4
    };

    // Добавление id узлов
    if (0 != hostsId.length) {
        json_items_get.params.hostids = hostsId;
    }

    process.innerHTML += '<p>Получение элементов ...</p>';

    requestInfo.onreadystatechange = function() {
        if(4 == requestInfo.readyState) {
            if(200 == requestInfo.status) {
                var items = JSON.parse(requestInfo.responseText).result;
                var hostNameLower;
                var hostHostLower;
                var hostNameFieldLower = hostName.value.toLowerCase().trim();
                var html = '';
                var counter = 0;
                // target.innerHTML += JSON.stringify(items[0]); return;
                for (var i=0;i<items.length;i++){
                    hostNameLower = items[i].hosts[0].name.toLowerCase();
                    hostHostLower = items[i].hosts[0].host.toLowerCase();
                    if (((hostNameLower.indexOf(hostNameFieldLower) + 1) || (hostHostLower.indexOf(hostNameFieldLower) + 1)) && (hostStatusChecked == items[i].hosts[0].status)) {
                        // Деактивация элементов
                        if('deactivate' == typeQueryChecked){
                            deactivateItem(auth, items[i].itemid);
                        // Активация элементов
                        } else if ('activate' == typeQueryChecked){
                            activateItem(auth, items[i].itemid);
                        }
                        // Чтение элементов
                        else {
                            if(counter % 2) {
                                html += '<tr style="background-color: #eee;">';
                            } else {
                                html += '<tr>';
                            }
                            html += '<td>' + items[i].name + '</td>';
                            html += '<td>' + items[i].hosts[0].host + '</td>';
                            html += '<td>' + items[i].hosts[0].name + '</td>';
                            if(0 == items[i].hosts[0].proxy_hostid){
                                html += '<td>Без прокси</td>';
                            } else{
                                for(var k=0; k < proxy.length; k++){
                                    if(items[i].hosts[0].proxy_hostid == proxy[k].proxyid){
                                        html += '<td>' + proxy[k].host + '</td>';
                                        break;
                                    }
                                }
                            }
                            html += '<td><a target="_blank" href="https://' + host + '/zabbix/items.php?form=update&hostid=' + items[i].hosts[0].hostid + '&itemid=' + items[i].itemid + '">Ссылка на элемент<a/></td>';
                            html += '</tr>';
                            target.innerHTML = html;
                            counter ++;
                        }
                    }
                }
                if('get' == typeQueryChecked || 0 == triggers.length){
                    process.innerHTML += '<p>Всего элементов: ' + counter + '</p>';
                    logout(auth);
                }
            } else {
                process.innerHTML = '<p>Нет ответа</p>';
            }
        }
    };

    requestInfo.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestInfo.setRequestHeader('Content-type', 'application/json-rpc');
    requestInfo.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestInfo.send(JSON.stringify(json_items_get));
}

// Деактивация элемента
function deactivateItem(auth, itemid) {
    var json_item_update = {
        'jsonrpc': '2.0',
        'method': 'item.update',
        'params': {
            'itemid': itemid,
            'status': 1,
        },
        'auth': auth,
        'id': 5
    };

    process.innerHTML += '<p>Изменение элемента ' + itemid + '</p>';
	
	counterQuery++;

    var requestDeactivate = new XMLHttpRequest();

    requestDeactivate.onreadystatechange = function() {
        if(4 == requestDeactivate.readyState) {
            if(200 == requestDeactivate.status) {
                process.innerHTML += '<p>Ответ: ' + requestDeactivate.responseText + '</p>';
				counterQuery--;
				if(0 == counterQuery) {
					logout(auth);
				}
            } else {
                process.innerHTML += '<p>Нет ответа</p>';
            }
        }
    };

    requestDeactivate.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestDeactivate.setRequestHeader('Content-type', 'application/json-rpc');
    requestDeactivate.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestDeactivate.send(JSON.stringify(json_item_update));
}

// Активация элемента
function activateItem(auth, itemid) {
    var json_item_update = {
        'jsonrpc': '2.0',
        'method': 'item.update',
        'params': {
            'itemid': itemid,
            'status': 0,
        },
        'auth': auth,
        'id': 6
    };

    process.innerHTML += '<p>Изменение элемента ' + itemid + '</p>';
	
	counterQuery++;

    var requestActivate = new XMLHttpRequest();

    requestActivate.onreadystatechange = function() {
        if(4 == requestActivate.readyState) {
            if(200 == requestActivate.status) {
                process.innerHTML += '<p>Ответ: ' + requestActivate.responseText + '</p>';
				counterQuery--;
				if(0 == counterQuery) {
					logout(auth);
				}
            } else {
                process.innerHTML += '<p>Нет ответа</p>';
            }
        }
    };

    requestActivate.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestActivate.setRequestHeader('Content-type', 'application/json-rpc');
    requestActivate.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestActivate.send(JSON.stringify(json_item_update));
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
                process.innerHTML = '<p>Нет ответа</p>';
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
        getItems(auth);
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
					getItems(auth);
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