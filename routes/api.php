<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\TaskController;

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

// Auth
Route::post('auth/register', [ApiTokenController::class, 'register']);
Route::post('auth/login', [ApiTokenController::class, 'login']);

// Task
Route::middleware('auth:sanctum')->get('tasks', 'App\Http\Controllers\TaskController@tasks');
Route::middleware('auth:sanctum')->post('createTask', 'App\Http\Controllers\TaskController@createTask');
Route::middleware('auth:sanctum')->put('updateTask/{id}', 'App\Http\Controllers\TaskController@updateTask');
Route::middleware('auth:sanctum')->get('completeTask/{id}', 'App\Http\Controllers\TaskController@completeTask');
Route::middleware('auth:sanctum')->delete('deleteTask/{id}', 'App\Http\Controllers\TaskController@deleteTask');
