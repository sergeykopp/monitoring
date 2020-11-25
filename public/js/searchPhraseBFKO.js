var request = getXmlHttpRequest();
var token = document.getElementsByName('_token')[0];

// ajax-запрос для поиска фразы
function search_ajax(phrase) {
    var fieldSearch = document.getElementById('fieldSearch');
    var ajaxList = document.getElementById("ajaxList");
    ajaxList.style.bottom = fieldSearch.offsetHeight + 'px';
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
        // Если запрос не пуст
        if (request.responseText != '') {
            ajaxList.style.display = '';
            ajaxList.innerHTML = request.responseText;
        }
        // Если запрос пуст
        else {
            ajaxList.style.display = 'none';
        }
    };
    request.open("POST", "/ajax/phraseBFKO", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&searchPhrase=" + phrase); // Параметры отправки
}

function abort_ajax() {
    request.abort();
}

// Перемещение по списку ajax-запроса
function movingAjaxList(direction) {
    var ajaxList = document.getElementById("ajaxList");
    var spans = ajaxList.getElementsByTagName('span');
    if (spans.length > 0) {
        var search = document.getElementsByName('searchPhrase')[0];
        var isSelected = false;
        for (var i = 0; i < spans.length; i++) {
            if (spans[i].style.backgroundColor != '') {
                isSelected = true;
                spans[i].style.backgroundColor = '';
                if (direction == 'up') {
                    if (i == 0) {
                        spans[spans.length - 1].style.backgroundColor = '#6CAEDF';
                        search.value = spans[spans.length - 1].firstChild.nodeValue;
                    }
                    else {
                        spans[i - 1].style.backgroundColor = '#6CAEDF';
                        search.value = spans[i - 1].firstChild.nodeValue;
                    }
                }
                else if (direction == 'down') {
                    if (i == spans.length - 1) {
                        spans[0].style.backgroundColor = '#6CAEDF';
                        search.value = spans[0].firstChild.nodeValue;
                    }
                    else {
                        spans[i + 1].style.backgroundColor = '#6CAEDF';
                        search.value = spans[i + 1].firstChild.nodeValue;
                    }
                }
                break;
            }
        }
        if (isSelected == false) {
            if (direction == 'up') {
                spans[spans.length - 1].style.backgroundColor = '#6CAEDF';
                search.value = spans[spans.length - 1].firstChild.nodeValue;
            }
            else if (direction == 'down') {
                spans[0].style.backgroundColor = '#6CAEDF';
                search.value = spans[0].firstChild.nodeValue;
            }
        }
    }
}

// Очистка стиля списка ajax-запроса
function clearStyleAjax() {
    var ajaxList = document.getElementById("ajaxList");
    var spans = ajaxList.getElementsByTagName('span');
    for (var i = 0; i < spans.length; i++)
        spans[i].style.backgroundColor = '';
}