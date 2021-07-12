<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TemplateCategory;
use App\Http\Controllers\AttributesMedia;

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
    'category' => TemplateCategory::class,
    'medium' => AttributesMedia::class,
    'port-orientation' => AttributesPortOrientation::class,
  ]);
});

/*
Route::apiResources([
  'category' => TemplateCategory::class,
]);
*/