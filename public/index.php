<?php

/**
 * ---------------------------------------------------------------
 * FRONT CONTROLLER
 * ---------------------------------------------------------------
 * تمام درخواست‌های وب به این فایل هدایت می‌شوند.
 */

// Start session
session_start();

// Load main configuration file
require_once __DIR__ . '/../config.php';

// --- Corrected Autoloader for Case-Sensitive Servers ---
spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') !== 0) return;
    $classPath = str_replace('App\\', '', $className);
    $parts = explode('\\', $classPath);
    $classFileName = array_pop($parts);
    $directoryPath = strtolower(implode('/', $parts));
    $file = APP_PATH . (empty($directoryPath) ? '' : '/' . $directoryPath) . '/' . $classFileName . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helper functions
require_once APP_PATH . '/helpers/functions.php';
require_once APP_PATH . '/helpers/jdf.php';

// Initialize the Router
$router = new App\Core\Router();

// --- Define All Application Routes ---

// Authentication
$router->get('/login', [App\Controllers\AuthController::class, 'showLoginForm']);
$router->post('/login', [App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

// Dashboard
$router->get('/', [App\Controllers\DashboardController::class, 'index']);

// User Profile
$router->get('/profile', [App\Controllers\UserController::class, 'profile']);
$router->post('/profile/update', [App\Controllers\UserController::class, 'updateProfile']);

// Announcement Routes
$router->get('/announcements', [App\Controllers\AnnouncementController::class, 'index']);
$router->post('/announcements/store', [App\Controllers\AnnouncementController::class, 'store']);
$router->get('/announcements/user', [App\Controllers\AnnouncementController::class, 'userIndex']);
$router->post('/announcements/mark-all-read', [App\Controllers\AnnouncementController::class, 'markAllAsRead']);

// Task Assignment Routes
$router->get('/tasks', [App\Controllers\TaskController::class, 'index']);
$router->post('/tasks/assign', [App\Controllers\TaskController::class, 'assign']);

// Order Routes
$router->get('/orders', [App\Controllers\OrderController::class, 'index']);
$router->get('/cancelled-orders', [App\Controllers\OrderController::class, 'cancelledIndex']);
$router->get('/deleted-orders', [App\Controllers\OrderController::class, 'deletedIndex']);
$router->get('/suspended-orders', [App\Controllers\OrderController::class, 'suspendedIndex']);
$router->get('/orders/create', [App\Controllers\OrderController::class, 'create']);
$router->post('/orders/store', [App\Controllers\OrderController::class, 'store']);

// Suitcase Routes
$router->get('/suitcases', [App\Controllers\SuitcaseController::class, 'index']);
$router->get('/suitcases/create', [App\Controllers\SuitcaseController::class, 'create']);
$router->post('/suitcases/store', [App\Controllers\SuitcaseController::class, 'store']);
$router->get('/suitcases/show', [App\Controllers\SuitcaseController::class, 'show']);
$router->post('/suitcases/assign-order', [App\Controllers\SuitcaseController::class, 'assignOrder']);
$router->post('/suitcases/remove-order', [App\Controllers\SuitcaseController::class, 'removeOrder']);

// Financial Routes
$router->get('/financial/wallets/product', [App\Controllers\FinancialController::class, 'productWallets']);
$router->get('/financial/wallets/shipping', [App\Controllers\FinancialController::class, 'shippingWallets']);
$router->get('/financial/transactions/product', [App\Controllers\FinancialController::class, 'productTransactions']);
$router->get('/financial/transactions/shipping', [App\Controllers\FinancialController::class, 'shippingTransactions']);
$router->get('/financial/receipts/product', [App\Controllers\ReceiptController::class, 'productReceipts']);
$router->get('/financial/receipts/shipping', [App\Controllers\ReceiptController::class, 'shippingReceipts']);
$router->post('/financial/receipts/process', [App\Controllers\ReceiptController::class, 'process']);
$router->get('/financial/assign-credit', [App\Controllers\FinancialController::class, 'assignCredit']);
$router->post('/financial/store-credit', [App\Controllers\FinancialController::class, 'storeCredit']);
$router->get('/financial/debtors', [App\Controllers\FinancialController::class, 'debtorsList']);
$router->get('/financial/sales-stats', [App\Controllers\FinancialController::class, 'salesStats']);

// Ticket Routes
$router->get('/tickets', [App\Controllers\TicketController::class, 'index']);
$router->get('/tickets/show', [App\Controllers\TicketController::class, 'show']);
$router->post('/tickets/reply', [App\Controllers\TicketController::class, 'storeReply']);

// User & Role Routes
$router->get('/users', [App\Controllers\UserController::class, 'index']);
$router->get('/users/create', [App\Controllers\UserController::class, 'create']);
$router->post('/users/store', [App\Controllers\UserController::class, 'store']);
$router->get('/users/edit', [App\Controllers\UserController::class, 'edit']);
$router->post('/users/update', [App\Controllers\UserController::class, 'updateUser']);
$router->get('/roles', [App\Controllers\RoleController::class, 'index']);
$router->get('/roles/edit', [App\Controllers\RoleController::class, 'edit']);
$router->post('/roles/update', [App\Controllers\RoleController::class, 'update']);
$router->post('/roles/store', [App\Controllers\RoleController::class, 'store']);
$router->post('/roles/delete', [App\Controllers\RoleController::class, 'delete']);

// Content Management Routes
$router->get('/pages', [App\Controllers\PageController::class, 'index']);
$router->get('/pages/edit', [App\Controllers\PageController::class, 'edit']);
$router->post('/pages/update', [App\Controllers\PageController::class, 'update']);
$router->get('/blog', [App\Controllers\BlogController::class, 'index']);
$router->get('/blog/create', [App\Controllers\BlogController::class, 'create']);
$router->post('/blog/store', [App\Controllers\BlogController::class, 'store']);
$router->get('/blog/categories', [App\Controllers\BlogController::class, 'categories']);
$router->post('/blog/categories/store', [App\Controllers\BlogController::class, 'storeCategory']);
$router->get('/images', [App\Controllers\ImageController::class, 'index']);
$router->post('/images/upload', [App\Controllers\ImageController::class, 'upload']);
$router->post('/images/delete', [App\Controllers\ImageController::class, 'delete']);

// Menu Management Routes
$router->get('/menu-groups', [App\Controllers\MenuController::class, 'groups']);
$router->get('/menus', [App\Controllers\MenuController::class, 'items']);
$router->post('/menus/store', [App\Controllers\MenuController::class, 'storeItem']);

// Log & Stats Routes
$router->get('/logs/visitors', [App\Controllers\LogController::class, 'visitorLogs']);
$router->get('/logs/activity', [App\Controllers\LogController::class, 'activityLogs']);

// Settings & Constants Routes
$router->get('/admins', [App\Controllers\AdminUserController::class, 'index']);
$router->get('/admins/create', [App\Controllers\AdminUserController::class, 'create']);
$router->post('/admins/store', [App\Controllers\AdminUserController::class, 'store']);
$router->get('/settings', [App\Controllers\SettingsController::class, 'index']);
$router->get('/settings/orders', [App\Controllers\SettingsController::class, 'orderSettings']);
$router->get('/settings/sms', [App\Controllers\SettingsController::class, 'smsSettings']);
$router->get('/settings/tickets', [App\Controllers\SettingsController::class, 'ticketSettings']);
$router->post('/settings/update', [App\Controllers\SettingsController::class, 'update']);
$router->get('/constants/order-statuses', [App\Controllers\ConstantController::class, 'orderStatuses']);
$router->post('/constants/order-statuses/store', [App\Controllers\ConstantController::class, 'storeOrderStatus']);
$router->post('/constants/order-statuses/delete', [App\Controllers\ConstantController::class, 'deleteOrderStatus']);
$router->get('/constants/shipping-rates', [App\Controllers\ConstantController::class, 'shippingRates']);
$router->post('/constants/shipping-rates/store', [App\Controllers\ConstantController::class, 'storeShippingRate']);
$router->post('/constants/shipping-rates/delete', [App\Controllers\ConstantController::class, 'deleteShippingRate']);
$router->get('/constants/sites', [App\Controllers\ConstantController::class, 'sites']);
$router->post('/constants/sites/store', [App\Controllers\ConstantController::class, 'storeSite']);
$router->post('/constants/sites/delete', [App\Controllers\ConstantController::class, 'deleteSite']);
$router->get('/constants/ticket-categories', [App\Controllers\ConstantController::class, 'ticketCategories']);
$router->post('/constants/ticket-categories/store', [App\Controllers\ConstantController::class, 'storeTicketCategory']);
$router->post('/constants/ticket-categories/delete', [App\Controllers\ConstantController::class, 'deleteTicketCategory']);

// Dispatch the router
try {
    // Log the visit for every page load
    (new \App\Models\Log())->logVisit();
    
    $router->dispatch();
} catch (Exception $e) {
    // This will be caught by our custom ErrorHandler
    throw $e;
}
