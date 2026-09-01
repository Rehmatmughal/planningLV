<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AdminDashboardController;

// copy from other project
// 2nd project table
use App\Http\Controllers\AreaVariationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopmentStatusController;
use App\Http\Controllers\LopStatusController;
use App\Http\Controllers\MortgageStatusController;
use App\Http\Controllers\PlotController;
use App\Http\Controllers\PossessionStatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\PlotsizeController;
use App\Http\Controllers\PlotCategoryTypeController;
use App\Http\Controllers\ActivityLogController;
use App\http\Controllers\PossessionCaseController;


// use App\Models\PlotCategoryType;



Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

Route::middleware(['auth'])->group(function () {
    // Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity.logs');
    // Route::get('/activity-logs-export', [App\Http\Controllers\ActivityLogController::class, 'export'])
    Route::get('/activity-logs-export', [ActivityLogController::class, 'export'])
    ->name('activity.logs.export');

});

require __DIR__.'/auth.php';
//
Route::middleware(['auth','role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/admindashboard', [AdminDashboardController::class, 'index'])
        // ->middleware('permission:user.create')
        ->name('admin.index');

});
Route::middleware(['auth','permission:user.view'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('users.update');
    // for password change

});

//  for passwrod change
// ye change password remove kr dia ha ku k ye function already Auth.php mn ha
    // Route::get('/change-password', function () {
    //     return view('auth.change-password');
    // })->middleware('auth')->name('password.form');
    // Route::post('/change-password', [UserController::class, 'changePassword'])
    //     ->middleware('auth')
    //     ->name('password.change');


// Route::middleware(['auth','role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
Route::middleware(['auth','permission:role.view'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:role.create')
        ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:role.store')
        // ->name('roles.create');
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:role.edit')
        ->name('roles.edit');
    // Route::put('/roles/{role}', [RoleController::class, 'update'])
    //     ->middleware('permission:role.edit')
    //     ->name('roles.update'); 
    // Route::delete('/roles/{role}',[RoleController::class, 'destroy'])
    //     ->middleware('permission:role.delete')
    //     ->name('roles.destroy');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:role.delete')
        ->name('roles.destroy');

    // Route::resource('roles', RoleController::class);
    // old resource route
    // Route::resource('permissions', PermissionController::class);

});



// project route change resource into manual

// Route::middleware(['auth','permission:project.view'])->prefix('admin')->name('admin.')->group(function () {
Route::middleware(['auth','permission:project.view'])->prefix('admin')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->middleware('permission:project.create')
        ->name('projects.create');
    
    Route::post('/projects', [ProjectController::class, 'store'])
        ->middleware('permission:project.create')
        ->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->middleware('permission:project.edit')
        ->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->middleware('permission:project.update')
        ->name('projects.update');
    Route::delete('/projects/{project}',[ProjectController::class, 'destroy'])
        ->middleware('permission:project.delete')
        ->name('projects.destroy');
});

// block route from resource to genral routes
Route::middleware(['auth','permission:block.view'])->prefix('admin')->group(function () {
    Route::get('/blocks', [BlockController::class, 'index'])->name('blocks.index');
    Route::get('/blocks/create', [BlockController::class, 'create'])
        ->middleware('permission:block.create')
        ->name('blocks.create');    
    Route::post('/blocks', [BlockController::class, 'store'])
        ->middleware('permission:block.create')
        ->name('blocks.store');
    Route::get('/blocks/{block}/edit', [BlockController::class, 'edit'])
        ->middleware('permission:block.edit')
        ->name('blocks.edit');
    Route::put('/blocks/{block}', [BlockController::class, 'update'])
        ->middleware('permission:block.update')
        ->name('blocks.update');
    Route::delete('/blocks/{block}',[BlockController::class, 'destroy'])
        ->middleware('permission:block.delete')
        ->name('blocks.destroy');
// index of blocks of selected project
    Route::get('/projects/{project}/blocks', [BlockController::class, 'index'])
        ->middleware('permission:block.view')
        ->name('projects.blocks.index');
});

// routes for plotcontroller 
Route::middleware(['auth','permission:plot.view'])->prefix('admin')->group(function () {

// route for excel exports blockwise plots
    Route::get('/blocks/{block}/plots/export', [PlotController::class, 'exportBlockPlots'])
            ->middleware('permission:plot.excel')
            ->name('blocks.plots.export');
    Route::get('/streets/{street}/plots', [PlotController::class, 'indexByStreet'])
            ->middleware('permission:plot.view')
            ->name('streets.plots.index');
    Route::get('/plot/{id}', [PlotController::class, 'show'])
            ->middleware('permission:plot.view')
            ->name('plots.show');
    Route::get('/plots', [PlotController::class, 'index'])->name('plots.index');
    Route::get('/plots/create', [plotController::class, 'create'])
        ->middleware('permission:plot.create')
        ->name('plots.create');    
    Route::post('/plots', [PlotController::class, 'store'])
        ->middleware('permission:plot.create')
        ->name('plots.store');
    Route::get('/plots/{plot}/edit', [PlotController::class, 'edit'])
        ->middleware('permission:plot.edit')
        ->name('plots.edit');
    Route::put('/plots/{plot}', [PlotController::class, 'update'])
        ->middleware('permission:plot.update')
        ->name('plots.update');
    Route::delete('/plots/{plot}',[PlotController::class, 'destroy'])
        ->middleware('permission:plot.delete')
        ->name('plots.destroy');
        // deleted plots and restore route
    Route::get('/plots/deleted', [PlotController::class, 'deleted'])
        ->middleware('permission:plot.delete')
        ->name('plots.deleted');

    Route::get('/plots/{id}/deleted-view', [PlotController::class, 'deletedView'])
        ->middleware('permission:plot.trashview')
        ->name('plots.deleted.view');

    Route::put('/plots/{id}/restore', [PlotController::class, 'restore'])
        ->middleware('permission:plot.restore')
        ->name('plots.restore');

    Route::delete('/plots/{id}/force-delete', [PlotController::class, 'forceDelete'])
        ->middleware('permission:plot.force-delete')
        ->name('plots.forceDelete');
});

Route::middleware(['auth','permission:areavariation.view'])->prefix('admin')->group(function () {

    Route::get('/area-variations/createnew/{plot_id}', [AreaVariationController::class, 'createnew'])
        ->middleware('permission:areavariation.create')
        ->name('area_variations.createnew');
});

    // AJAX endpoints (controller methods described below)
// Route::middleware(['auth','permission:areavariation.view'])->prefix('admin')->group(function () {
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('/plots/{plot}/lop', [PlotController::class, 'updateLop'])
        ->middleware('permission:lop.edit')
        ->name('plots.updateLop');
    Route::get('/plots/{plot}/lop', [PlotController::class, 'getLop'])
        ->middleware('permission:lop.view')
        ->name('plots.getLop');
});

// Route::middleware(['auth'])->prefix('admin')->group(function () {
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('/plots/{plot}/development', [PlotController::class, 'updateDevelopment'])
        // ->middleware('permission:development.create'|'development.update')
        ->middleware('permission:development.edit')
        ->name('plots.updateDevelopment');
    Route::get('/plots/{plot}/development', [PlotController::class, 'getDevelopment'])
        // ->middleware('permission::development.view')
        ->name('plots.getDevelopment');
});

// street controller resource into auth system
Route::middleware(['auth','permission:street.view'])->prefix('admin')->group(function () {

// route for excel exports blockwise plots
    // Route::get('/blocks/{block}/plots/export', [PlotController::class, 'exportBlockPlots'])
    //         ->middleware('permission:plot.excel')
    //         ->name('blocks.plots.export');
    // Route::get('streets/{street}/plots', [PlotController::class, 'indexByStreet'])
    //         ->middleware('permission:plot.view')
    //         ->name('streets.plots.index');
    // Route::get('/plot/{id}', [PlotController::class, 'show'])
    //         ->middleware('permission:plot.view')
    //         ->name('plots.show');
    Route::get('/streets', [StreetController::class, 'index'])
        ->name('streets.index');
    // for special streets index
    Route::get('/blocks/{block}/streets', [StreetController::class, 'index'])
        ->name('blocks.streets.index');
    Route::get('/streets/create', [StreetController::class, 'create'])
        ->middleware('permission:street.create')
        ->name('streets.create');    
    Route::post('/streets', [StreetController::class, 'store'])
        ->middleware('permission:street.create')
        ->name('streets.store');
    Route::get('/streets/{street}/edit', [StreetController::class, 'edit'])
        ->middleware('permission:street.edit')
        ->name('streets.edit');
    Route::put('/streets/{street}', [StreetController::class, 'update'])
        ->middleware('permission:street.update')
        ->name('streets.update');
    Route::delete('/streets/{street}',[streetController::class, 'destroy'])
        ->middleware('permission:street.delete')
        ->name('streets.destroy');
});
// size routes resource into authsystem
Route::middleware(['auth','permission:size.view'])->prefix('admin')->group(function () {
 
    Route::get('/sizes', [PlotsizeController::class, 'index'])->name('sizes.index');
    Route::get('/sizes/create', [PlotsizeController::class, 'create'])
        ->middleware('permission:size.create')
        ->name('sizes.create');    
    Route::post('/ssizes', [PlotsizeController::class, 'store'])
        ->middleware('permission:size.create')
        ->name('sizes.store');
    Route::get('/sizes/{size}/edit', [PlotsizeController::class, 'edit'])
        ->middleware('permission:size.edit')
        ->name('sizes.edit');
    Route::put('/sizes/{size}', [PlotsizeController::class, 'update'])
        ->middleware('permission:size.update')
        ->name('sizes.update');
    Route::delete('/sizes/{size}',[PlotsizeController::class, 'destroy'])
        ->middleware('permission:size.delete')
        ->name('sizes.destroy');
    
    // Trash / Deleted Sizes
    Route::get('sizes-trash', [PlotsizeController::class, 'trash'])
        ->middleware('permission:size.trashview')
        ->name('sizes.trash');

    // Restore Deleted Size
    Route::post('sizes/{id}/restore', [PlotsizeController::class, 'restore'])
        ->middleware('permission:size.trashrestore')
        ->name('sizes.restore');

    Route::delete('/sizes/{id}/force-delete', [PlotsizeController::class, 'forceDelete'])
        ->middleware('permission:size.delete')
        ->name('sizes.forceDelete');
});

Route::middleware(['auth'])->group(function () {

    // Route::resource('projects', ProjectController::class);
    // Route::resource('blocks', BlockController::class);
    // Route::resource('plots', PlotController::class);
    // Route::resource('streets', StreetController::class);
    // Route::resource('sizes', PlotsizeController::class);
    Route::resource('categories', PlotCategoryTypeController::class);

    // new check 
    // Route::get('/',[BlockController::class,'showBlocks'])->name('home');
    Route::get('/add-block', [BlockController::class, 'addBlock'])->name('addblock');
    // get blocks of special project
    // Route::get('/getblocks/{proid}', [BlockController::class, 'projectwise'])->name('projectwiseb');

    // delete street index by blockwise (old method)
    // Route::get('/blocks/{block}/plots', [PlotController::class, 'indexByBlock'])
    //     ->name('blocks.plots.index');

    // Route::get('/projects/{project}/blocks', [BlockController::class, 'index'])->name('projects.blocks.index');

    // Route::get('/getblocks/{proid}/{pname}', [BlockController::class, 'createpsproject'])->name('psproject');

    // from chatbpt
    Route::get('/blocks/createpsproject/{project_id?}', [BlockController::class, 'createpsproject'])->name('blocks.createpsproject');

    Route::post('/save-block', [BlockController::class, 'saveBlock'])->name('saveblock');
    // for blockwise plot list
    Route::get('/get-plots/{block_id}', [PlotController::class, 'blockwise'])->name('getplots');
    // plot detail print route 
    Route::get('/plot/{id}/print', [PlotController::class, 'print'])->name('plot.print');
    // plot coordinate 
    Route::get('/plot/{plot}/googlemap', [PlotController::class, 'getMap'])->name('googlemap.index');

    // new with chatgpt
    // old V1.1
    // Route::get('/', function () {
    //     // return redirect()->route('plots.index');
    //     return redirect()->route('dashboard');
    // })->name('home');

    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Route::get('/app', [DashboardController::class, 'app'])->name('app');

    // new ajax -- Start --

    // Route::post('/plots/{plot}/area-variations', [AreaVariationController::class, 'storearea'])->name('area_variations.storearea');
    // new store routes
    Route::get('/area-variations/create/{plot}', [AreaVariationController::class, 'create'])
        ->name('area_variations.create');


    // new routes
    // Route::post('/area-variations/{plot}/{area?}', [AreaVariationController::class, 'store'])->name('area_variations.store');
        
    // for excel download areavariation
    Route::get('/area-variation/{id}/excel', [AreaVariationController::class, 'exportExcel'])
        ->name('area_variations.excel');
    // Optionally: Route::get('/plots/{plot}/area-variations', [AreaVariationController::class,'index']);

    // new ajax -- END --

    // Ajax dependent dropdowns
    Route::get('/get-blocks/{project_id}', [PlotController::class, 'getBlocks'])->name('get.blocks');
    Route::get('/get-sizes/{project_id}', [PlotController::class, 'getSizes'])->name('get.sizes');
    Route::get('/get-streets/{block_id}', [PlotController::class, 'getStreets'])->name('get.streets');
    // for preselect project name

    
    Route::post('/development/store', [DevelopmentStatusController::class, 'storeOrUpdate'])->name('development.store');
    Route::post('/lop/store', [LopStatusController::class, 'storeOrUpdate'])->name('lop.store');
    Route::post('/mortgage/store', [MortgageStatusController::class, 'storeOrUpdate'])->name('mortgage.store');
    Route::post('/possession/store', [PossessionStatusController::class, 'storeOrUpdate'])->name('possession.store');

    // new

    Route::get('/area-variations', [AreaVariationController::class, 'index'])->name('area_variations.index');
    Route::get('/area-variations/{id}/edit', [AreaVariationController::class, 'edit'])->name('area_variations.edit'); // optional direct
    Route::post('/area-variations', [AreaVariationController::class, 'store'])->name('area_variations.store');
    Route::put('/area-variations/{id}', [AreaVariationController::class, 'update'])->name('area_variations.update');
    Route::delete('/area-variations/{id}', [AreaVariationController::class, 'destroy'])->name('area_variations.destroy');
    Route::get('/area-variations/{id}/print', [AreaVariationController::class, 'print'])->name('area_variations.print');
    Route::post('/area-variations/{id}/verify', [AreaVariationController::class, 'verify'])
        ->name('area_variations.verify');
    Route::post('/area-variations/{id}/mark-printed', [AreaVariationController::class, 'markAsPrinted'])
        ->name('area_variations.markPrinted');

    // Existing storeOrUpdate routes for statuses (kept)
    Route::post('/development/store', [DevelopmentStatusController::class, 'storeOrUpdate'])->name('development.store');
    Route::post('/lop/store', [LopStatusController::class, 'storeOrUpdate'])->name('lop.store');
    Route::post('/mortgage/store', [MortgageStatusController::class, 'storeOrUpdate'])->name('mortgage.store');
    Route::post('/possession/store', [PossessionStatusController::class, 'storeOrUpdate'])->name('possession.store');

    // Plots Filtering (AJAX)
    Route::get('/plots/filter', [PlotController::class, 'filter'])->name('plots.filter');
    // Fetch Blocks by Project
    Route::get('/blocks/by-project/{project_id}', [BlockController::class, 'getBlocksByProject'])
        ->name('blocks.byProject');

    // Fetch Streets by Block
    Route::get('/streets/by-block/{block_id}', [StreetController::class, 'getStreetsByBlock'])
        ->name('streets.byBlock');

        // new working for admin pannel V1.2

        // for import area variations
    Route::get('/import-area-variations', [AreaVariationController::class, 'importAreaVariations']);
    // areavarition filter section depanded dropdown 
    Route::get('/ajax/blocks-by-project/{project}', function ($projectId) {
        return \App\Models\Block::where('project_id', $projectId)
            ->orderBy('block_name') 
            ->get(['id', 'block_name']);
    })->name('ajax.blocks.by.project');
    // route for excel exports
    Route::get('/projects/{project}/blocks/excel', [BlockController::class, 'exportProjectBlocksExcel']
    )->name('projects.blocks.excel');

});
// auth system routes copy from other project
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(['auth','permission:user.view'])->prefix('admin')->name('admin.')->group(function () {

//     Route::get('/users', [UserController::class, 'index'])->name('users.index');
//     Route::get('/users/create', [UserController::class, 'create'])
//         ->middleware('permission:user.create')
//         ->name('users.create');

//     Route::post('/users', [UserController::class, 'store'])
//         ->middleware('permission:user.create')
//         ->name('users.store');

//     Route::get('/users/{user}/edit', [UserController::class, 'edit'])
//         ->middleware('permission:user.edit')
//         ->name('users.edit');

//     Route::put('/users/{user}', [UserController::class, 'update'])
//         ->middleware('permission:user.edit')
//         ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('users.destroy');

//     // Route::post('/change-password', [UserController::class, 'changePassword'])
//     //     ->name('password.change');
    
});

// Route::middleware(['auth','role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
Route::middleware(['auth','permission:role.view'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:role.create')
        ->name('roles.create');
    
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:role.create')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:role.edit')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:role.edit')
        ->name('roles.update');
    Route::delete('/roles',[RoleController::class, 'destroy'])
        ->middleware('permission:role.destroy')
        ->name('roles.destroy');
    // Route::resource('roles', RoleController::class);
    // old resource route
    // Route::resource('permissions', PermissionController::class);

});

Route::middleware(['auth','permission:permission.view'])->prefix('admin')->name('admin.')->group(function () {
    // Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

});

Route::resource('possession-cases', PossessionCaseController::class);

Route::patch(
    'possession-cases/{possessionCase}/status',
    [PossessionCaseController::class, 'updateStatus']
)->name('possession-cases.update-status');
