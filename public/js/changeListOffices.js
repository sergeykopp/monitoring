var request = getXmlHttpRequest();
var fieldDirectorate = document.getElementsByName('id_directorate')[0];
var fieldFilial = document.getElementsByName('id_filial')[0];
var fieldCity = document.getElementsByName('id_city')[0];
var fieldOffice = document.getElementsByName('id_office')[0];
var fieldAddress = document.getElementsByName('address')[0];
var buttonEditOffice = document.getElementsByName('buttonEditOffice')[0];
//var fieldNotes = document.getElementsByName('notes')[0];
var fieldCatalogueCities = document.getElementsByName('catalogue_cities')[0];
var fieldCatalogueOffices = document.getElementsByName('catalogue_offices')[0];
var token = document.getElementsByName('_token')[0];

function changeDirectorate(a_sync)
{
    var id_directorate = fieldDirectorate.options[fieldDirectorate.selectedIndex].getAttribute('value');
    fieldCity.innerHTML = '';
    fieldCity.disabled = true;
    fieldOffice.innerHTML = '';
    fieldOffice.disabled = true;
    buttonEditOffice.style.display = 'none';
    fieldAddress.innerHTML = '';
    //fieldNotes.innerHTML = '';
    if('' == id_directorate){
        fieldFilial.innerHTML = '';
        fieldFilial.disabled = true;
        return;
    }
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
            fieldFilial.innerHTML = request.responseText;
            fieldFilial.disabled = false;
        }
        // Если запрос пуст
        else{
            //
        }
    };
    request.open("POST", "/ajax/directorate", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id_directorate=" + id_directorate); // Параметры отправки
}

function changeFilial(a_sync)
{
    var id_filial = fieldFilial.options[fieldFilial.selectedIndex].getAttribute('value');
    fieldOffice.innerHTML = '';
    fieldOffice.disabled = true;
    buttonEditOffice.style.display = 'none';
    fieldAddress.innerHTML = '';
    //fieldNotes.innerHTML = '';
    if('' == id_filial){
        fieldCity.innerHTML = '';
        fieldCity.disabled = true;
        return;
    }
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
            //ajaxList.style.display = '';
            fieldCity.innerHTML = request.responseText;
            fieldCity.disabled = false;
        }
        // Если запрос пуст
        else{
            //
        }
    };
    request.open("POST", "/ajax/filial", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id_filial=" + id_filial); // Параметры отправки
}

function changeCity(a_sync)
{
    var id_city = fieldCity.options[fieldCity.selectedIndex].getAttribute('value');
    buttonEditOffice.style.display = 'none';
    fieldAddress.innerHTML = '';
    //fieldNotes.innerHTML = '';
    if('' == id_city){
        fieldOffice.innerHTML = '';
        fieldOffice.disabled = true;
        return;
    }
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
            //ajaxList.style.display = '';
            fieldOffice.innerHTML = request.responseText;
            fieldOffice.disabled = false;
        }
        // Если запрос пуст
        else{
            //
        }
    };
    request.open("POST", "/ajax/city", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id_city=" + id_city); // Параметры отправки
}

function changeOffice(a_sync)
{
    var id_office = fieldOffice.options[fieldOffice.selectedIndex].getAttribute('value');
    if('' == id_office){
        buttonEditOffice.style.display = 'none';
        fieldAddress.innerHTML = '';
        //fieldNotes.innerHTML = '';
        return;
    }
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
            var result = JSON.parse(request.responseText);
            buttonEditOffice.setAttribute('onclick', 'window.open("/office/edit/' + result.id + '","_blank")');
            buttonEditOffice.style.display = '';
            fieldAddress.innerHTML = 'Адрес: ' + result.address;
            //fieldNotes.innerHTML = result.notes;
        }
        // Если запрос пуст
        else{
            //
        }
    };
    request.open("POST", "/ajax/office", a_sync);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Эмуляция отправки формы для заполнения массива POST
    request.send("_token=" + token.value + "&id_office=" + id_office); // Параметры отправки
}

function changeCatalogueOffices()
{
    var id_office = fieldCatalogueOffices.options[fieldCatalogueOffices.selectedIndex].getAttribute('value');
    if('' == id_office){
        return;
    }
    var id_directorate = fieldCatalogueOffices.options[fieldCatalogueOffices.selectedIndex].getAttribute('id_directorate');
    var id_filial = fieldCatalogueOffices.options[fieldCatalogueOffices.selectedIndex].getAttribute('id_filial');
    var id_city = fieldCatalogueOffices.options[fieldCatalogueOffices.selectedIndex].getAttribute('id_city');
    var fieldDirectorate = document.getElementsByName('id_directorate')[0];
    var fieldFilial = document.getElementsByName('id_filial')[0];
    var fieldCity = document.getElementsByName('id_city')[0];
    var fieldOffice = document.getElementsByName('id_office')[0];
    for(var i=0; i<fieldDirectorate.length; i++){
        if(id_directorate == fieldDirectorate[i].getAttribute('value')){
            fieldDirectorate.selectedIndex = i;
            changeDirectorate(false);
            break;
        }
    }
    for(var i=0; i<fieldFilial.length; i++){
        if(id_filial == fieldFilial[i].getAttribute('value')){
            fieldFilial.selectedIndex = i;
            changeFilial(false);
            break;
        }
    }
    for(var i=0; i<fieldCity.length; i++){
        if(id_city == fieldCity[i].getAttribute('value')){
            fieldCity.selectedIndex = i;
            changeCity(false);
            break;
        }
    }
    for(var i=0; i<fieldOffice.length; i++){
        if(id_office == fieldOffice[i].getAttribute('value')){
            fieldOffice.selectedIndex = i;
            break;
        }
    }
    fieldCatalogueCities.selectedIndex = 0;
    changeOffice(false);
}

function changeCatalogueCities()
{
    var id_city = fieldCatalogueCities.options[fieldCatalogueCities.selectedIndex].getAttribute('value');
    if('' == id_city){
        return;
    }
    var id_directorate = fieldCatalogueCities.options[fieldCatalogueCities.selectedIndex].getAttribute('id_directorate');
    var id_filial = fieldCatalogueCities.options[fieldCatalogueCities.selectedIndex].getAttribute('id_filial');
    var fieldDirectorate = document.getElementsByName('id_directorate')[0];
    var fieldFilial = document.getElementsByName('id_filial')[0];
    var fieldCity = document.getElementsByName('id_city')[0];
    for(var i=0; i<fieldDirectorate.length; i++){
        if(id_directorate == fieldDirectorate[i].getAttribute('value')){
            fieldDirectorate.selectedIndex = i;
            changeDirectorate(false);
            break;
        }
    }
    for(var i=0; i<fieldFilial.length; i++){
        if(id_filial == fieldFilial[i].getAttribute('value')){
            fieldFilial.selectedIndex = i;
            changeFilial(false);
            break;
        }
    }
    for(var i=0; i<fieldCity.length; i++){
        if(id_city == fieldCity[i].getAttribute('value')){
            fieldCity.selectedIndex = i;
            changeCity(false);
            break;
        }
    }
    fieldCatalogueOffices.selectedIndex = 0;
}