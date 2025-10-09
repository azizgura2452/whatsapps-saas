<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\BroadcastController;
use App\Http\Controllers\Backend\BroadcastGroupController;
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
use App\Http\Controllers\Backend\WhatsAppChatboxController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Utility
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
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    \Artisan::call('config:cache');

    return 'Config cleared and cached!';
});

// Other explicit routes
Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/send-payment', [MyFatoorahController::class, 'index'])->name('send.payment');
Route::get('/payment-callback', [MyFatoorahController::class, 'callback'])->name('myfatoorah.callback');
Route::get('/payment-success', [MyFatoorahController::class, 'paymentSuccess'])->name('myfatoorah.success');
Route::get('/product-feed', [ProductFeedController::class, 'generateFeed']);

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');

// Admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/testmail', [DashboardController::class, 'sendTestEmail'])->name('dashboard.sendEmail');
    Route::resource('roles', RolesController::class);

    Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{id}', [PermissionsController::class, 'show'])->name('permissions.show');

    Route::get('/modules', [ModulesController::class, 'index'])->name('modules.index');
    Route::post('/modules/toggle-status/{module}', [ModulesController::class, 'toggleStatus'])->name('modules.toggle-status');
    Route::post('/modules/upload', [ModulesController::class, 'upload'])->name('modules.upload');
    Route::delete('/modules/{module}', [ModulesController::class, 'destroy'])->name('modules.delete');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');

    Route::resource('users', UsersController::class);
    Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
    Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

    Route::get('customers/{customer}/orders', [CustomersController::class, 'orders'])->name('customers.orders');
    Route::get('customers/{customerId}/chat', [CustomersController::class, 'chat'])->name('customers.chat');
    Route::get('customers/{customer}/messages', [CustomersController::class, 'fetchMessages'])->name('customers.messages');
    Route::get('customers/attribute-values/{key}', [CustomersController::class, 'getAttributeValues'])
        ->name('customers.attribute-values');
    Route::post('customers/{customer}/send-message', [CustomersController::class, 'sendMessage'])
        ->name('customers.sendMessage');
    Route::post('customers/import', [CustomersController::class, 'importCustomers'])
        ->name('customers.import');
    Route::get('customers/download-template', [CustomersController::class, 'downloadTemplate'])
        ->name('customers.download-template');
    Route::resource('customers', CustomersController::class);

    
    Route::resource('whatsapp-templates', WhatsAppTemplatesController::class);
    // Broadcasts
    Route::resource('broadcasts', BroadcastController::class);
    Route::get('broadcasts/{id}/report', [BroadcastController::class, 'report'])
        ->name('broadcasts.report');
    // Broadcast Groups
    Route::resource('broadcast-groups', BroadcastGroupController::class);
    Route::get('broadcast-groups-template/download', [BroadcastGroupController::class, 'downloadTemplate'])
        ->name('broadcast-groups.template');
    Route::post('broadcast-groups/{id}/import', [BroadcastGroupController::class, 'importCustomers'])
        ->name('broadcast-groups.import');

    Route::resource('products', ProductController::class)->except(['show'])->names('products');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::resource('orders', OrderController::class)->names('orders');
    Route::get('orders/{product}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');
    // WhatsApp Chatbox (sidebar + AJAX chat pane)
    Route::get('whatsapp-chatbox', [WhatsAppChatboxController::class, 'index'])->name('whatsapp.chatbox');
    Route::get('whatsapp-chatbox/chat/{customer}', [WhatsAppChatboxController::class, 'chat'])->name('whatsapp.chatbox.chat');
    // Note: polling will reuse existing route('admin.customers.messages', $customer)

    Route::get('whatsapp-chatbox/media/{mediaId}', [WhatsAppChatboxController::class, 'downloadMedia'])
        ->name('whatsapp.chatbox.media');
});

// Profile
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    Route::get('/edit', [ProfilesController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfilesController::class, 'update'])->name('update');
});
