<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Locations;
use App\Http\Controllers\Categories;
use App\Http\Controllers\Templates;
use App\Http\Controllers\AttributesMedia;
use App\Http\Controllers\AttributesPortOrientation;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::apiResources([
    'locations' => Locations::class,
    'categories' => Categories::class,
    'templates' => Templates::class,
    'medium' => AttributesMedia::class,
    'port-orientation' => AttributesPortOrientation::class,
  ]);
});