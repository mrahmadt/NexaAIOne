<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/call/{id}/{name?}', [APIController::class, 'execute']);

    Route::post('/collections/documents/create', [DocumentController::class, 'create']);
    Route::delete('/collections/document/delete/{document_id}', [DocumentController::class, 'delete']);
    Route::get('/collections/document/get/{document_id}', [DocumentController::class, 'getDocument']);
    Route::get('/collections/documents/list/{collection_id}', [DocumentController::class, 'listDocuments']);
    Route::get('/collections/documents/status/{jobID}', [DocumentController::class, 'documentCreationStatus']);
});

Route::prefix('test')->group(function () {
    Route::post('/test', [App\Http\Controllers\testController::class, 'test']);
//     Route::post('/caching', [testController::class, 'caching']);
//     Route::post('/memory', [testController::class, 'memory']);
});
