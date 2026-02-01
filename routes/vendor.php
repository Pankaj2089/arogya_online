<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VendorPlanCategoriesController;
use App\Http\Controllers\Admin\VendorPlansController;
use App\Http\Controllers\Admin\VendorsController;

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

    #vendor plan categories
    Route::get('/vendor-plan-categories',[VendorPlanCategoriesController::class, 'getList'])->name('admin.vendor-plan-categories');
    Route::any('/vendor_plan_category_paginate',[VendorPlanCategoriesController::class, 'listPaginate'])->name('admin.vendor_plan_category_paginate');
    Route::any('/add-vendor-plan-category',[VendorPlanCategoriesController::class, 'addPage'])->name('admin.add-vendor-plan-category');
    Route::any('/edit-vendor-plan-category/{row_id}',[VendorPlanCategoriesController::class, 'editPage'])->name('admin.edit-vendor-plan-category');
	
	#vendor plans
    Route::get('/vendor-plans',[VendorPlansController::class, 'getList'])->name('admin.vendor-plans');
    Route::any('/vendor_plans_paginate',[VendorPlansController::class, 'listPaginate'])->name('admin.vendor_plans_paginate');
    Route::any('/add-vendor-plan',[VendorPlansController::class, 'addPage'])->name('admin.add-vendor-plan');
    Route::any('/edit-vendor-plan/{row_id}',[VendorPlansController::class, 'editPage'])->name('admin.edit-vendor-plan');
	
	#Vendors
    Route::get('/vendors',[VendorsController::class, 'getList'])->name('admin.vendors');
    Route::any('/vendors_paginate',[VendorsController::class, 'listPaginate'])->name('admin.vendors_paginate');
    Route::any('/add-vendor',[VendorsController::class, 'addPage'])->name('admin.add-vendor');
    Route::any('/edit-vendor/{row_id}',[VendorsController::class, 'editPage'])->name('admin.edit-vendor');
	Route::any('/login-vendor/{row_id}',[VendorsController::class, 'loginPage'])->name('admin.login-vendor');


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
