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

Route::get('/', 'HomeController@dashboard')->name('home');
Route::get('/dashboard', 'HomeController@dashboard')->name('home');

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

Route::get('results', 'DemoController@demo');
