<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Auth Routes
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Auth::routes();

Route::group([
    'middleware' => ['auth'],
], function () {


    Route::get('/', 'ToolController@home');

    //region Users / Roles / Permissions
    // User Routes

    Route::get('/profile', 'UserController@profile');
    Route::resource('/users', 'UserController');
    Route::put('/changeStatus/{user}', 'UserController@changeStatus');
    Route::put('/updatePicture', 'UserController@updatePicture');
    Route::get('/getUsers', 'UserController@getUsers');

// Role Routes
    Route::resource('/roles', 'RoleController');
    Route::get('/getRoles', 'RoleController@getRoles');
    Route::get('/getRolePermissions/{role}', 'RoleController@getPermissions');
    Route::post('/assignPermissionRole', 'RoleController@assignPermissionRole');

// Permission Routes
    Route::resource('/permissions', 'PermissionController');
    Route::get('/getPermissions', 'PermissionController@getPermissions');
    Route::get('/getPermissionRoles/{permission}', 'PermissionController@getRoles');
    //endregion

    Route::get('/unauthorized', 'ToolController@unauthorized');

    Route::get('/dashboard', 'StatsController@dashboard')->name('dashboard');
    Route::get('/agences', 'StatsController@dashboard')->name('agence.index');
    Route::get('/agents', 'StatsController@dashboard')->name('agent.index');

    Route::get('/all-stats', 'StatsController@index')->name('stats.index');
    Route::post('/stats/get-stats', 'StatsController@getStats')->name('stats.get-stats');
    Route::get('/stats/filter', 'ToolController@getFilterAllStats')->name('stats.get-filter');

    Route::get('/agents/all-data', 'AgentController@allData')->name('agents.viewData');
    Route::get('/agents/get-data', 'AgentController@getData')->name('agents.getData');



    Route::get('/dates', 'StatsController@getDates');

    Route::get('/stats/filter/{column}', 'StatsController@filterList');
    Route::get('/agences/list', 'StatsController@getAgencies')->name('agence.list');
    Route::get('/agents/list', 'StatsController@getAgents')->name('agent.list');



    //region DASHBOARD
    Route::get('/regions/details/groupement', 'StatsController@getRegionsByGrpCall'); // column = key_groupement value // // PERCENT
    Route::get('/regions/details/groupement/columns', 'StatsController@getRegionsByGrpCallColumns'); // column = key_groupement value // // PERCENT

    Route::get('/regions/{callResult}', 'StatsController@getRegions'); // column = Groupement                                       // PERCENT
    Route::get('/regions/columns/{callResult}', 'StatsController@getRegionsColumn');

    Route::get('/folders/{callResult}', 'StatsController@getFolders'); // column = Groupement                                       // SUM
    Route::get('/folders/columns/{callResult}', 'StatsController@getFoldersColumn');

    Route::get('/regionsCallState/{column}', 'StatsController@getRegionsCallState'); // column = Nom_Region / Date_Heure_Note_Semaine   // SUM
    Route::get('/regionsCallState/columns/{column}', 'StatsController@getRegionsCallStateColumn');

    Route::get('/clientsByCallState/{callResult}', 'StatsController@getClientsByCallState'); // value = Injoignable / Joignable         // PERCENT
    Route::get('/clientsByCallState/columns/{callResult}', 'StatsController@getClientsByCallStateColumn');

    Route::get('/nonValidatedFolders/{column}', 'StatsController@getNonValidatedFolders'); // column = Code_Intervention / Code_Type_Intervention   // PERCENT
    Route::get('/nonValidatedFolders/columns/{column}', 'StatsController@getNonValidatedFoldersColumn');

    Route::get('/clientsByPerimeter', 'StatsController@getClientsByPerimeter');                                                          // SUM
    Route::get('/clientsByPerimeter/columns', 'StatsController@getClientsByPerimeterColumn');

    Route::get('clientsWithCallStates', 'StatsController@getClientsWithCallStates'); // value = Injoignable + Joignable  => Appel Prealable
    Route::get('clientsWithCallStates/columns', 'StatsController@getClientsWithCallStatesColumn');
    //endregion


    //region FILTERS PAGES
    Route::get('/dashboard/{filter}', 'FilterController@dashboard_filter');

    Route::get('Cloturetech', 'StatsController@getCloturetech');
    Route::get('Cloturetech/columns', 'StatsController@getCloturetechColumn');

    Route::get('GlobalDelay', 'StatsController@getGlobalDelay');
    Route::get('GlobalDelay/columns', 'StatsController@getGlobalDelayColumn');

    Route::get('ProcessingDelay', 'StatsController@getProcessingDelay');
    Route::get('ProcessingDelay/columns', 'StatsController@getProcessingDelayColumn');

    Route::get('TypeIntervention', 'StatsController@getTypeIntervention');
    Route::get('TypeIntervention/columns', 'StatsController@getTypeInterventionColumn');
    Route::get('TypeIntervention/details', 'StatsController@getTypeInterventionGrpCall');
    Route::get('TypeIntervention/details/columns', 'StatsController@getTypeInterventionColumnGrpCall');

    Route::get('ValTypeIntervention', 'StatsController@getValTypeIntervention');
    Route::get('ValTypeIntervention/columns', 'StatsController@getValTypeInterventionColumn');
    Route::get('ValTypeIntervention/details', 'StatsController@getValTypeInterventionGrpCall');
    Route::get('ValTypeIntervention/details/columns', 'StatsController@getValTypeInterventionColumnGrpCall');

    Route::get('RepTypeIntervention', 'StatsController@getRepTypeIntervention');
    Route::get('RepTypeIntervention/columns', 'StatsController@getRepTypeInterventionColumn');
    Route::get('RepTypeIntervention/details', 'StatsController@getRepTypeInterventionGrpCall');
    Route::get('RepTypeIntervention/details/columns', 'StatsController@getRepTypeInterventionColumnGrpCall');

    Route::get('RepJoiDepartement', 'StatsController@getRepJoiDepartement');
    Route::get('RepJoiDepartement/columns', 'StatsController@getRepJoiDepartementColumn');
    Route::get('RepJoiDepartement/details', 'StatsController@getRepJoiDepartementGrpCall');
    Route::get('RepJoiDepartement/details/columns', 'StatsController@getRepJoiDepartementColumnGrpCall');

    Route::get('RepJoiAutreDepartement', 'StatsController@getRepJoiAutreDepartement');
    Route::get('RepJoiAutreDepartement/columns', 'StatsController@getRepJoiAutreDepartementColumn');
    Route::get('RepJoiAutreDepartement/details', 'StatsController@getRepJoiAutreDepartementGrpCall');
    Route::get('RepJoiAutreDepartement/details/columns', 'StatsController@getRepJoiAutreDepartementColumnGrpCall');


    Route::get('/globalView', 'StatsController@getGlobalView');
    Route::get('/globalView/columns', 'StatsController@getGlobalViewColumns');

    Route::get('/globalView/details', 'StatsController@getGlobalViewDetails');
    Route::get('/globalView/details/columns', 'StatsController@getGlobalViewDetailsColumns');

    Route::get('AgentProd', 'StatsController@getAgentProd');
    Route::get('AgentProd/columns', 'StatsController@getAgentProdColumn');
    //endregion


    //region Import / Export
    Route::get('/import/agents', 'AgentController@importView')->name('agents.importView');
    Route::post('/import/agents', 'AgentController@import')->name('agents.import');

    Route::get('/import/stats', 'StatsController@import')->name('stats.importView');
    Route::post('/import/stats', 'StatsController@importStats')->name('stats.import');

    Route::get('/import/data/count', 'ToolController@getInsertedData');
    Route::get('/import/status/edit/{flag}', 'ToolController@editImportingStatus');

    Route::get('Export/ExportXls', 'StatsController@exportXls')->name('ExportXls');
    Route::get('Export/agentProdExport', 'StatsController@agentProdExport')->name('agentProdExport');
    //endregion agentProdExport

    Route::get('/user/filter', 'FilterController@getUserFilter');
    Route::post('/user/filter', 'FilterController@saveUserFilter');




});
