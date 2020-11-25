<?php

// Мониторинг

Route::match(['get', 'post'], '/', ['uses' => 'MonitoringController@allTroubles', 'as' => 'main']);

Route::get('/actual', ['uses' => 'MonitoringController@actualTroubles', 'as' => 'actual']);

Route::get('/new/{id?}', ['uses' => 'MonitoringController@newTrouble', 'as' => 'new', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/new/{id?}', ['uses' => 'MonitoringController@storeTrouble', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/edit/{id}', ['uses' => 'MonitoringController@editTrouble', 'as' => 'edit', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/edit/{id}', ['uses' => 'MonitoringController@storeTrouble', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/info', ['uses' => 'MonitoringController@getInfo', 'as' => 'info', 'middleware' => ['web','auth']]);

Route::get('/offices', ['uses' => 'OfficeController@offices', 'middleware' => ['web','auth']]);
Route::get('/offices/actual', ['uses' => 'OfficeController@actualOffices', 'middleware' => ['web','auth']]);

Route::get('/office/new', ['uses' => 'OfficeController@newOffice', 'as' => 'newOffice', 'middleware' => ['web','auth']]);
Route::post('/office/new', ['uses' => 'OfficeController@storeOffice', 'middleware' => ['web','auth']]);

Route::get('/office/edit/{id}', ['uses' => 'OfficeController@editOffice', 'as' => 'editOffice', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/office/edit/{id}', ['uses' => 'OfficeController@storeOffice', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/cities', ['uses' => 'CityController@cities', 'middleware' => ['web','auth']]);

Route::get('/city/new', ['uses' => 'CityController@newCity', 'as' => 'newCity', 'middleware' => ['web','auth']]);
Route::post('/city/new', ['uses' => 'CityController@storeCity', 'middleware' => ['web','auth']]);

Route::get('/city/edit/{id}', ['uses' => 'CityController@editCity', 'as' => 'editCity', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/city/edit/{id}', ['uses' => 'CityController@storeCity', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/filials', ['uses' => 'FilialController@filials', 'middleware' => ['web','auth']]);

Route::get('/filial/new', ['uses' => 'FilialController@newFilial', 'as' => 'newFilial', 'middleware' => ['web','auth']]);
Route::post('/filial/new', ['uses' => 'FilialController@storeFilial', 'middleware' => ['web','auth']]);

Route::get('/filial/edit/{id}', ['uses' => 'FilialController@editFilial', 'as' => 'editFilial', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/filial/edit/{id}', ['uses' => 'FilialController@storeFilial', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/directorates', ['uses' => 'DirectorateController@directorates', 'middleware' => ['web','auth']]);

Route::get('/directorate/new', ['uses' => 'DirectorateController@newDirectorate', 'as' => 'newDirectorate', 'middleware' => ['web','auth']]);
Route::post('/directorate/new', ['uses' => 'DirectorateController@storeDirectorate', 'middleware' => ['web','auth']]);

Route::get('/directorate/edit/{id}', ['uses' => 'DirectorateController@editDirectorate', 'as' => 'editDirectorate', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/directorate/edit/{id}', ['uses' => 'DirectorateController@storeDirectorate', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/users', ['uses' => 'UserController@users', 'middleware' => ['web','auth']]);
Route::get('/withRoles', ['uses' => 'UserController@usersWithRoles', 'middleware' => ['web','auth']]);

Route::get('/user/edit/{id}', ['uses' => 'UserController@editUser', 'as' => 'editUser', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/user/edit/{id}', ['uses' => 'UserController@storeUser', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/sources', ['uses' => 'SourceController@sources', 'middleware' => ['web','auth']]);
Route::get('/sources/actual', ['uses' => 'SourceController@actualSources', 'middleware' => ['web','auth']]);

Route::get('/source/new', ['uses' => 'SourceController@newSource', 'as' => 'newSource', 'middleware' => ['web','auth']]);
Route::post('/source/new', ['uses' => 'SourceController@storeSource', 'middleware' => ['web','auth']]);

Route::get('/source/edit/{id}', ['uses' => 'SourceController@editSource', 'as' => 'editSource', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/source/edit/{id}', ['uses' => 'SourceController@storeSource', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/groupsServices', ['uses' => 'GroupServicesController@groupsServices', 'middleware' => ['web','auth']]);

Route::get('/groupServices/new', ['uses' => 'GroupServicesController@newGroupServices', 'as' => 'newGroupServices', 'middleware' => ['web','auth']]);
Route::post('/groupServices/new', ['uses' => 'GroupServicesController@storeGroupServices', 'middleware' => ['web','auth']]);

Route::get('/groupServices/edit/{id}', ['uses' => 'GroupServicesController@editGroupServices', 'as' => 'editGroupServices', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/groupServices/edit/{id}', ['uses' => 'GroupServicesController@storeGroupServices', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

Route::get('/services', ['uses' => 'ServiceController@services', 'middleware' => ['web','auth']]);
Route::get('/services/actual', ['uses' => 'ServiceController@actualServices', 'middleware' => ['web','auth']]);

Route::get('/service/new', ['uses' => 'ServiceController@newService', 'as' => 'newService', 'middleware' => ['web','auth']]);
Route::post('/service/new', ['uses' => 'ServiceController@storeService', 'middleware' => ['web','auth']]);

Route::get('/service/edit/{id}', ['uses' => 'ServiceController@editService', 'as' => 'editService', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);
Route::post('/service/edit/{id}', ['uses' => 'ServiceController@storeService', 'middleware' => ['web','auth']])->where(['id' => '[0-9]+']);

// Ajax запросы

Route::post('/ajax/directorate', ['uses' => 'AjaxController@getFilials']);
Route::post('/ajax/filial', ['uses' => 'AjaxController@getCities']);
Route::post('/ajax/city', ['uses' => 'AjaxController@getOffices']);
Route::post('/ajax/office', ['uses' => 'AjaxController@getInfoOffice']);
Route::post('/ajax/phrase', ['uses' => 'AjaxController@getPhrases']);
Route::post('/ajax/phraseBFKO', ['uses' => 'AjaxController@getPhrasesBFKO']);
Route::post('/ajax/fullText', ['uses' => 'AjaxController@getFullText']);
Route::post('/ajax/getNow', ['uses' => 'AjaxController@getNow']);
Route::post('/ajax/getHistory', ['uses' => 'AjaxController@getHistory']);

// Отчётность

Route::group(['prefix' => '/reports'], function () {

    Route::match(['get', 'post'], '/availability', ['uses' => 'ReportsController@availability', 'as' => 'availability']);
    Route::match(['get', 'post'], '/risk', ['uses' => 'ReportsController@forOperRisk', 'as' => 'risk']);
    Route::match(['get', 'post'], '/bfko', ['uses' => 'ReportsController@troublesBFKO', 'as' => 'bfko']);

});

// Администрирование

Route::group(['prefix' => '/admin'], function () {

    Route::get('/', ['uses' => 'AdministrationController@channelsWithoutOffice', 'as' => 'admin']);

    Route::match(['get', 'post'], '/backup', ['uses' => 'AdministrationController@backup', 'as' => 'backup']);

});

// Аутентификация

Auth::routes();

// Сервис-каталог

Route::group(['prefix' => '/serviceCatalog'], function () {

    Route::match(['get', 'post'], '/', ['uses' => 'ServiceCatalogController@allConfigurationItems', 'as' => 'allConfigurationItems']);

});

// API Zabbix

Route::group(['prefix' => '/apiZabbix', 'middleware' => ['web','auth']], function () {

    Route::get('/getItems', function(){
        return view('apiZabbix.getItems');
    });
	
	Route::get('/getHosts', function(){
        return view('apiZabbix.getHosts');
    });

    Route::get('/getTriggersByDescription', function(){
        return view('apiZabbix.getTriggersByDescription');
    });
	
	Route::get('/getOrUpdateTriggersByComments', function(){
        return view('apiZabbix.getOrUpdateTriggersByComments');
    });

    Route::get('/getTriggersInvalidProviders', function(){
        return view('apiZabbix.getTriggersInvalidProviders');
    });
	
	Route::get('/getTriggersLossesDependency', function(){
        return view('apiZabbix.getTriggersLossesDependency');
    });
	
	Route::get('/getTriggersChannelsDependency', function(){
        return view('apiZabbix.getTriggersChannelsDependency');
    });
	
	Route::get('/getTriggersRoutersDependency', function(){
        return view('apiZabbix.getTriggersRoutersDependency');
    });
	
	Route::get('/getTriggersMoreThanADay', function(){
        return view('apiZabbix.getTriggersMoreThanADay');
    });

});

// Zabbix

Route::group(['prefix' => '/zabbix', 'middleware' => ['web','auth']], function () {

    Route::get('/getNotSupportedItems', ['uses' => 'ZabbixController@getNotSupportedItems', 'as' => 'notSupportedItems']);

});