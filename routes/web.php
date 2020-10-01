<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::group(['prefix' => 'manager', 'middleware' => 'manager'], function () {
    Route::get('tasks', [TaskController::class, 'getManagerTasks'])->name('getManagerTasks');
    Route::put('task/{id}', [TaskController::class, 'updateManagerTask'])->name('updateManagerTask');
    Route::post('task', [TaskController::class, 'storeManagerTask'])->name('storeManagerTask');
    Route::delete('task/{id}', [TaskController::class, 'deleteManagerTask'])->name('deleteManagerTask');
    Route::post('task/appoint', [TaskController::class, 'appointManagerTask'])->name('appointManagerTask');
});

Route::group(['prefix' => 'user', 'middleware' => 'user'], function () {
    Route::get('tasks', [TaskController::class, 'getUserTasks'])->name('getUserTasks');
    Route::post('task/change-status', [TaskController::class, 'changeUserTaskStatus'])->name('changeUserTaskStatus');
});
