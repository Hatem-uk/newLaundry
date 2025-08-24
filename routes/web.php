<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\AgentAuthController;
use App\Http\Controllers\Web\ApprovalController;
use App\Http\Controllers\Web\AdminProfileController;
use App\Http\Controllers\Web\AdminController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Redirect root to default locale
Route::get('/', function () {
    return redirect(LaravelLocalization::getLocalizedURL(LaravelLocalization::getDefaultLocale()));
});

// Localized routes using LaravelLocalization
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['set.locale']
], function() {
    
    // Public routes
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Admin Auth Routes (no authentication required)
    Route::prefix('admin')->group(function() {
        Route::middleware('guest:web')->group(function() {
            Route::get('register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
            Route::post('register', [AdminAuthController::class, 'register']);
            Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
            Route::post('login', [AdminAuthController::class, 'login']);
        });

        // Admin Protected Routes (requires authentication and admin role)
        Route::middleware(['auth:web', 'admin'])->group(function() {
            // Dashboard and main admin routes
            Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
            Route::get('users', [AdminController::class, 'users'])->name('admin.users');
            Route::get('users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
            Route::post('users', [AdminController::class, 'storeUser'])->name('admin.users.store');
            Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
            Route::get('agents', [AdminController::class, 'agents'])->name('admin.agents');
            Route::get('laundries', [AdminController::class, 'laundries'])->name('admin.laundries');
            
            // Laundry management routes
            Route::get('laundries/create', [AdminController::class, 'createLaundry'])->name('admin.laundries.create');
Route::post('laundries', [AdminController::class, 'storeLaundry'])->name('admin.laundries.store');

// Language switcher route
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');
            Route::get('laundries/{laundry}/view', [AdminController::class, 'viewLaundry'])->name('admin.laundries.view');
            Route::get('laundries/{laundry}/edit', [AdminController::class, 'editLaundry'])->name('admin.laundries.edit');
            Route::put('laundries/{laundry}', [AdminController::class, 'updateLaundry'])->name('admin.laundries.update');
            Route::post('laundries/{laundry}/revenue', [AdminController::class, 'getRevenueForPeriod'])->name('admin.laundries.revenue');
            
            // Laundry status management routes
            Route::get('laundries/pending', [AdminController::class, 'pendingLaundries'])->name('admin.laundries.pending');
            Route::get('laundries/rejected', [AdminController::class, 'rejectedLaundries'])->name('admin.laundries.rejected');

            Route::post('laundries/{laundry}/approve', [AdminController::class, 'approveLaundry'])->name('admin.laundries.approve');
            Route::post('laundries/{laundry}/reject', [AdminController::class, 'rejectLaundry'])->name('admin.laundries.reject');

            Route::post('laundries/{laundry}/block', [AdminController::class, 'deactivateLaundry'])->name('admin.laundries.block');
            Route::post('laundries/{laundry}/notification', [AdminController::class, 'sendLaundryNotification'])->name('admin.laundries.notification');
            
            // Laundry AJAX routes
            Route::post('laundries/{laundry}/status', [AdminController::class, 'updateLaundryStatus'])->name('admin.laundries.status');
            Route::get('laundries/{laundry}/details', [AdminController::class, 'getLaundryDetails'])->name('admin.laundries.details');
            Route::get('laundries/{laundry}/orders', [AdminController::class, 'getLaundryOrders'])->name('admin.laundries.orders');
            Route::delete('laundries/{laundry}', [AdminController::class, 'destroyLaundry'])->name('admin.laundries.destroy');
            Route::get('services', [AdminController::class, 'services'])->name('admin.services');
Route::get('services/{service}/view', [AdminController::class, 'viewService'])->name('admin.services.view');
Route::get('services/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
Route::put('services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
Route::delete('services/{service}', [AdminController::class, 'deleteService'])->name('admin.services.delete');

Route::get('orders', [AdminController::class, 'orders'])->name('admin.orders');
Route::get('orders/{order}/view', [AdminController::class, 'viewOrder'])->name('admin.orders.view');
Route::get('orders/{order}/edit', [AdminController::class, 'editOrder'])->name('admin.orders.edit');
Route::put('orders/{order}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
Route::delete('orders/{order}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');

Route::get('tracking', [AdminController::class, 'tracking'])->name('admin.tracking');
            
            // Admin profile routes
            Route::get('profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');
            Route::get('profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::put('profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
            Route::delete('profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
            
            // AJAX routes for admin functionality
            Route::post('users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
            Route::post('orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
            Route::get('orders/{order}/details', [AdminController::class, 'getOrderDetails'])->name('admin.orders.details');
            Route::get('users/{user}/details', [AdminController::class, 'getUserDetails'])->name('admin.users.details');
            
            // Agent AJAX routes
            Route::post('agents/{agent}/status', [AdminController::class, 'updateAgentStatus'])->name('admin.agents.status');
            Route::get('agents/{agent}/details', [AdminController::class, 'getAgentDetails'])->name('admin.agents.details');
            Route::get('agents/{agent}/orders', [AdminController::class, 'getAgentOrders'])->name('admin.agents.orders');
            
            // User management routes
            Route::get('users/{user}/view', [AdminController::class, 'viewUser'])->name('admin.users.view');
            Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
            Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
            Route::post('users/{user}/block', [AdminController::class, 'blockUser'])->name('admin.users.block');
            Route::post('users/{user}/activate', [AdminController::class, 'activateUser'])->name('admin.users.activate');
            
            // Agent approval routes
            Route::post('agents/{user}/approve', [AdminController::class, 'approveAgent'])->name('admin.agents.approve');
            Route::post('agents/{user}/reject', [AdminController::class, 'rejectAgent'])->name('admin.agents.reject');
            Route::post('agents/{user}/suspend', [AdminController::class, 'suspendAgent'])->name('admin.agents.suspend');
            Route::post('agents/{user}/reactivate', [AdminController::class, 'reactivateAgent'])->name('admin.agents.reactivate');
            
            // Agent management routes
            Route::get('agents/{agent}/view', [AdminController::class, 'viewAgent'])->name('admin.agents.view');
            Route::get('agents/{agent}/edit', [AdminController::class, 'editAgent'])->name('admin.agents.edit');
            Route::put('agents/{agent}', [AdminController::class, 'updateAgent'])->name('admin.agents.update');
            Route::delete('agents/{agent}', [AdminController::class, 'destroyAgent'])->name('admin.agents.destroy');
            Route::post('agents/{agent}/block', [AdminController::class, 'blockAgent'])->name('admin.agents.block');
            Route::post('agents/{agent}/activate', [AdminController::class, 'activateAgent'])->name('admin.agents.activate');
            Route::post('agents/{agent}/notification', [AdminController::class, 'sendAgentNotification'])->name('admin.agents.notification');
            
            // Agent status management routes
            Route::get('agents/pending', [AdminController::class, 'pendingAgents'])->name('admin.agents.pending');
            Route::get('agents/rejected', [AdminController::class, 'rejectedAgents'])->name('admin.agents.rejected');
            Route::get('agents/suspended', [AdminController::class, 'suspendedAgents'])->name('admin.agents.suspended');
            
            // Logout route
            Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
            
            // Approval Routes
            Route::prefix('approvals')->group(function() {
                Route::get('/', [ApprovalController::class, 'pendingApprovals'])->name('admin.approvals.pending');
                Route::get('{user}', [ApprovalController::class, 'showApproval'])->name('admin.approvals.show');
                Route::post('{user}/approve', [ApprovalController::class, 'approveUser'])->name('admin.approvals.approve');
                Route::post('{user}/reject', [ApprovalController::class, 'rejectUser'])->name('admin.approvals.reject');
            });
        });
    });

    // Agent Auth Routes (no authentication required)
    Route::prefix('agent')->group(function() {
        Route::middleware('guest:web')->group(function() {
            Route::get('register', [AgentAuthController::class, 'showRegisterForm'])->name('agent.register');
            Route::post('register', [AgentAuthController::class, 'register']);
            Route::get('login', [AgentAuthController::class, 'showLoginForm'])->name('agent.login');
            Route::post('login', [AgentAuthController::class, 'login']);
        });

        // Agent Protected Routes (requires authentication and agent role)
        Route::middleware(['auth:web', 'agent'])->group(function() {
            Route::get('dashboard', function() {
                return view('agent.dashboard');
            })->name('agent.dashboard');
            
            Route::post('logout', [AgentAuthController::class, 'logout'])->name('agent.logout');
            
            // Add agent-specific routes here
            // Route::get('profile', [AgentController::class, 'profile'])->name('agent.profile');
            // Route::get('orders', [AgentController::class, 'orders'])->name('agent.orders');
        });
    });
});

// Catch-all route for unsupported locales
Route::get('{locale}', function($locale) {
    if (!in_array($locale, LaravelLocalization::getSupportedLanguagesKeys())) {
        abort(404);
    }
    // Redirect to supported locale
    return redirect(LaravelLocalization::getLocalizedURL($locale));
})->where('locale', '[a-z]{2}');