<?php

use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* Route::get('/', 'App\Http\Controllers\PusherController@index');
Route::post('/broadcast', 'App\Http\Controllers\PusherController@broadcast');
Route::post('/receive', 'App\Http\Controllers\PusherController@receive'); */

Route::get('/send-message', function () {
    $message = 'Hello from Pusher!';

    // Broadcast the event
    broadcast(new MessageSent($message))->toOthers();

    return response()->json(['message' => $message]);
});
