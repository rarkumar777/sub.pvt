<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\CannedDayController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GuaranteedDepartureController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\TripWizardController;

// Public Trip Wizard
Route::get('/create-trip', [TripWizardController::class, 'show'])->name('trip.wizard');
Route::post('/create-trip', [TripWizardController::class, 'store'])->name('trip.wizard.store');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/auto-login-dev', function() { Auth::login(\App\Models\User::first()); return redirect('/admin'); });

Route::get('/admin', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin', [LoginController::class, 'login'])->name('login.post');
Route::get('/admin/users/login', [LoginController::class, 'showLoginForm']);
Route::post('/admin/users/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/admin/register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('/admin/register', [LoginController::class, 'register'])->name('register.post');

// Password Reset Routes
Route::get('/admin/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/admin/forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/admin/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/admin/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['admin'])->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Request Manager
    Route::get('request-manager', [App\Http\Controllers\Admin\RequestManagerController::class, 'index'])->name('admin.request-manager');
    Route::get('request-manager/pipeline', [App\Http\Controllers\Admin\RequestManagerController::class, 'pipeline'])->name('admin.request-manager.pipeline');
    Route::get('request-manager/search-library', [App\Http\Controllers\Admin\RequestManagerController::class, 'searchLibrary'])->name('admin.request-manager.search-library');
    Route::post('request-manager/{id}/stage', [App\Http\Controllers\Admin\RequestManagerController::class, 'updateStage'])->name('admin.request-manager.stage');
    Route::post('request-manager/{id}/update-field', [App\Http\Controllers\Admin\RequestManagerController::class, 'updateField'])->name('admin.request-manager.update-field');
    Route::post('request-manager/{id}/read', [App\Http\Controllers\Admin\RequestManagerController::class, 'markRead'])->name('admin.request-manager.read');
    Route::get('request-manager/{id}', [App\Http\Controllers\Admin\RequestManagerController::class, 'show'])->name('admin.request-manager.show');
    Route::post('request-manager/{id}/message', [App\Http\Controllers\Admin\RequestManagerController::class, 'sendMessage'])->name('admin.request-manager.message');
    // Itinerary routes
    Route::post('request-manager/{id}/itinerary', [App\Http\Controllers\Admin\RequestManagerController::class, 'storeItinerary'])->name('admin.request-manager.itinerary.store');
    Route::put('request-manager/{id}/itinerary/{itinId}', [App\Http\Controllers\Admin\RequestManagerController::class, 'updateItinerary'])->name('admin.request-manager.itinerary.update');
    Route::post('request-manager/{id}/itinerary/{itinId}/day', [App\Http\Controllers\Admin\RequestManagerController::class, 'storeDay'])->name('admin.request-manager.day.store');
    Route::get('request-manager/{id}/itinerary/{itinId}/day/{dayId}', [App\Http\Controllers\Admin\RequestManagerController::class, 'showDay'])->name('admin.request-manager.day.show');
    Route::put('request-manager/{id}/itinerary/{itinId}/day/{dayId}', [App\Http\Controllers\Admin\RequestManagerController::class, 'updateDay'])->name('admin.request-manager.day.update');
    Route::delete('request-manager/{id}/itinerary/{itinId}/day/{dayId}', [App\Http\Controllers\Admin\RequestManagerController::class, 'deleteDay'])->name('admin.request-manager.day.delete');
    Route::post('request-manager/{id}/itinerary/{itinId}/day/reorder', [App\Http\Controllers\Admin\RequestManagerController::class, 'reorderDays'])->name('admin.request-manager.day.reorder');
    Route::get('request-manager/{id}/itinerary/{itinId}/service-total', [App\Http\Controllers\Admin\RequestManagerController::class, 'getServiceTotal'])->name('admin.request-manager.service-total');
    Route::post('request-manager/{id}/itinerary/{itinId}/service-qtys', [App\Http\Controllers\Admin\RequestManagerController::class, 'updateServiceQtys'])->name('admin.request-manager.service-qtys');
    // Day photo upload/delete
    Route::post('request-manager/{id}/itinerary/{itinId}/day/{dayId}/photo', [App\Http\Controllers\Admin\RequestManagerController::class, 'uploadDayPhoto'])->name('admin.request-manager.day.photo.upload');
    Route::delete('request-manager/{id}/itinerary/{itinId}/day/{dayId}/photo', [App\Http\Controllers\Admin\RequestManagerController::class, 'deleteDayPhoto'])->name('admin.request-manager.day.photo.delete');
    // Trip Planner tab pages (separate URLs)
    Route::get('request-manager/{id}/trip-planner/My-quote', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripPlanner'])->name('admin.request-manager.trip-planner.my-quote');
    Route::get('request-manager/{id}/trip-planner/daybyday', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripPlanner'])->name('admin.request-manager.trip-planner.daybyday');
    Route::get('request-manager/{id}/trip-planner/price', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripPlanner'])->name('admin.request-manager.trip-planner.price');
    // Trip Planner (full page)
    Route::get('request-manager/{id}/trip-planner', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripPlanner'])->name('admin.request-manager.trip-planner');
    Route::get('request-manager/{id}/trip-planner/preview', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripPreview'])->name('admin.request-manager.trip-preview');
    Route::get('request-manager/{id}/trip-planner/preview/my-trip', [App\Http\Controllers\Admin\RequestManagerController::class, 'tripQuote'])->name('admin.request-manager.trip-quote');
    Route::get('request-manager/{id}/generate-payment', [App\Http\Controllers\Admin\RequestManagerController::class, 'generatePayment'])->name('admin.request-manager.generate-payment');
    Route::post('request-manager/{id}/itinerary/{itinId}/sync-quote', [App\Http\Controllers\Admin\RequestManagerController::class, 'syncToQuotation'])->name('admin.request-manager.sync-quote');
    Route::get('request-manager/{id}/copy-itinerary/{oldItinId}', [App\Http\Controllers\Admin\RequestManagerController::class, 'copyItinerary'])->name('admin.request-manager.copy-itinerary');
    Route::post('request-manager/{id}/upload-cover', [App\Http\Controllers\Admin\RequestManagerController::class, 'uploadCover'])->name('admin.request-manager.upload-cover');
    Route::post('request-manager/{id}/convert-to-booking', [App\Http\Controllers\Admin\RequestManagerController::class, 'convertToBooking'])->name('admin.request-manager.convert-to-booking');
    Route::get('my-account', [DashboardController::class, 'myAccount'])->name('admin.my-account');
    Route::post('my-account', [DashboardController::class, 'updateMyAccount'])->name('admin.my-account.update');

    // Tours Module
    Route::match(['get','post'], 'tours/seasons', [TourController::class, 'globalSeasons'])->name('admin.tours-seasons');
    Route::get('tours/seasons/{id}/delete', [TourController::class, 'deleteGlobalSeason'])->name('admin.tours-seasons.delete');
    Route::get('tours/global-pricing', [TourController::class, 'globalPricing'])->name('admin.tours.global-pricing');
    Route::resource('tours', TourController::class)->names('admin.tours');
    Route::get('tours/{tour}/images', [TourController::class, 'images'])->name('admin.tours.images');
    Route::post('tours/{tour}/images', [TourController::class, 'storeImage'])->name('admin.tours.images.store');
    Route::delete('tours/{tour}/images/{image}', [TourController::class, 'destroyImage'])->name('admin.tours.images.destroy');
    Route::get('tours/{tour}/pricing', [TourController::class, 'pricing'])->name('admin.tours.pricing');
    Route::post('tours/{tour}/pricing', [TourController::class, 'updatePricing'])->name('admin.tours.pricing.update');

    Route::get('tours/{tour}/inclusions', [TourController::class, 'inclusions'])->name('admin.tours.inclusions');
    Route::post('tours/{tour}/inclusions', [TourController::class, 'updateInclusions'])->name('admin.tours.inclusions.update');

    Route::get('tours/{tour}/itinerary', [TourController::class, 'itinerary'])->name('admin.tours.itinerary');
    Route::post('tours/{tour}/itinerary', [TourController::class, 'updateItinerary'])->name('admin.tours.itinerary.update');

    Route::get('tours/{tour}/departures', [TourController::class, 'departures'])->name('admin.tours.departures');
    Route::get('tours/{tour}/menu', [TourController::class, 'menu'])->name('admin.tours.menu');
    Route::get('tour-categories', [TourController::class, 'categories'])->name('admin.tour-categories');
    Route::post('tour-categories', [TourController::class, 'storeCategory'])->name('admin.tour-categories.store');
    Route::delete('tour-categories/{id}', [TourController::class, 'destroyCategory'])->name('admin.tour-categories.destroy');
    Route::get('tour-categories/{id}/edit-ajax', [TourController::class, 'editCategoryAjax'])->name('admin.tour-categories.edit-ajax');
    Route::post('tour-categories/{id}/update', [TourController::class, 'updateCategory'])->name('admin.tour-categories.update');
    Route::get('tour-types', [TourController::class, 'types'])->name('admin.tour-types');
    Route::post('tour-types', [TourController::class, 'storeType'])->name('admin.tour-types.store');
    Route::delete('tour-types/{id}', [TourController::class, 'destroyType'])->name('admin.tour-types.destroy');
    Route::get('tour-types/{id}/edit-ajax', [TourController::class, 'editTypeAjax'])->name('admin.tour-types.edit-ajax');
    Route::post('tour-types/{id}/update', [TourController::class, 'updateType'])->name('admin.tour-types.update');
    Route::get('tour-inclusions', [TourController::class, 'globalInclusions'])->name('admin.tour-inclusions');
    Route::post('tour-inclusions', [TourController::class, 'storeGlobalInclusion'])->name('admin.tour-inclusions.store');
    Route::get('tour-inclusions/{id}/delete', [TourController::class, 'destroyGlobalInclusion'])->name('admin.tour-inclusions.destroy');
    Route::get('tour-inclusions/{id}/edit-ajax', [TourController::class, 'editInclusionAjax'])->name('admin.tour-inclusions.edit-ajax');
    Route::post('tour-inclusions/{id}/update', [TourController::class, 'updateInclusion'])->name('admin.tour-inclusions.update');
    Route::get('tour-tec', [TourController::class, 'globalTecDetails'])->name('admin.tour-tec');
    Route::post('tour-tec', [TourController::class, 'storeTecDetail'])->name('admin.tour-tec.store');
    Route::get('tour-tec/{id}/delete', [TourController::class, 'destroyTecDetail'])->name('admin.tour-tec.destroy');
    Route::get('tour-tec/{id}/edit-ajax', [TourController::class, 'editTecAjax'])->name('admin.tour-tec.edit-ajax');
    Route::post('tour-tec/{id}/update', [TourController::class, 'updateTecDetail'])->name('admin.tour-tec.update');

    Route::get('tour-settings', [TourController::class, 'tourSettings'])->name('admin.tour-settings');
    Route::post('tour-settings', [TourController::class, 'saveTourSettings'])->name('admin.tour-settings.save');

    // Bookings
    Route::resource('bookings', BookingController::class)->names('admin.bookings');
    Route::get('bookings/{booking}/travelers', [BookingController::class, 'travelers'])->name('admin.bookings.travelers');
    Route::post('bookings/{booking}/travelers', [BookingController::class, 'storeTraveler'])->name('admin.bookings.travelers.store');
    Route::get('bookings/{booking}/manifest', [BookingController::class, 'manifest'])->name('admin.bookings.manifest');
    Route::get('bookings/{booking}/mark-cancelled', [BookingController::class, 'markCancelled'])->name('admin.bookings.mark-cancelled');

    // Quotations
    Route::match(['get','post'], 'quotations/fast-access', [QuotationController::class, 'fastAccess'])->name('admin.quotation-fast-access');
    Route::match(['get','post'], 'quotations/email-templates', [QuotationController::class, 'emailTemplates'])->name('admin.quotation-email-templates');
    Route::resource('quotations', QuotationController::class)->names('admin.quotations');
    Route::get('quotations/{quotation}/copy', [QuotationController::class, 'copy'])->name('admin.quotations.copy');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('admin.quotations.send');
    Route::get('quotations/{quotation}/send-modal', [QuotationController::class, 'sendModal'])->name('admin.quotations.send-modal');
    Route::post('quotations/{quotation}/update-status', [QuotationController::class, 'updateStatus'])->name('admin.quotations.update-status');
    Route::get('quotations/{quotation}/days', [QuotationController::class, 'days'])->name('admin.quotations.days');
    Route::post('quotations/{quotation}/profit', [QuotationController::class, 'updateProfit'])->name('admin.quotations.profit');
    Route::post('quotations/{quotation}/validate', [QuotationController::class, 'validateQuotation'])->name('admin.quotations.validate');

    // Canned Days
    Route::get('canned-days/{id}/delete', [CannedDayController::class, 'destroy'])->name('admin.canned-days.destroy.get');
    Route::post('canned-days/store-ajax', [CannedDayController::class, 'storeAjax'])->name('admin.canned-days.store-ajax');
    Route::get('canned-days/{id}/edit-ajax', [CannedDayController::class, 'editAjax'])->name('admin.canned-days.edit-ajax');
    Route::post('canned-days/{id}/update-ajax', [CannedDayController::class, 'updateAjax'])->name('admin.canned-days.update-ajax');
    Route::get('canned-days/services-by-category', [CannedDayController::class, 'servicesByCategory'])->name('admin.canned-days.services-by-category');
    Route::get('canned-days/category-tree', [CannedDayController::class, 'categoryTree'])->name('admin.canned-days.category-tree');
    Route::get('canned-days/inclusion-items', [CannedDayController::class, 'inclusionItems'])->name('admin.canned-days.inclusion-items');
    Route::resource('canned-days', CannedDayController::class)->names('admin.canned-days');

    // Guaranteed Departures
    Route::resource('guaranteed-departures', GuaranteedDepartureController::class)->names('admin.guaranteed-departures');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->names('admin.invoices');
    Route::get('invoices/{invoice}/expenses', [InvoiceController::class, 'expenses'])->name('admin.invoices.expenses');
    Route::post('invoices/{invoice}/expenses/store', [InvoiceController::class, 'storeExpense'])->name('admin.invoices.expenses.store');
    Route::get('invoices/{invoice}/expenses/{expense}/edit-form', [InvoiceController::class, 'expenseEditForm'])->name('admin.invoices.expenses.edit-form');
    Route::post('invoices/{invoice}/expenses/{expense}/update', [InvoiceController::class, 'updateExpense'])->name('admin.invoices.expenses.update');
    Route::get('invoices/{invoice}/expenses/{expense}/delete', [InvoiceController::class, 'deleteExpense'])->name('admin.invoices.expenses.delete');
    Route::match(['get', 'post'], 'invoices/{invoice}/send', [InvoiceController::class, 'sendInvoice'])->name('admin.invoices.send');
    Route::get('invoices/{invoice}/transactions-ajax', [InvoiceController::class, 'transactionsAjax'])->name('admin.invoices.transactions');
    Route::get('invoices/{invoice}/transactions/add-form', [InvoiceController::class, 'transactionAddForm'])->name('admin.invoices.transactions.add-form');
    Route::get('invoices/{invoice}/transactions/{trans}/edit-form', [InvoiceController::class, 'transactionEditForm'])->name('admin.invoices.transactions.edit-form');
    Route::post('invoices/{invoice}/transactions/store', [InvoiceController::class, 'storeTransaction'])->name('admin.invoices.transactions.store');
    Route::post('invoices/{invoice}/transactions/{trans}/update', [InvoiceController::class, 'updateTransaction'])->name('admin.invoices.transactions.update');
    Route::get('invoices/{invoice}/transactions/{trans}/delete', [InvoiceController::class, 'deleteTransaction'])->name('admin.invoices.transactions.delete');

    // Customer Ledger
    Route::get('customers/ledger', [App\Http\Controllers\Admin\CustomerLedgerController::class, 'index'])->name('admin.customers.ledger');
    Route::get('customers/ledger/{id}', [App\Http\Controllers\Admin\CustomerLedgerController::class, 'account'])->name('admin.customers.ledger.account');

    // Services
    Route::resource('services', ServiceController::class)->names('admin.services');
    Route::get('services-ajax', [ServiceController::class, 'getServices'])->name('admin.services.ajax');
    Route::get('services-venders', [ServiceController::class, 'venders'])->name('admin.services.venders');
    Route::get('services-venders/{id}/account', [ServiceController::class, 'venderAccount'])->name('admin.services.venders.account');
    Route::get('services-venders/{id}/description', [ServiceController::class, 'venderDescription'])->name('admin.services.venders.description');
    Route::post('services-venders/{id}/description', [ServiceController::class, 'updateVenderDescription'])->name('admin.services.venders.description.update');
    Route::get('services-settings', [ServiceController::class, 'settings'])->name('admin.services.settings');
    Route::post('services-settings', [ServiceController::class, 'updateSettings'])->name('admin.services.settings.update');
    Route::get('services-category/create', [ServiceController::class, 'addCategoryModal'])->name('admin.services.category.create');
    Route::get('services-category/{id}/edit', [ServiceController::class, 'editCategoryModal'])->name('admin.services.category.edit');
    Route::post('services-store-category', [ServiceController::class, 'storeCategory'])->name('admin.services.store-category');
    Route::post('services-category/{id}/update', [ServiceController::class, 'updateCategory'])->name('admin.services.category.update');
    Route::delete('services-category/{id}', [ServiceController::class, 'destroyCategory'])->name('admin.services.category.destroy');
    Route::get('services/{service}/seasons', [ServiceController::class, 'getSeasons'])->name('admin.services.seasons');
    Route::post('services/{service}/seasons', [ServiceController::class, 'addSeason'])->name('admin.services.seasons.add');
    Route::put('services/seasons/{season}', [ServiceController::class, 'updateSeason'])->name('admin.services.seasons.update');
    Route::delete('services/seasons/{season}', [ServiceController::class, 'deleteSeason'])->name('admin.services.seasons.delete');

    // Library (Evaneos-style)
    Route::get('library', [ServiceController::class, 'library'])->name('admin.library');
    Route::get('library/filter', [ServiceController::class, 'libraryFilter'])->name('admin.library.filter');
    Route::get('library/services-by-category', [ServiceController::class, 'getServicesByCategory'])->name('admin.library.services-by-category');
    Route::get('library/vendor-services-table/{id}', [ServiceController::class, 'getVendorServicesTable'])->name('admin.library.vendor-services-table');
    Route::get('library/days', [ServiceController::class, 'libraryDays'])->name('admin.library.days');
    Route::post('services/quick-add', [ServiceController::class, 'quickAdd'])->name('admin.services.quick-add');
    Route::post('transports/quick-add', [ServiceController::class, 'quickAddTransport'])->name('admin.transports.quick-add');
    Route::post('guides/quick-add', [ServiceController::class, 'quickAddGuide'])->name('admin.guides.quick-add');
    Route::post('activities/quick-add', [ServiceController::class, 'quickAddActivity'])->name('admin.activities.quick-add');
    Route::post('restaurants/quick-add', [ServiceController::class, 'quickAddRestaurant'])->name('admin.restaurants.quick-add');

    // Expenses
    Route::get('expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::get('expenses/mark-all-completed', [ExpenseController::class, 'markAllCompleted'])->name('admin.expenses.mark-all-completed');
    Route::get('expenses/services', [ExpenseController::class, 'getServices'])->name('admin.expenses.services');
    Route::get('expenses/service-detail', [ExpenseController::class, 'getServiceDetail'])->name('admin.expenses.service-detail');
    Route::post('expenses/store', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('admin.expenses.edit');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('admin.expenses.update');
    Route::get('expenses/{expense}/history', [ExpenseController::class, 'history'])->name('admin.expenses.history');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');

    // Users
    Route::resource('users', UserController::class)->names('admin.users');
    Route::get('user-groups', [UserController::class, 'groups'])->name('admin.user-groups.index');
    Route::post('user-groups', [UserController::class, 'storeGroup'])->name('admin.user-groups.store');
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('admin.users.permissions');
    Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('admin.users.permissions.update');
    Route::get('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::get('user-groups/{group}/fields', [UserController::class, 'groupFields'])->name('admin.user-groups.fields');
    Route::post('user-groups/{group}/fields', [UserController::class, 'updateGroupFields'])->name('admin.user-groups.fields.update');

    // CMS
    Route::prefix('cms')->group(function () {
        Route::get('sliders', [CmsController::class, 'sliders'])->name('admin.sliders.index');
        Route::post('sliders', [CmsController::class, 'storeSlider'])->name('admin.sliders.store');
        Route::get('sliders/{slider}/edit', [CmsController::class, 'editSlider'])->name('admin.sliders.edit');
        Route::put('sliders/{slider}', [CmsController::class, 'updateSlider'])->name('admin.sliders.update');
        Route::delete('sliders/{slider}', [CmsController::class, 'deleteSlider'])->name('admin.sliders.delete');
        Route::get('sliders/{slider}/images', [CmsController::class, 'sliderImages'])->name('admin.sliders.images');
        Route::post('sliders/{slider}/images', [CmsController::class, 'storeSliderImage'])->name('admin.sliders.images.store');
        Route::delete('sliders/{slider}/images/{image}', [CmsController::class, 'deleteSliderImage'])->name('admin.sliders.images.delete');
        Route::get('sliders/{slider}/images/{image}/delete', [CmsController::class, 'deleteSliderImage'])->name('admin.sliders.images.delete.get');
        Route::get('sliders/{slider}/images/{image}/edit', [CmsController::class, 'editSliderImage'])->name('admin.sliders.images.edit');
        Route::put('sliders/{slider}/images/{image}', [CmsController::class, 'updateSliderImage'])->name('admin.sliders.images.update');
        Route::get('pages', [CmsController::class, 'pages'])->name('admin.pages.index');
        Route::post('pages/{page}/toggle-status', [CmsController::class, 'togglePageStatus'])->name('admin.pages.toggle-status');
        Route::get('pages/create', [CmsController::class, 'createPage'])->name('admin.pages.create');
        Route::post('pages', [CmsController::class, 'storePage'])->name('admin.pages.store');
        Route::get('pages/{page}/edit', [CmsController::class, 'editPage'])->name('admin.pages.edit');
        Route::put('pages/{page}', [CmsController::class, 'updatePage'])->name('admin.pages.update');
        Route::delete('pages/{page}', [CmsController::class, 'destroyPage'])->name('admin.pages.destroy');
        Route::get('nav', [CmsController::class, 'nav'])->name('admin.nav.index');
        Route::post('nav', [CmsController::class, 'storeNavLink'])->name('admin.nav.store');
        Route::post('nav/save-order', [CmsController::class, 'saveNavOrder'])->name('admin.nav.save-order');
        Route::get('nav/{navLink}/edit', [CmsController::class, 'editNavLink'])->name('admin.nav.edit');
        Route::put('nav/{navLink}', [CmsController::class, 'updateNavLink'])->name('admin.nav.update');
        Route::get('nav/{id}/delete', [CmsController::class, 'deleteNavLink'])->name('admin.nav.delete');
        Route::get('blocks', [CmsController::class, 'blocks'])->name('admin.blocks.index');
        Route::get('blocks/{block}/edit', [CmsController::class, 'editBlock'])->name('admin.blocks.edit');
        Route::get('custom-blocks', [CmsController::class, 'customBlocks'])->name('admin.customblocks.index');
        Route::post('custom-blocks', [CmsController::class, 'storeCustomBlock'])->name('admin.customblocks.store');
        Route::get('custom-blocks/{name}/edit', [CmsController::class, 'editCustomBlock'])->name('admin.customblocks.edit');
        Route::post('custom-blocks/{name}/update', [CmsController::class, 'updateCustomBlock'])->name('admin.customblocks.update');
        Route::get('custom-blocks/{name}/delete', [CmsController::class, 'deleteCustomBlock'])->name('admin.customblocks.delete');
        Route::get('footer', [CmsController::class, 'footer'])->name('admin.footer.index');
        Route::post('footer', [CmsController::class, 'updateFooter'])->name('admin.footer.update');
    });

    // Quotation Pricing (global)
    Route::get('quotation-pricing', [QuotationController::class, 'pricing'])->name('admin.quotation-pricing.index');
    Route::post('quotation-pricing', [QuotationController::class, 'storePricing'])->name('admin.quotation-pricing.store');
    Route::get('quotation-pricing/{id}/edit', [QuotationController::class, 'editPricing'])->name('admin.quotation-pricing.edit');
    Route::put('quotation-pricing/{id}', [QuotationController::class, 'updatePricing'])->name('admin.quotation-pricing.update');
    Route::get('quotation-pricing/{id}/delete', [QuotationController::class, 'destroyPricing'])->name('admin.quotation-pricing.destroy');

    // Quotation Sub-Pages
    Route::get('quotations/{quotation}/email-template', [QuotationController::class, 'emailTemplate'])->name('admin.quotations.email-template');


    // Booking Sub-Pages
    Route::match(['get','post'], 'bookings/guaranteed/{booking}/edit', [BookingController::class, 'editGuaranteed'])->name('admin.bookings.guaranteed.edit');

    // Other
    Route::get('auto-advisor', [DashboardController::class, 'autoAdvisor'])->name('admin.auto-advisor');

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('global', [SettingsController::class, 'global'])->name('admin.settings.global');
        Route::post('global', [SettingsController::class, 'updateGlobal'])->name('admin.settings.global.update');
        Route::get('countries', [SettingsController::class, 'countries'])->name('admin.settings.countries');
        Route::post('countries', [SettingsController::class, 'storeCountry'])->name('admin.settings.countries.store');
        Route::put('countries/{id}', [SettingsController::class, 'updateCountry'])->name('admin.settings.countries.update');
        Route::delete('countries/{id}', [SettingsController::class, 'deleteCountry'])->name('admin.settings.countries.delete');
        Route::get('currency', [SettingsController::class, 'currency'])->name('admin.settings.currency');
        Route::post('currency', [SettingsController::class, 'storeCurrency'])->name('admin.settings.currency.store');
        Route::put('currency/{id}', [SettingsController::class, 'updateCurrency'])->name('admin.settings.currency.update');
        Route::delete('currency/{id}', [SettingsController::class, 'deleteCurrency'])->name('admin.settings.currency.delete');
        Route::get('company-profile', [SettingsController::class, 'companyProfile'])->name('admin.settings.company-profile');
        Route::post('company-profile', [SettingsController::class, 'updateCompanyProfile'])->name('admin.settings.company-profile.update');
        Route::get('languages', [SettingsController::class, 'languages'])->name('admin.settings.languages');
        Route::post('languages', [SettingsController::class, 'storeLanguage'])->name('admin.settings.languages.store');
        Route::post('languages/{code}/toggle', [SettingsController::class, 'toggleLanguage'])->name('admin.settings.languages.toggle');
        Route::delete('languages/{code}', [SettingsController::class, 'deleteLanguage'])->name('admin.settings.languages.delete');
        Route::get('email-templates/{mod?}', [SettingsController::class, 'emailTemplates'])->name('admin.settings.email-templates');
        Route::get('email-templates/{mod}/{key}/edit', [SettingsController::class, 'editEmailTemplate'])->name('admin.settings.email-templates.edit');
        Route::post('email-templates/{mod}/{key}/update', [SettingsController::class, 'updateEmailTemplate'])->name('admin.settings.email-templates.update');
        Route::get('seo', [SettingsController::class, 'seo'])->name('admin.settings.seo');
        Route::post('seo', [SettingsController::class, 'updateSeo'])->name('admin.settings.seo.update');
        Route::get('on-page-seo', [SettingsController::class, 'onPageSeo'])->name('admin.settings.on-page-seo');
        Route::get('sitemap', [SettingsController::class, 'sitemap'])->name('admin.settings.sitemap');
        Route::get('links', [SettingsController::class, 'links'])->name('admin.settings.links');
        Route::get('payments', [SettingsController::class, 'payments'])->name('admin.settings.payments');
        Route::post('payments/{gateway}/update', [SettingsController::class, 'updatePaymentConfig'])->name('admin.settings.payments.update');
        Route::get('layouts', [SettingsController::class, 'layouts'])->name('admin.settings.layouts');
        Route::post('layouts', [SettingsController::class, 'storeLayout'])->name('admin.layouts.store');
        Route::get('layouts/{name}/delete', [SettingsController::class, 'deleteLayout'])->name('admin.layouts.delete')->where('name', '.*');
        Route::get('layouts/{name}/blocks', [SettingsController::class, 'layoutBlocks'])->name('admin.layouts.blocks')->where('name', '.*');
        Route::post('layouts/{name}/blocks/save', [SettingsController::class, 'saveLayoutBlocks'])->name('admin.layouts.blocks.save')->where('name', '.*');
        Route::get('layouts/blocks/get-blocks', [SettingsController::class, 'getBlocks'])->name('admin.layouts.blocks.get-blocks');
        Route::get('layout-settings', [SettingsController::class, 'layoutSettings'])->name('admin.settings.layout-settings');
        Route::post('layout-settings', [SettingsController::class, 'saveLayoutSettings'])->name('admin.settings.layout-settings.save');
        Route::get('destinations', [SettingsController::class, 'destinations'])->name('admin.settings.destinations');
        Route::post('destinations', [SettingsController::class, 'storeDestination'])->name('admin.settings.destinations.store');
        Route::get('cities', [SettingsController::class, 'cities'])->name('admin.settings.cities');
        Route::post('cities', [SettingsController::class, 'storeCity'])->name('admin.settings.cities.store');
        Route::get('backup', [SettingsController::class, 'backup'])->name('admin.settings.backup');
        Route::get('file-manager', [SettingsController::class, 'fileManager'])->name('admin.settings.file-manager');
        Route::post('file-manager/upload', [SettingsController::class, 'fileManagerUpload'])->name('admin.settings.file-manager.upload');
        Route::post('file-manager/new-folder', [SettingsController::class, 'fileManagerNewFolder'])->name('admin.settings.file-manager.new-folder');
        Route::post('file-manager/delete-file', [SettingsController::class, 'fileManagerDeleteFile'])->name('admin.settings.file-manager.delete-file');
        Route::post('file-manager/delete-folder', [SettingsController::class, 'fileManagerDeleteFolder'])->name('admin.settings.file-manager.delete-folder');
        Route::post('file-manager/rename', [SettingsController::class, 'fileManagerRename'])->name('admin.settings.file-manager.rename');
        Route::get('modules', [SettingsController::class, 'modules'])->name('admin.settings.modules');
        Route::get('modules/toggle/{id}', [SettingsController::class, 'toggleModule'])->name('admin.settings.modules.toggle');
        Route::get('translations', [SettingsController::class, 'translations'])->name('admin.settings.translations');
    });

    // AJAX Endpoints
    Route::prefix('ajax')->group(function () {
        Route::post('search-services', [AjaxController::class, 'searchServices'])->name('admin.ajax.search-services');
        Route::post('add-expense', [AjaxController::class, 'addExpense'])->name('admin.ajax.add-expense');
        Route::post('calculate-quotation', [AjaxController::class, 'calculateQuotation'])->name('admin.ajax.calculate-quotation');
        Route::post('delete-expense', [AjaxController::class, 'deleteExpense'])->name('admin.ajax.delete-expense');
        Route::get('check-user', [AjaxController::class, 'checkUser'])->name('admin.ajax.check-user');
        Route::post('get-service-details', [AjaxController::class, 'getServiceDetails'])->name('admin.ajax.get-service-details');
        Route::post('get-cities', [AjaxController::class, 'getCities'])->name('admin.ajax.get-cities');
        Route::get('get-inclusions', [AjaxController::class, 'getInclusions'])->name('admin.ajax.get-inclusions');
        Route::post('save-quotation-day', [AjaxController::class, 'saveQuotationDay'])->name('admin.ajax.save-quotation-day');
        Route::get('file-manager-browse', [AjaxController::class, 'fileManagerBrowse'])->name('admin.ajax.file-manager-browse');
        Route::post('file-manager-upload', [AjaxController::class, 'fileManagerUpload'])->name('admin.ajax.file-manager-upload');
        Route::post('file-manager-create-folder', [AjaxController::class, 'fileManagerCreateFolder'])->name('admin.ajax.file-manager-create-folder');
        Route::get('get-country-categories', [AjaxController::class, 'getCountryCategories'])->name('admin.ajax.get-country-categories');
        Route::get('get-subcategories', [AjaxController::class, 'getSubcategories'])->name('admin.ajax.get-subcategories');
    });
});

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $defaultLang = 'en';
    $configPath = base_path('../pvt.jo/config/global.php');
    if (file_exists($configPath)) {
        $content = file_get_contents($configPath);
        if (preg_match("/\\\$GOGIES\['lang'\]='([^']*)'/", $content, $m)) {
            $defaultLang = $m[1] ?: 'en';
        }
    }
    return redirect('/' . $defaultLang . '/');
});

Route::middleware(['website.offline'])->group(function () {

// Frontend Home
Route::get('/{lang}/', [\App\Http\Controllers\FrontendController::class, 'home'])->name('frontend.home')->where('lang', '[a-zA-Z]{2}');

// Frontend Tours Listing
Route::match(['get','post'], '/{lang}/tours/', [\App\Http\Controllers\FrontendController::class, 'toursList'])->name('frontend.tours')->where('lang', '[a-zA-Z]{2}');

// Frontend General Inquiry
Route::get('/{lang}/inquiry/', [\App\Http\Controllers\FrontendController::class, 'inquiry'])->name('frontend.inquiry')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/inquiry/', [\App\Http\Controllers\FrontendController::class, 'submitInquiry'])->name('frontend.inquiry.submit')->where('lang', '[a-zA-Z]{2}');

// Frontend Quotation View
Route::get('/{lang}/tours/quotation/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'quotation'])->name('frontend.quotation.show')->where('lang', '[a-zA-Z]{2}');

// Frontend Tour Inquery (Quotation Form Submission)
Route::get('/{lang}/tours/inquery/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'showInquery'])->name('frontend.tour.inquery.show')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/tours/inquery/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'inquery'])->name('frontend.tour.inquery')->where('lang', '[a-zA-Z]{2}');

// Frontend Tour Booking (2-step: show confirmation then place booking)
Route::get('/{lang}/tours/book_tour/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'showBookTour'])->name('frontend.tour.book.show')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/tours/book_tour/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'bookTour'])->name('frontend.tour.book')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/tours/booking_success/{id}', [\App\Http\Controllers\FrontendTourController::class, 'bookingSuccess'])->name('frontend.tour.booking_success')->where('lang', '[a-zA-Z]{2}');

// Frontend Invoice View
Route::get('/{lang}/invoice/{id}/', [\App\Http\Controllers\FrontendTourController::class, 'showInvoice'])->name('frontend.invoice.show')->where('lang', '[a-zA-Z]{2}');

// Payment Routes
Route::post('/{lang}/invoice/{id}/pay', [\App\Http\Controllers\PaymentController::class, 'initiatePayment'])->name('frontend.payment.initiate')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/invoice/{id}/simulate-success', [\App\Http\Controllers\PaymentController::class, 'simulateSuccess'])->name('frontend.payment.simulate')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/payment/migs/return', [\App\Http\Controllers\PaymentController::class, 'migsReturn'])->name('frontend.payment.migs.return')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/payment/paytabs/callback', [\App\Http\Controllers\PaymentController::class, 'paytabsCallback'])->name('frontend.payment.paytabs.callback')->where('lang', '[a-zA-Z]{2}');
Route::match(['get', 'post'], '/{lang}/payment/paytabs/return', [\App\Http\Controllers\PaymentController::class, 'paytabsReturn'])->name('frontend.payment.paytabs.return')->where('lang', '[a-zA-Z]{2}');

// Booking-flow PayTabs payment routes (reference site style)
Route::any('/payment/return', [\App\Http\Controllers\PaymentController::class, 'handleReturn'])->name('payment.return');
Route::post('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'handleCallback'])->name('payment.callback');

// Frontend Booking Detail (public view of booking expenses/services)
Route::get('/{lang}/tours/booking/{bookingSlug}/', [\App\Http\Controllers\FrontendTourController::class, 'showBooking'])->name('frontend.booking.show')->where('lang', '[a-zA-Z]{2}');

// Frontend Tour View
Route::get('/{lang}/tours/{country}/{slug}', [\App\Http\Controllers\FrontendTourController::class, 'show'])->name('frontend.tour.show')->where('lang', '[a-zA-Z]{2}');

// Frontend User Auth Routes
Route::get('/{lang}/users/login/', [\App\Http\Controllers\FrontendAuthController::class, 'showLogin'])->name('frontend.login')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/login/', [\App\Http\Controllers\FrontendAuthController::class, 'login'])->name('frontend.login.post')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/register/', [\App\Http\Controllers\FrontendAuthController::class, 'showRegister'])->name('frontend.register')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/register/', [\App\Http\Controllers\FrontendAuthController::class, 'register'])->name('frontend.register.post')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/account/my-bookings/', [\App\Http\Controllers\FrontendAuthController::class, 'showMyBookings'])->name('frontend.account.bookings')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/account/my-messages/', [\App\Http\Controllers\FrontendAuthController::class, 'showMyMessages'])->name('frontend.account.messages')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/account/my-messages/send', [\App\Http\Controllers\FrontendAuthController::class, 'sendMessage'])->name('frontend.account.messages.send')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/account/edit-account/', [\App\Http\Controllers\FrontendAuthController::class, 'showEditAccount'])->name('frontend.account.edit')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/account/edit-account/', [\App\Http\Controllers\FrontendAuthController::class, 'updateAccount'])->name('frontend.account.update')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/logout/', [\App\Http\Controllers\FrontendAuthController::class, 'logout'])->name('frontend.logout')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/forgot-password/', [\App\Http\Controllers\FrontendAuthController::class, 'showForgotPassword'])->name('frontend.forgot.show')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/forgot-password/', [\App\Http\Controllers\FrontendAuthController::class, 'sendForgotPassword'])->name('frontend.forgot.send')->where('lang', '[a-zA-Z]{2}');
Route::get('/{lang}/users/reset-password/{token}', [\App\Http\Controllers\FrontendAuthController::class, 'showResetPassword'])->name('frontend.reset.show')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/users/reset-password/', [\App\Http\Controllers\FrontendAuthController::class, 'resetPassword'])->name('frontend.reset.update')->where('lang', '[a-zA-Z]{2}');

// Frontend Contact Us
Route::get('/{lang}/contact-us/', [\App\Http\Controllers\FrontendController::class, 'contactUs'])->name('frontend.contact-us')->where('lang', '[a-zA-Z]{2}');
Route::post('/{lang}/contact-us/', [\App\Http\Controllers\FrontendController::class, 'submitContactUs'])->name('frontend.contact-us.submit')->where('lang', '[a-zA-Z]{2}');

// Frontend CMS Page (catch-all — must be last)
Route::get('/{lang}/{slug}', [\App\Http\Controllers\FrontendController::class, 'page'])->name('frontend.page')->where('lang', '[a-zA-Z]{2}');

}); // end website.offline middleware group

require base_path('routes/test_dev.php');
