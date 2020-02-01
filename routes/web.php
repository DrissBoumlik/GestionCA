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

//Auth::routes();
// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');

// Auth Routes
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Auth::routes();

Route::group([
   'middleware' => ['auth'],
], function () {
    //Route::get('/', 'HomeController@dashboard')->name('home');
//Route::get('/_dashboard', 'HomeController@dashboard')->name('home');
    Route::get('/', 'ToolController@home');

// User Routes
//Route::get('/users/create', 'UserController@create');
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

    Route::get('/unauthorized', 'ToolController@unauthorized');

    Route::get('/dashboard', 'StatsController@dashboard')->name('dashboard');

    Route::get('/all-stats', 'StatsController@index')->name('stats.index');
    Route::post('/stats/get-stats', 'StatsController@getStats')->name('stats.get-stats');
//Route::post('/getRegionsByDates', 'StatsController@getRegionsByDates');
//Route::post('/getNonValidatedFoldersByCodeByDates', 'StatsController@getNonValidatedFoldersByCodeByDates');
//Route::post('/getNonValidatedFoldersByTypeByDates', 'StatsController@getNonValidatedFoldersByTypeByDates');
//Route::post('/getClientsByCallStateJoiByDates', 'StatsController@getClientsByCallStateJoiByDates');
//Route::post('/getClientsByCallStateInjByDates', 'StatsController@getClientsByCallStateInjByDates');

//Route::get('/getDates', 'StatsController@getDates');
    Route::get('/dates', 'StatsController@getDates');
    Route::get('/agences/dates', 'StatsController@getDates');
//Route::get('/agences/filter/{column}', 'AgenceController@filterList');
    Route::get('/agences/regions/{callResult}', 'StatsController@getRegions');
    Route::get('/agences/regions/columns/{callResult}', 'StatsController@getRegionsColumn');
    Route::get('/agences/regionsCallState/{column}', 'StatsController@getRegionsCallState'); // Nom_Region / Date_Heure_Note_Semaine
    Route::get('/agences/regionsCallState/columns/{column}', 'StatsController@getRegionsCallStateColumn');

    Route::get('/agences/nonValidatedFolders/{column}', 'StatsController@getNonValidatedFolders'); // Code_Intervention / Code_Type_Intervention
    Route::get('/agences/nonValidatedFolders/columns/{column}', 'StatsController@getNonValidatedFoldersColumn');

    Route::get('/agences/clientsByCallState/{callResult}', 'StatsController@getClientsByCallState'); // Injoignable / Joignable
    Route::get('/agences/clientsByCallState/columns/{callResult}', 'StatsController@getClientsByCallStateColumn');
//Route::get('/agences/list', 'AgenceController@getAgencies')->name('agence.list');
    Route::get('/agences', 'StatsController@dashboard')->name('agence.index');

    Route::get('/agents/dates', 'StatsController@getDates');
//Route::get('/agents/filter/{column}', 'AgentController@filterList');
    Route::get('/agents/regions/{callResult}', 'StatsController@getRegions');
    Route::get('/agents/regions/columns/{callResult}', 'StatsController@getRegionsColumn');
    Route::get('/agents/regionsCallState/{column}', 'StatsController@getRegionsCallState'); // Nom_Region / Date_Heure_Note_Semaine
    Route::get('/agents/regionsCallState/columns/{column}', 'StatsController@getRegionsCallStateColumn');

    Route::get('/agents/nonValidatedFolders/{column}', 'StatsController@getNonValidatedFolders'); // Code_Intervention / Code_Type_Intervention
    Route::get('/agents/nonValidatedFolders/columns/{column}', 'StatsController@getNonValidatedFoldersColumn');

    Route::get('/agents/clientsByCallState/{callResult}', 'StatsController@getClientsByCallState'); // Injoignable / Joignable
    Route::get('/agents/clientsByCallState/columns/{callResult}', 'StatsController@getClientsByCallStateColumn');
//Route::get('/agents/list', 'AgentController@getAgencies')->name('agent.list');
    Route::get('/agents', 'StatsController@dashboard')->name('agent.index');
//Route::get('/getRegions', 'StatsController@getRegions');



    Route::get('/stats/filter/{column}', 'StatsController@filterList');
    Route::get('/agences/list', 'StatsController@getAgencies')->name('agence.list');
    Route::get('/agents/list', 'StatsController@getAgents')->name('agent.list');
// ===============

//Route::get('/tasks', 'TaskController@index')->name('tasks.index');
//Route::get('/tasks/get-tasks', 'TaskController@getTasks')->name('tasks.get-tasks');
//Route::post('/tasks/import-tasks', 'TaskController@importTasks')->name('tasks.import-tasks');

    Route::get('/stats', 'StatsController@import')->name('stats.import');
    Route::post('/stats/import-stats', 'StatsController@importStats')->name('stats.import-stats');

//Route::get('/demo', 'DemoController@index');

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


//    FILTERS
    Route::get('/dashboard/{filter}', 'FilterController@dashboard_filter');

    //"Appels Prealable " Filters
    Route::get('appels-pralables/regions/details/groupement', 'FilterController@getRegionsByGrpCall'); // column = key_groupement value // // PERCENT
    Route::get('appels-pralables/regions/details/groupement/columns', 'FilterController@getRegionsByGrpCallColumns'); // column = key_groupement value // // PERCENT
    Route::get('appels-pralables/regionsCallState/{column}', 'FilterController@getRegionsCallState'); // column = Nom_Region / Date_Heure_Note_Semaine   // SUM
    Route::get('appels-pralables/regionsCallState/columns/{column}', 'FilterController@getRegionsCallStateColumn');
    Route::get('appels-pralables/clientsByCallState/{callResult}', 'FilterController@getClientsByCallState'); // value = Injoignable / Joignable         // PERCENT
    Route::get('appels-pralables/clientsByCallState/columns/{callResult}', 'FilterController@getClientsByCallStateColumn');

    // "Appels Gem" Filters

    Route::get('appels-gem/regions/details/groupement', 'FilterController@getRegionsByGrpCall'); // column = key_groupement value // // PERCENT
    Route::get('appels-gem/regions/details/groupement/columns', 'FilterController@getRegionsByGrpCallColumns'); // column = key_groupement value // // PERCENT
    Route::get('appels-gem/regionsCallState/{column}', 'FilterController@getRegionsCallState'); // column = Nom_Region / Date_Heure_Note_Semaine   // SUM
    Route::get('appels-gem/regionsCallState/columns/{column}', 'FilterController@getRegionsCallStateColumn');

    // "production_globale_cam" Filters
//    Route::get('production_globale_cam/clientsByPerimeter', 'StatsController@getClientsByPerimeter');                                                          // SUM
//    Route::get('production_globale_cam/clientsByPerimeter/columns', 'StatsController@getClientsByPerimeterColumn');

    // "clotureOt_TaitementBL" Filters

    Route::get('appels-clture/regions/details/groupement', 'FilterController@getRegionsByGrpCall'); // column = key_groupement value // // PERCENT
    Route::get('appels-clture/regions/details/groupement/columns', 'FilterController@getRegionsByGrpCallColumns'); // column = key_groupement value // // PERCENT
    Route::get('appels-clture/folders/{callResult}', 'FilterController@getFolders'); // column = Resultat_Appel                                       // SUM
    Route::get('appels-clture/folders/columns/{callResult}', 'FilterController@getFoldersColumn');
    Route::get('appels-clture/nonValidatedFolders/{column}', 'FilterController@getNonValidatedFolders'); // column = Code_Intervention / Code_Type_Intervention   // PERCENT
    Route::get('appels-clture/nonValidatedFolders/columns/{column}', 'FilterController@getNonValidatedFoldersColumn');
    Route::get('appels-clture/Cloturetech', 'FilterController@getCloturetech');
    Route::get('appels-clture/Cloturetech/columns', 'FilterController@getCloturetechColumn');
    Route::get('appels-clture/GlobalDelay', 'FilterController@getGlobalDelay');
    Route::get('appels-clture/GlobalDelay/columns', 'FilterController@GlobalDelayColumn');

    Route::get('Export/ExportXls', 'StatsController@exportXls')->name('ExportXls');

    Route::get('/user/filter', 'FilterController@userFilter');
    //

});
