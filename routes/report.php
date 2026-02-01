<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;

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

Route::prefix('admin')->group(function () {

    Route::any('/get-mind-map',[ReportController::class, 'getMindMap'])->name('report.get.mind.map');
	Route::any('/get-mind-map-records',[ReportController::class, 'getMindMapRecords'])->name('report.get.mind.map.records');
	
	Route::any('/get-sales',[ReportController::class, 'getSales'])->name('report.get.sales');
	Route::any('/get-sales-records',[ReportController::class, 'getSalesRecords'])->name('report.get.sales.records');


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
