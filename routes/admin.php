<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\FinancialYearsController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\DesignationsController;
use App\Http\Controllers\Admin\DiseasesController;
use App\Http\Controllers\Admin\OpdController;



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



Route::prefix('admin')->group(function(){
	
    #account setup
    Route::get('/',[AdminController::class, 'login'])->name('admin.login');
    Route::get('/login',[AdminController::class, 'login'])->name('admin.login');

    #dashboard setup
    Route::get('/dashboard',[AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin-login',[AdminController::class, 'admin_login'])->name('admin.admin_login');
    Route::get('/logout',[AdminController::class, 'logout'])->name('admin.logout');	
	
	#accounts
    Route::get('/accounts',[AccountsController::class, 'getList'])->name('admin.accounts');
    Route::any('/accounts_paginate',[AccountsController::class, 'listPaginate'])->name('admin.accounts_paginate');
	Route::any('/edit-account/{row_id}',[AccountsController::class, 'editPage'])->name('admin.edit-account');
	Route::any('/add-account',[AccountsController::class, 'addPage'])->name('admin.add-accounts');

	#super admins
    Route::get('/admins',[AdminsController::class, 'getList'])->name('admin.admins');
    Route::any('/admins_paginate',[AdminsController::class, 'listPaginate'])->name('admin.admins_paginate');
	Route::any('/edit-admin/{row_id}',[AdminsController::class, 'editPage'])->name('admin.edit-admins');
	Route::any('/add-admin',[AdminsController::class, 'addPage'])->name('admin.add-admins');
	
	#ajax
	Route::post('/change-status',[AjaxController::class, 'changeStatus'])->name('admin.change-status');
    Route::post('/delete-record',[AjaxController::class, 'deleteRecord'])->name('admin.delete-record');
	Route::post('/update-new-order',[AjaxController::class, 'updateNewOrder'])->name('admin.update-new-order');
	
	#settings
    Route::get('/settings',[ProfileController::class, 'settings'])->name('admin.settings');
	Route::post('/save-setting',[ProfileController::class, 'saveSetting'])->name('admin.save-setting');

    #update profile
    Route::get('/update-profile',[ProfileController::class, 'updateProfile'])->name('admin.update-profile');
    Route::post('/save-profile',[ProfileController::class, 'saveProfile'])->name('admin.save-profile');

    #change password
    Route::get('/change-password',[ProfileController::class, 'changePassword'])->name('admin.change-password');
    Route::post('/update-password',[ProfileController::class, 'updatePassword'])->name('admin.dashboard');
	
	#Users
    Route::get('/users',[UsersController::class, 'getList'])->name('admin.users');
    Route::any('/users_paginate',[UsersController::class, 'listPaginate'])->name('admin.users_paginate');
    Route::any('/add-user',[UsersController::class, 'addPage'])->name('admin.add-user');
    Route::any('/edit-user/{row_id}',[UsersController::class, 'editPage'])->name('admin.edit-user');

	#Financial Years (General Managers)
    Route::get('/financial-years',[FinancialYearsController::class, 'getList'])->name('admin.financial-years');
    Route::any('/financial-years_paginate',[FinancialYearsController::class, 'listPaginate'])->name('admin.financial-years_paginate');
    Route::any('/add-financial-year',[FinancialYearsController::class, 'addPage'])->name('admin.add-financial-year');
    Route::any('/edit-financial-year/{row_id}',[FinancialYearsController::class, 'editPage'])->name('admin.edit-financial-year');

	#Departments (General Managers)
    Route::get('/departments',[DepartmentsController::class, 'getList'])->name('admin.departments');
    Route::any('/departments_paginate',[DepartmentsController::class, 'listPaginate'])->name('admin.departments_paginate');
    Route::any('/add-department',[DepartmentsController::class, 'addPage'])->name('admin.add-department');
    Route::any('/edit-department/{row_id}',[DepartmentsController::class, 'editPage'])->name('admin.edit-department');

	#Designations (General Managers)
    Route::get('/designations',[DesignationsController::class, 'getList'])->name('admin.designations');
    Route::any('/designations_paginate',[DesignationsController::class, 'listPaginate'])->name('admin.designations_paginate');
    Route::any('/add-designation',[DesignationsController::class, 'addPage'])->name('admin.add-designation');
    Route::any('/edit-designation/{row_id}',[DesignationsController::class, 'editPage'])->name('admin.edit-designation');

	#Diseases (General Managers)
    Route::get('/diseases',[DiseasesController::class, 'getList'])->name('admin.diseases');
    Route::any('/diseases_paginate',[DiseasesController::class, 'listPaginate'])->name('admin.diseases_paginate');
    Route::any('/add-disease',[DiseasesController::class, 'addPage'])->name('admin.add-disease');
    Route::any('/edit-disease/{row_id}',[DiseasesController::class, 'editPage'])->name('admin.edit-disease');

	#OPD (OPD Manager)
    Route::any('/new-opd-registration',[OpdController::class, 'newOpdRegistration'])->name('admin.new-opd-registration');
    Route::any('/re-schedule-opd',[OpdController::class, 'reScheduleOpd'])->name('admin.re-schedule-opd');
	
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();

});