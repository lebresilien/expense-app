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
use App\Http\Controllers\API\CategoryController;
use Carbon\Carbon;

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

    $route->controller(TransactionController::class)->prefix('transactions')->group(function($r) {
        $r->get('',  'index');
        $r->post('',  'store');
        $r->get('{id}',  'show');
        //$r->patch('{id}',  'update');
        $r->delete('{id}',  'destroy');
        $r->get('statistics',  'statistics');
    });

    $route->controller(CategoryController::class)->prefix('categories')->group(function($r) {
        $r->get('',  'index');
        $r->post('',  'store');
        $r->get('{id}/{start}/{end}',  'show');
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

Route::get('types', [TypeController::class, 'index']);
Route::post('types', [TypeController::class, 'store']);

Route::post('/upload', FileUploadController::class);

Route::get('test', function() {
    return Carbon::now()->format('m');
    echo Carbon::now()->addDays(5)->format('Y-m-d') . '-' . Carbon::parse('2024-11-30')->format('Y-m-d');
    //sreturn Carbon::now()->addDays(5)->format('Y-m-d');
    if(Carbon::parse(Carbon::now()->addDays(5)->format('Y-m-d'))->eq(Carbon::parse('2024-11-29'))) {
        return 'lflf';
    }

});
