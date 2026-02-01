<?php



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ImportController;



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



    Route::any('/import-ailments',[ImportController::class, 'importAilments'])->name('imports.ailments');

	Route::any('/import-lab-test',[ImportController::class, 'importLabTest'])->name('imports.lab_test');

	Route::any('/import-speciality',[ImportController::class, 'importSpeciality'])->name('imports.speciality');

	Route::any('/import-category',[ImportController::class, 'importCategory'])->name('imports.category');

	Route::any('/import-barnd',[ImportController::class, 'importBrand'])->name('imports.barnd');

	Route::any('/import-product',[ImportController::class, 'importProduct'])->name('imports.product');

	Route::any('/import-Ingredients',[ImportController::class, 'importIngredients'])->name('imports.Ingredients');

	Route::any('/import-language',[ImportController::class, 'importLanguage'])->name('imports.language');

	Route::any('/import-language-proficiency',[ImportController::class, 'importLanguageProficiency'])->name('imports.language.proficiency');

	Route::any('/import-sub-category',[ImportController::class, 'importSubCategory'])->name('imports.sub.category');

	Route::any('/import-vendor-plan-category',[ImportController::class, 'importVendorPlanCategory'])->name('imports.vendor.plan.category');

	Route::any('/import-vendor-plan',[ImportController::class, 'importVendorPlan'])->name('imports.vendor.plan');

	Route::any('/import-vendor',[ImportController::class, 'importVendor'])->name('imports.vendor');

	Route::any('/import-shipping-method',[ImportController::class, 'importShippingMethod'])->name('imports.shipping.method');

	Route::any('/import-coupon-code',[ImportController::class, 'importCouponCode'])->name('imports.coupon.code');

	Route::any('/import-payment-method',[ImportController::class, 'importPaymentMethod'])->name('imports.payment.method');

	Route::any('/import-tax',[ImportController::class, 'importTax'])->name('imports.tax');

	Route::any('/import-review',[ImportController::class, 'importReview'])->name('imports.review');

	Route::any('/import-products',[ImportController::class, 'importProducts'])->name('imports.products');
	
	Route::any('/import-customer',[ImportController::class, 'importCustomer'])->name('imports.customer');


});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();

});

