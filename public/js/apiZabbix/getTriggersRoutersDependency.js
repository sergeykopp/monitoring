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
                'status'
            ],
            'filter': {
                'status': 0
            },
            'search': {
                'description': 'недоступен маршрутизатор'
            },
            'selectDependencies' : [
                'description'
            ],
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
                // var num1, num2;
                // var regExp = /\D/g;
                for (var i=0;i<triggers.length;i++){
                    // num1 = triggers[i].description.replace(regExp, "");
                    // num2 = triggers[i].dependencies[0].description.replace(regExp, "");
                    if ((0 == triggers[i].hosts[0].status) && ('Недоступен маршрутизатор' == triggers[i].description) && ((!triggers[i].dependencies[0]) || ((triggers[i].dependencies[0]) && (triggers[i].dependencies[0].description != 'Недоступен объект')))) {
                        if(counter % 2) {
                            html += '<tr style="background-color: #eee;">';
                        } else {
                            html += '<tr>';
                        }
                        html += '<td>' + triggers[i].description + '</td>';
                        if(triggers[i].dependencies[0]) {
                            html += '<td>' + triggers[i].dependencies[0].description + '</td>';
                        } else {
                            html += '<td></td>';
                        }
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