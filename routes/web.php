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

//Route::get('/', 'HomeController@dashboard')->name('home');
Route::get('/_dashboard', 'HomeController@dashboard')->name('home');
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
//Route::get('/skills', 'SkillController@index');
//Route::post('/skills', 'SkillController@store');
//Route::delete('/skills/{skill}', 'SkillController@destroy');
//Route::put('/skills/{skill}', 'SkillController@update');

Route::resource('/skills', 'SkillController');
Route::get('/getSkills', 'SkillController@getSkills');
Route::get('/getUserSkills', 'SkillController@getUserSkills');
Route::get('/editSkills', 'SkillController@editSkills');
Route::put('/updateSkills', 'SkillController@updateSkills');
//Route::post('/chooseSkill', 'SkillController@chooseSkill');
//Route::put('/chooseTopSkill', 'SkillController@chooseTopSkill');

// Projects Routes
Route::resource('/projects', 'ProjectController');
Route::get('/getProjects', 'ProjectController@getProjects');
Route::get('/getTechs/{project?}', 'ProjectController@getTechs');
Route::get('/getCollaborators/{project?}', 'ProjectController@getCollaborators');

Route::get('/unauthorized', 'ToolController@unauthorized');

Route::get('/dashboard', 'StatsController@dashboard');
Route::post('/getRegionsByDates', 'StatsController@getRegionsByDates');
Route::post('/getNonValidatedFoldersByCodeByDates', 'StatsController@getNonValidatedFoldersByCodeByDates');
Route::post('/getNonValidatedFoldersByTypeByDates', 'StatsController@getNonValidatedFoldersByTypeByDates');
Route::post('/getClientsByCallStateJoiByDates', 'StatsController@getClientsByCallStateJoiByDates');
Route::post('/getClientsByCallStateInjByDates', 'StatsController@getClientsByCallStateInjByDates');

//Route::get('/getDates', 'StatsController@getDates');
Route::get('/getDates', 'StatsController@getDates');
Route::get('/agencies', 'StatsController@byAgency');

//Route::get('/getRegions', 'StatsController@getRegions');


// ===============

Route::get('/tasks', 'TaskController@index')->name('tasks.index');
Route::get('/tasks/get-tasks', 'TaskController@getTasks')->name('tasks.get-tasks');
Route::post('/tasks/import-tasks', 'TaskController@importTasks')->name('tasks.import-tasks');

Route::get('/stats', 'StatsController@index')->name('stats.index');
Route::post('/stats/import-stats', 'StatsController@importStats')->name('stats.import-stats');

Route::get('/demo', 'DemoController@index');
Route::get('/getRegions/{callResult}', 'StatsController@getRegions');
Route::get('/getRegionsColumn/{callResult}', 'StatsController@getRegionsColumn');

Route::get('/getRegionsCallState/{column}', 'StatsController@getRegionsCallState'); // Nom_Region ---- Date_Heure_Note_Semaine
Route::get('/getRegionsCallStateColumn/{column}', 'StatsController@getRegionsCallStateColumn');

Route::get('/getNonValidatedFolders/{intervCol}', 'StatsController@getNonValidatedFolders');
Route::get('/getNonValidatedFoldersColumn/{intervCol}', 'StatsController@getNonValidatedFoldersColumn');

Route::get('/getClientsByCallState/{callResult}', 'StatsController@getClientsByCallState');
Route::get('/getClientsByCallStateColumn/{callResult}', 'StatsController@getClientsByCallStateColumn');
