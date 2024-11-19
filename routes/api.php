<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PasswordController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\SavingController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\TypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function ($route) {

    $route->post('logout', [RegisterController::class, 'logout']);
    $route->get('user', function(Request $request) {
        return $request->user();
    });

    $route->controller(GoalController::class)->prefix('goals')->group(function($r) {
        $r->get('',  'index');
        $r->post('',  'store');
        $r->get('{id}',  'show');
        $r->patch('{id}',  'update');
        $r->delete('{id}',  'destroy');
    });

    $route->post('savings', [SavingController::class, 'store']);

    $route->get('types', [TypeController::class, 'index']);
    $route->post('types', [TypeController::class, 'store']);

    $route->controller(TransactionController::class)->prefix('transactions')->group(function($r) {
        $r->get('{start?}/{end?}',  'index');
        $r->post('',  'store');
        $r->get('{id}',  'show');
        //$r->patch('{id}',  'update');
        $r->delete('{id}',  'destroy');
    });
});

Route::controller(RegisterController::class)->group(function($route) {
    $route->post('register', 'register');
    $route->post('login', 'login');
});

Route::controller(PasswordController::class)->prefix('password')->group(function($route) {
    $route->post('email', 'forgot');
    $route->post('code/check', 'check');
    $route->post('reset', 'reset');
});

Route::post('/upload', FileUploadController::class);
