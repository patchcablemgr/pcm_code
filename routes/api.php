<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\AttributesMedia;
use App\Http\Controllers\AttributesPortOrientation;
use App\Http\Controllers\AttributesPortConnector;
use App\Http\Controllers\FloorplanTemplateController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TrunkController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\UserController;

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

  // Only POST
  Route::post('objects/standard', [ObjectController::class, 'storeStandard']);
  Route::post('objects/insert', [ObjectController::class, 'storeInsert']);
  Route::post('objects/floorplan', [ObjectController::class, 'storeFloorplan']);
  Route::post('locations/{id}/image', [ImageController::class, 'storeLocationImage']);
  Route::post('templates/{id}/image', [ImageController::class, 'storeTemplateImage']);

  // Only GET
  Route::get('medium', [AttributesMedia::class, 'index']);
  Route::get('port-orientation', [AttributesPortOrientation::class, 'index']);
  Route::get('port-connectors', [AttributesPortConnector::class, 'index']);
  Route::get('floorplan-templates', [FloorplanTemplateController::class, 'index']);

  // No POST
  Route::apiResources([
    'objects' => ObjectController::class,
    'users' => UserController::class,
  ],[
    'only' =>[
      'index',
      'show',
      'update',
      'destroy'
    ]
  ]);

  // All verbs
  Route::apiResources([
    'locations' => LocationController::class,
    'categories' => CategoryController::class,
    'templates' => TemplateController::class,
    'trunks' => TrunkController::class,
    'connections' => ConnectionController::class,
  ],[
    'only' =>[
      'index',
      'store',
      'show',
      'update',
      'destroy'
    ]
  ]);
});