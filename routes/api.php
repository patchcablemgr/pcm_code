<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\AttributesMedia;
use App\Http\Controllers\AttributesMediaType;
use App\Http\Controllers\AttributesPortOrientation;
use App\Http\Controllers\AttributesPortConnector;
use App\Http\Controllers\AttributesCableConnector;
use App\Http\Controllers\FloorplanTemplateController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TrunkController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CablePathController;
use App\Http\Controllers\DataSyncController;
use App\Http\Controllers\CableController;
use App\Http\Controllers\PortController;

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
    Route::post('mfa', [AuthController::class, 'mfa']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register-tenant', [AuthController::class, 'registerTenant']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function(){

  // Only POST
  Route::post('archive', [ArchiveController::class, 'import'])->middleware('archiveUpload');
  Route::post('cables', [CableController::class, 'store']);
  Route::post('cable-paths', [CablePathController::class, 'store']);
  Route::post('config/app/update', [ConfigController::class, 'updateApp']);
  Route::post('config/csr', [ConfigController::class, 'generateCSR']);
  Route::post('config/csr/{id}/cert', [ConfigController::class, 'storeCert']);
  Route::post('config/csr/{id}/self-signed', [ConfigController::class, 'generateSelfSigned']);
  Route::post('config/network', [ConfigController::class, 'networkConfig']);
  Route::post('data-sync', [DataSyncController::class, 'getChanges']);
  Route::post('locations/{id}/image', [ImageController::class, 'storeLocationImage']);
  Route::post('objects/floorplan', [ObjectController::class, 'storeFloorplan']);
  Route::post('objects/insert', [ObjectController::class, 'storeInsert']);
  Route::post('objects/standard', [ObjectController::class, 'storeStandard']);
  Route::post('ports', [PortController::class, 'store']);
  Route::post('profile/mfa', [ProfileController::class, 'confirmMFA']);
  Route::post('templates/{id}/image', [ImageController::class, 'storeTemplateImage']);

  // Only GET
  Route::get('archive', [ArchiveController::class, 'export']);
  Route::get('cables', [CableController::class, 'index']);
  Route::get('cable-paths', [CablePathController::class, 'index']);
  Route::get('catalog/categories', [CategoryController::class, 'indexCatalogCategories']);
  Route::get('catalog/templates', [TemplateController::class, 'indexCatalogTemplates']);
  Route::get('catalog/template/{id}', [TemplateController::class, 'importCatalogTemplate']);
  Route::get('config/network', [ConfigController::class, 'indexNetwork']);
  Route::get('config/csr', [ConfigController::class, 'indexCSR']);
  Route::get('config/cert', [ConfigController::class, 'indexCert']);
  Route::get('floorplan-templates', [FloorplanTemplateController::class, 'index']);
  Route::get('media', [AttributesMedia::class, 'index']);
  Route::get('media-type', [AttributesMediaType::class, 'index']);
  Route::get('organization', [OrganizationController::class, 'index']);
  Route::get('organization/license', [OrganizationController::class, 'showLicense']);
  Route::get('organization/license/portal', [OrganizationController::class, 'showLicensePortal']);
  Route::get('port-connectors', [AttributesPortConnector::class, 'index']);
  Route::get('cable-connectors', [AttributesCableConnector::class, 'index']);
  Route::get('port-orientation', [AttributesPortOrientation::class, 'index']);
  Route::get('profile/mfa', [ProfileController::class, 'generateMFAQRCode']);

  // Only DELETE
  Route::delete('cable-paths/{id}', [CablePathController::class, 'destroy']);
  Route::delete('cables/{id}', [CableController::class, 'destroy']);
  Route::delete('config/cert/{id}', [ConfigController::class, 'destroyCert']);
  Route::delete('config/csr/{id}', [ConfigController::class, 'destroyCSR']);
  
  // Only PATCH
  Route::patch('cable-paths/{id}', [CablePathController::class, 'update']);
  Route::patch('config/cert/{id}/activate', [ConfigController::class, 'activateCert']);
  Route::patch('organization/license', [OrganizationController::class, 'updateLicense']);
  Route::patch('profile/change-password', [ProfileController::class, 'changePassword']);
  Route::patch('profile/mfa', [ProfileController::class, 'disableMFA']);
  
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