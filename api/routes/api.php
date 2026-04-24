<?php

use App\Http\Controllers\AtelierController;
use App\Http\Controllers\DangerController;
use App\Http\Controllers\GrapheController;
use App\Http\Controllers\HistoriqueAccesController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\TrashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsineController;
// use App\Models\User;


Route::post('/login', [UserController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [UserController::class, 'user']);


    Route::get('/factories/all', [UsineController::class, 'all']);
    Route::get('/factories/alls', [UsineController::class, 'alls']);
    Route::put('/factories/{id}', [UsineController::class, 'edit']);
    Route::post('/factories/add/', [UsineController::class, 'add']);
    route::put('/factories/delete/{id}', [UsineController::class, 'delete']);


    route::get('dangers/all', [DangerController::class, 'all']);


    Route::get('/dashboard', [UserController::class, 'dashboard']);

    route::get('/user/profile', [UserController::class, 'profile']);

    Route::get('/workshop/alls', [AtelierController::class, 'all']);
    Route::get('/workshop/all/{code}', [AtelierController::class, 'allByFactory']);
    Route::put('/workshop/{id}', [AtelierController::class, 'edit']);
    route::post('/workshop/add/{idUsine}', [AtelierController::class, 'add']);
    route::get('/workshop/all-products/{codeAtelier}', [ProduitController::class, 'ListProduit']);
    Route::get('/workshop/{usineId}/ateliers', [AtelierController::class, 'ateliersByUsine']);

    Route::get('workshop/{atelierId}/dangers', [GrapheController::class, 'getDangersData']);


    route::get('/product/photo/{path}', [ProduitController::class, 'getPhoto'])->where('path', '.*');
    route::get('product/all', [ProduitController::class, 'getAll']);

    route::get('/product/information/{code}', [ProduitController::class, 'oneProduct']);
    route::get('/product/add', [ProduitController::class, 'add']);


    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/GraphAtelier/{idusine}', [GrapheController::class, 'getAteliers']);


    route::get('/trash/factories', [TrashController::class, 'factories']);
    route::get('/trash/workshop', [TrashController::class, 'workshop']);


    route::get('/history/access', [HistoriqueAccesController::class, 'all']);
    route::get('/history/workshop/', [HistoriqueController::class, 'atelier']);
    route::get('/history/factories/', [HistoriqueController::class, 'usine']);
    route::get('/history/users/', [HistoriqueController::class, 'user']);

});


route::get('/product/fds/{path}', [ProduitController::class, 'getFDS'])->where('path', '.*');


Route::get('/product/dangerss/all', [GrapheController::class, 'getDangerStatsAllAteliers']);
// Route::get('/product/dangers/{idatelier}', GrapheDangerWorkshop::class);
// Route::get('/product/dangers/usine/{idusine}', GrapheDangerDashboard::class);
Route::get('/product/dangers/usine/{idusine}', [GrapheController::class, 'getDangerStatsByUsine']);
Route::get('/showGraphAtelier', [GrapheController::class, 'getAteliersWithDetailsAll']);

Route::get('/product/dangers/usine/{idusine}', [GrapheController::class, 'getDangerStatsByUsine']);

Route::post('/tests', function () {
    return response()->json(['ok' => true], 200);
});
// Route::post('/login', [UserController::class, 'login'])->name('login');
// Route::post('/logout', [UserController::class, 'logout'])->name('users.logout');


