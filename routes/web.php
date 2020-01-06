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

//Route::group([
//    'middleware' => ['auth'],
//], function () {
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

// Skills Routes
    Route::get('/skills', 'SkillController@index');
    Route::post('/skills', 'SkillController@store');
    Route::delete('/skills/{skill}', 'SkillController@destroy');
    Route::put('/skills/{skill}', 'SkillController@update');

    Route::resource('/skills', 'SkillController');
    Route::get('/getSkills', 'SkillController@getSkills');
    Route::get('/getUserSkills', 'SkillController@getUserSkills');
    Route::get('/editSkills', 'SkillController@editSkills');
    Route::put('/updateSkills', 'SkillController@updateSkills');
    Route::post('/chooseSkill', 'SkillController@chooseSkill');
    Route::put('/chooseTopSkill', 'SkillController@chooseTopSkill');

// Projects Routes
    Route::resource('/projects', 'ProjectController');
    Route::get('/getProjects', 'ProjectController@getProjects');
    Route::get('/getTechs/{project?}', 'ProjectController@getTechs');
    Route::get('/getCollaborators/{project?}', 'ProjectController@getCollaborators');

    Route::get('/unauthorized', 'ToolController@unauthorized');

    Route::get('/dashboard', 'StatsController@dashboard')->name('dashboard');
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

    Route::get('/stats', 'StatsController@index')->name('stats.index');
    Route::post('/stats/import-stats', 'StatsController@importStats')->name('stats.import-stats');

//Route::get('/demo', 'DemoController@index');

    Route::get('/regions/details/groupement', 'StatsController@getRegionsByGrpCall')->name('stateregdet'); // column = key_groupement value // // PERCENT
    Route::get('/regions/{callResult}', 'StatsController@getRegions'); // column = Resultat_Appel                                       // PERCENT
    Route::get('/regions/columns/{callResult}', 'StatsController@getRegionsColumn');

    Route::get('/folders/{callResult}', 'StatsController@getFolders'); // column = Resultat_Appel                                       // SUM
    Route::get('/folders/columns/{callResult}', 'StatsController@getFoldersColumn');

    Route::get('/regionsCallState/{column}', 'StatsController@getRegionsCallState'); // column = Nom_Region / Date_Heure_Note_Semaine   // SUM
    Route::get('/regionsCallState/columns/{column}', 'StatsController@getRegionsCallStateColumn');

    Route::get('/clientsByCallState/{callResult}', 'StatsController@getClientsByCallState'); // value = Injoignable / Joignable         // PERCENT
    Route::get('/clientsByCallState/columns/{callResult}', 'StatsController@getClientsByCallStateColumn');

    Route::get('/nonValidatedFolders/{column}', 'StatsController@getNonValidatedFolders'); // column = Code_Intervention / Code_Type_Intervention   // PERCENT
    Route::get('/nonValidatedFolders/columns/{column}', 'StatsController@getNonValidatedFoldersColumn');

    Route::get('/clientsByPerimeter', 'StatsController@getClientsByPerimeter');                                                          // SUM
    Route::get('/clientsByPerimeter/columns', 'StatsController@getClientsByPerimeterColumn');
    Route::get('/demo', 'StatsController@demo');
//});
