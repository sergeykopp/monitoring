var request = getXmlHttpRequest();
var fieldFinishedAt = document.getElementsByName('finished_at')[0];
var token = document.getElementsByName('_token')[0];

function getNow(a_sync)
{
    request.onreadystatechange = function() {
        // Если запрос ещё не готов
        if(4 != request.readyState)
            return;
        // Если запрос был отменён
        if(0 == request.status)
            return;
        // Если запрос не успешен
        if(200 != request.status){
            alert("Ошибка запроса " + request.status + " : " + request.statusText);
            return;
        }
        // Если запрос не пуст
        if('' != request.responseText){
            fieldFinishedAt.value = request.responseText;
        }
        // Если запрос пуст
        else{
            //
        }
    };
    request.open("POST", "/ajax/getNow", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value); // Параметры отправки
}

// Уменьшение на 4 часа полей с датой
function minus4Hours(fieldName)
{
    var field = document.getElementsByName(fieldName)[0];
    var datetime = field.value;
    var regExp = /([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{1,2})\:([0-9]{1,2})/;
    if(regExp.test(datetime)){
        datetime = regExp.exec(datetime);
        var date = new Date(datetime[3], datetime[2] * 1 - 1, datetime[1], datetime[4], datetime[5], 0, 0);
        date.setHours(date.getHours() - 4);
        field.value = (10 > date.getDate() ? '0' + date.getDate() : date.getDate()) + '.' +
            (10 > (date.getMonth() * 1 + 1) ? '0' + (date.getMonth()  * 1 + 1) : (date.getMonth()  * 1 + 1)) + '.' + date.getFullYear() + ' ' +
            (10 > date.getHours() ? '0' + date.getHours() : date.getHours()) + ':' +
            (10 > date.getMinutes() ? '0' + date.getMinutes() : date.getMinutes());
    }
}