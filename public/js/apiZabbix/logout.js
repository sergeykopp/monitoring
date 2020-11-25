var requestLogout = new XMLHttpRequest();

// Завершение сессии
function logout(auth) {

    var json_logout = {
        'jsonrpc': '2.0',
        'method': 'user.logout',
        'params': {},
        'auth': auth,
        'id': 9
    };

    requestLogout.onreadystatechange = function() {
        if(4 == requestLogout.readyState) {
            process.innerHTML += '<p>Завершение сессии ...</p>';
            if(200 == requestLogout.status) {
                process.innerHTML += '<p>Ответ: ' + requestLogout.responseText + '</p>';
            } else {
                process.innerHTML = '<p>Нет ответа</p>';
            }
        }
    };

    requestLogout.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
    requestLogout.setRequestHeader('Content-type', 'application/json-rpc');
    requestLogout.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
    requestLogout.send(JSON.stringify(json_logout));
}