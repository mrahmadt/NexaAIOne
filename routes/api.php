<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CollectionController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    Route::post('app/{appId}/{apiId}/{name?}', [APIController::class, 'execute'])->name('api.execute');

    Route::post('appCollection/create', [CollectionController::class, 'createAppCollection'])->name('api.app.collection.create');
    Route::delete('appCollection/delete', [CollectionController::class, 'deleteAppCollection'])->name('api.app.collection.delete');

    Route::prefix('collections')->group(function () {
        Route::post('documents/create', [DocumentController::class, 'create'])->name('api.document.create');
        Route::put('document/update/{document_id}', [DocumentController::class, 'update'])->name('api.document.update');
        Route::delete('document/delete/{document_id}', [DocumentController::class, 'delete'])->name('api.document.delete');
        Route::get('document/get/{document_id}', [DocumentController::class, 'getDocument'])->name('api.document.get');
        Route::get('documents/list/{collection_id}', [DocumentController::class, 'listDocuments'])->name('api.documents.list');
        Route::get('documents/status/{jobID}', [DocumentController::class, 'documentStatus'])->name('api.document.status');
    });
});

Route::prefix('test')->group(function () {
    Route::post('/test', [App\Http\Controllers\testController::class, 'test']);
//     Route::post('/caching', [testController::class, 'caching']);
//     Route::post('/memory', [testController::class, 'memory']);
});
