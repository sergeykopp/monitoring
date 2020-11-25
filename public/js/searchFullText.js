var request = getXmlHttpRequest();
var token = document.getElementsByName('_token')[0];

// ajax-запрос для замены короткого сообщения полным текстом
function search_ajax_full_text(obj, id, field) {
    request.onreadystatechange = function () {
        // Если запрос ещё не готов
        if (request.readyState != 4)
            return;
        // Если запрос был отменён
        if (request.status == 0)
            return;
        // Если запрос не успешен
        if (request.status != 200) {
            alert("Ошибка запроса " + request.status + " : " + request.statusText);
            return;
        }
        if (request.responseText != '') {
            obj.innerHTML = request.responseText;
        } else{
            return;
        }
    };
    request.open("POST", "/ajax/fullText", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id=" + id + "&field=" + field); // Параметры отправки
}