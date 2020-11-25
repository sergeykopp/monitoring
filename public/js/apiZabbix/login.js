var requestAuth = new XMLHttpRequest();

// Авторизация и получение ключа
function auth() {
    target.innerHTML = '';
    process.innerHTML = '';
	var json_login = {
		'jsonrpc': '2.0',
		'method': 'user.login',
		'params': {
			'user': user,
			'password': password,
		},
		'auth': null,
		'id': 1
	};

	requestAuth.onreadystatechange = function() {
		if(4 == requestAuth.readyState) {
			process.innerHTML = '<p>Авторизация ...</p>';
			if(200 == requestAuth.status) {
				process.innerHTML += '<p>Ответ: ' + requestAuth.responseText + '</p>';
				query(JSON.parse(requestAuth.responseText).result);
			} else {
				process.innerHTML += '<p>Нет ответа</p>';
			}
		}
	};

	requestAuth.open('post', 'http://' + host + '/nondomain/api_jsonrpc.php', true);
	requestAuth.setRequestHeader('Content-type', 'application/json-rpc');
	requestAuth.setRequestHeader('Authorization', 'Basic QUJTbW9uaXRvcmluZzpBQlNtb25pdG9yaW5n');
	requestAuth.send(JSON.stringify(json_login));
}