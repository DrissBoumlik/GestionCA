<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WikiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Auth::routes();

Route::group([
    'middleware' => ['auth'],
], function () {


    Route::get('/', [ToolController::class, 'home']);

    //region Users / Roles / Permissions
    // User Routes

    Route::get('/profile', [UserController::class, 'profile']);
    Route::resource('/users', UserController::class);
    Route::put('/changeStatus/{user}', [UserController::class, 'changeStatus']);
    Route::put('/updatePicture', [UserController::class, 'updatePicture']);
    Route::get('/getUsers', [UserController::class, 'getUsers']);

// Role Routes
    Route::resource('/roles', RoleController::class);
    Route::get('/getRoles', [RoleController::class, 'getRoles']);
    Route::get('/getRolePermissions/{role}', [RoleController::class, 'getPermissions']);
    Route::post('/assignPermissionRole', [RoleController::class, 'assignPermissionRole']);

// Permission Routes
    Route::resource('/permissions', PermissionController::class);
    Route::get('/getPermissions', [PermissionController::class, 'getPermissions']);
    Route::get('/getPermissionRoles/{permission}', [PermissionController::class, 'getRoles']);
    //endregion

    Route::get('/unauthorized', [ToolController::class, 'unauthorized']);

    Route::get('/dashboard', [StatsController::class, 'dashboard'])->name('dashboard');
    Route::get('/agences', [StatsController::class, 'dashboard'])->name('agence.index');
    Route::get('/agents', [StatsController::class, 'dashboard'])->name('agent.index');

    Route::get('/all-stats', [StatsController::class, 'index'])->name('stats.index');
    Route::post('/stats/get-stats', [StatsController::class, 'getStats'])->name('stats.get-stats');
    Route::get('/stats/filter', [ToolController::class, 'getFilterAllStats'])->name('stats.get-filter');

    Route::get('/agents/all-data', [AgentController::class, 'allData'])->name('agents.viewData');
    Route::get('/agents/get-data', [AgentController::class, 'getData'])->name('agents.getData');



    Route::get('/dates', [StatsController::class, 'getDates']);

    Route::get('/stats/filter/{column}', [StatsController::class, 'filterList']);
    Route::get('/agences/list', [StatsController::class, 'getAgencies'])->name('agence.list');
    Route::get('/agents/list', [StatsController::class, 'getAgents'])->name('agent.list');



    //region DASHBOARD
    Route::get('/regions/details/groupement', [StatsController::class, 'getRegionsByGrpCall']); // column = key_groupement value // // PERCENT
    Route::get('/regions/details/groupement/columns', [StatsController::class, 'getRegionsByGrpCallColumns']); // column = key_groupement value // // PERCENT

    Route::get('/regions/{callResult}', [StatsController::class, 'getRegions']); // column = Groupement                                       // PERCENT
    Route::get('/regions/columns/{callResult}', [StatsController::class, 'getRegionsColumn']);

    Route::get('/folders/{callResult}', [StatsController::class, 'getFolders']); // column = Groupement                                       // SUM
    Route::get('/folders/columns/{callResult}', [StatsController::class, 'getFoldersColumn']);

    Route::get('/regionsCallState/{column}', [StatsController::class, 'getRegionsCallState']); // column = Nom_Region / Date_Heure_Note_Semaine   // SUM
    Route::get('/regionsCallState/columns/{column}', [StatsController::class, 'getRegionsCallStateColumn']);

    Route::get('/clientsByCallState/{callResult}', [StatsController::class, 'getClientsByCallState']); // value = Injoignable / Joignable         // PERCENT
    Route::get('/clientsByCallState/columns/{callResult}', [StatsController::class, 'getClientsByCallStateColumn']);

    Route::get('/nonValidatedFolders/{column}', [StatsController::class, 'getNonValidatedFolders']); // column = Code_Intervention / Code_Type_Intervention   // PERCENT
    Route::get('/nonValidatedFolders/columns/{column}', [StatsController::class, 'getNonValidatedFoldersColumn']);

    Route::get('/clientsByPerimeter', [StatsController::class, 'getClientsByPerimeter']);                                                          // SUM
    Route::get('/clientsByPerimeter/columns', [StatsController::class, 'getClientsByPerimeterColumn']);

    Route::get('clientsWithCallStates', [StatsController::class, 'getClientsWithCallStates']); // value = Injoignable + Joignable  => Appel Prealable
    Route::get('clientsWithCallStates/columns', [StatsController::class, 'getClientsWithCallStatesColumn']);
    //endregion


    //region FILTERS PAGES
    Route::get('/dashboard/{filter}', [FilterController::class, 'dashboard_filter']);

    Route::get('Cloturetech', [StatsController::class, 'getCloturetech']);
    Route::get('Cloturetech/columns', [StatsController::class, 'getCloturetechColumn']);

    Route::get('GlobalDelay', [StatsController::class, 'getGlobalDelay']);
    Route::get('GlobalDelay/columns', [StatsController::class, 'getGlobalDelayColumn']);

    Route::get('ProcessingDelay', [StatsController::class, 'getProcessingDelay']);
    Route::get('ProcessingDelay/columns', [StatsController::class, 'getProcessingDelayColumn']);

    Route::get('TypeIntervention', [StatsController::class, 'getTypeIntervention']);
    Route::get('TypeIntervention/columns', [StatsController::class, 'getTypeInterventionColumn']);
    Route::get('TypeIntervention/details', [StatsController::class, 'getTypeInterventionGrpCall']);
    Route::get('TypeIntervention/details/columns', [StatsController::class, 'getTypeInterventionColumnGrpCall']);

    Route::get('ValTypeIntervention', [StatsController::class, 'getValTypeIntervention']);
    Route::get('ValTypeIntervention/columns', [StatsController::class, 'getValTypeInterventionColumn']);
    Route::get('ValTypeIntervention/details', [StatsController::class, 'getValTypeInterventionGrpCall']);
    Route::get('ValTypeIntervention/details/columns', [StatsController::class, 'getValTypeInterventionColumnGrpCall']);

    Route::get('RepTypeIntervention', [StatsController::class, 'getRepTypeIntervention']);
    Route::get('RepTypeIntervention/columns', [StatsController::class, 'getRepTypeInterventionColumn']);
    Route::get('RepTypeIntervention/details', [StatsController::class, 'getRepTypeInterventionGrpCall']);
    Route::get('RepTypeIntervention/details/columns', [StatsController::class, 'getRepTypeInterventionColumnGrpCall']);

    Route::get('RepJoiDepartement', [StatsController::class, 'getRepJoiDepartement']);
    Route::get('RepJoiDepartement/columns', [StatsController::class, 'getRepJoiDepartementColumn']);
    Route::get('RepJoiDepartement/details', [StatsController::class, 'getRepJoiDepartementGrpCall']);
    Route::get('RepJoiDepartement/details/columns', [StatsController::class, 'getRepJoiDepartementColumnGrpCall']);

    Route::get('RepJoiAutreDepartement', [StatsController::class, 'getRepJoiAutreDepartement']);
    Route::get('RepJoiAutreDepartement/columns', [StatsController::class, 'getRepJoiAutreDepartementColumn']);
    Route::get('RepJoiAutreDepartement/details', [StatsController::class, 'getRepJoiAutreDepartementGrpCall']);
    Route::get('RepJoiAutreDepartement/details/columns', [StatsController::class, 'getRepJoiAutreDepartementColumnGrpCall']);


    Route::get('/globalView', [StatsController::class, 'getGlobalView']);
    Route::get('/globalView/columns', [StatsController::class, 'getGlobalViewColumns']);

    Route::get('/globalView/details', [StatsController::class, 'getGlobalViewDetails']);
    Route::get('/globalView/details/columns', [StatsController::class, 'getGlobalViewDetailsColumns']);

    Route::get('AgentProd', [StatsController::class, 'getAgentProd']);
    Route::get('AgentProd/columns', [StatsController::class, 'getAgentProdColumn']);
    //endregion


    //region Import / Export
    Route::get('/import/agents', [AgentController::class, 'importView'])->name('agents.importView');
    Route::post('/import/agents', [AgentController::class, 'import'])->name('agents.import');

    Route::get('/import/stats', [StatsController::class, 'import'])->name('stats.importView');
    Route::post('/import/stats', [StatsController::class, 'importStats'])->name('stats.import');

    Route::get('/import/data/count', [ToolController::class, 'getInsertedData']);
    Route::get('/import/status/edit/{flag}', [ToolController::class, 'editImportingStatus']);

    Route::get('Export/ExportXls', [StatsController::class, 'exportXls'])->name('ExportXls');
    Route::get('Export/agentProdExport', [StatsController::class, 'agentProdExport'])->name('agentProdExport');
    //endregion agentProdExport

    Route::get('/user/filter', [FilterController::class, 'getUserFilter']);
    Route::post('/user/filter', [FilterController::class, 'saveUserFilter']);


    //region WIKI routes
    Route::get('/wiki', [WikiController::class, 'index'])->name('wiki.index');
    //endregion

});
