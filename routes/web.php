<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIDocsController;

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

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('api-docs/app/{appDocToken}/{apiID?}', [APIDocsController::class, 'viewApp'])->name('api-docs.app');
Route::get('api-docs/collection/document/create', [APIDocsController::class, 'collectionCreate'])->name('api-docs.collection.document.create');
Route::get('api-docs/collection/document/update', [APIDocsController::class, 'collectionUpdate'])->name('api-docs.collection.document.update');
Route::get('api-docs/collection/document/delete', [APIDocsController::class, 'collectionDelete'])->name('api-docs.collection.document.delete');
Route::get('api-docs/collection/document/get', [APIDocsController::class, 'collectionGet'])->name('api-docs.collection.document.get');
Route::get('api-docs/collection/documents/list', [APIDocsController::class, 'collectionList'])->name('api-docs.collection.documents.list');
Route::get('api-docs/collection/document/status', [APIDocsController::class, 'collectionStatus'])->name('api-docs.collection.document.status');