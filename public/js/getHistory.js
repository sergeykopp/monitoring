var request = getXmlHttpRequest();
var _history = document.getElementsByName('history')[0];
var fieldHistory = document.getElementsByName('fieldHistory')[0];
var token = document.getElementsByName('_token')[0];

function getHistory(id, a_sync)
{
    request.onreadystatechange = function () {
        // Если запрос ещё не готов
        if (4 != request.readyState)
            return;
        // Если запрос был отменён
        if (0 == request.status)
            return;
        // Если запрос не успешен
        if (200 != request.status) {
            alert("Ошибка запроса " + request.status + " : " + request.statusText);
            return;
        }
        // Если запрос не пуст
        if ('' != request.responseText) {
            _history.value = request.responseText;
            fieldHistory.style.display = '';
        }
        // Если запрос пуст
        else {
			_history.value = 'По данной проблеме нет записей в логах';
            fieldHistory.style.display = '';
        }
    };
    request.open("POST", "/ajax/getHistory", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id=" + id); // Параметры отправки
}