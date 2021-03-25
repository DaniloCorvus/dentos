<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//List the users

Route::get('users', [UserController::class, 'index']);

//List single user
Route::get('user/{id}', [UserController::class, 'show']);

//Create new user
Route::post('user', [UserController::class, 'store']);

//Update user
Route::put('user', [UserController::class, 'store']);

//List single user
Route::delete('user/{id}', [UserController::class, 'destroy']);
