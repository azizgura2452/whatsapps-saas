<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\BroadcastController;
use App\Http\Controllers\Backend\ActionLogController;
use App\Http\Controllers\Backend\CustomersController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ModulesController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PermissionsController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\ProfilesController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\UserLoginAsController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\WhatsAppTemplatesController;
use App\Http\Controllers\MyFatoorahController;
use App\Http\Controllers\ProductFeedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/migrate', function () {
    try {
        \Artisan::call('migrate', ['--force' => true]);
        return 'Migration completed successfully!';
    } catch (\Exception $e) {
        return 'Migration failed: ' . $e->getMessage();
    }
});


Route::get('/fix-vite', function () {
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    return 'Config cleared and cached!';
});

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/send-payment', [MyFatoorahController::class, 'index'])->name('send.payment');
Route::get('/payment-callback', [MyFatoorahController::class, 'callback'])->name('myfatoorah.callback');
// Route::get('/payment-error', [MyFatoorahController::class, 'paymentError'])->name('payment.error');
Route::get('/payment-success', [MyFatoorahController::class, 'paymentSuccess'])->name('myfatoorah.success');
Route::get('/product-feed', [ProductFeedController::class, 'generateFeed']);


/**
 * Admin routes.
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RolesController::class);

    // Permissions Routes.
    Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{id}', [PermissionsController::class, 'show'])->name('permissions.show');

    // Modules Routes.
    Route::get('/modules', [ModulesController::class, 'index'])->name('modules.index');
    Route::post('/modules/toggle-status/{module}', [ModulesController::class, 'toggleStatus'])->name('modules.toggle-status');
    Route::post('/modules/upload', [ModulesController::class, 'upload'])->name('modules.upload');
    Route::delete('/modules/{module}', [ModulesController::class, 'destroy'])->name('modules.delete');

    // Settings Routes.
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

    // Translation Routes
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');

    // Login as & Switch back
    Route::resource('users', UsersController::class);
    Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
    Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

    // Add this before the resource route
    Route::get('customers/{customer}/orders', [CustomersController::class, 'orders'])->name('customers.orders');
    Route::get('customers/{customerId}/chat', [CustomersController::class, 'chat'])->name('customers.chat');
    Route::get('customers/{customer}/messages', [CustomersController::class, 'fetchMessages'])->name('customers.messages');

    // Resource routes for customers (index, create, store, etc.)
    Route::resource('customers', CustomersController::class);
    Route::resource('whatsapp-templates', WhatsAppTemplatesController::class);

    Route::resource('broadcasts', BroadcastController::class);

    Route::resource('products', ProductController::class)->except(['show'])->names('products');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');


    Route::resource('orders', OrderController::class)->names('orders');
    Route::get('orders/{product}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Action Log Routes.
    Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');
});

/**
 * Profile routes.
 */
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    Route::get('/edit', [ProfilesController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfilesController::class, 'update'])->name('update');
});

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
