<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PasswordController;

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
