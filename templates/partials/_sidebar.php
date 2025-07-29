<?php
// Load models to fetch dynamic menu items.
$orderModel = new \App\Models\Order();
$active_statuses = $orderModel->getStatusesByCategory('active');
$cancelled_statuses = $orderModel->getStatusesByCategory('cancelled');
$deleted_statuses = $orderModel->getStatusesByCategory('deleted');

// --- Helper Functions for Sidebar ---
function getCurrentUriPath() {
    $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $basePath = trim(parse_url(APP_URL, PHP_URL_PATH), '/');
    if ($basePath) {
        $currentUri = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $currentUri);
    }
    return trim($currentUri, '/');
}

$currentPath = getCurrentUriPath();

function isMenuOpen($paths, $currentPath) {
    foreach ((array)$paths as $path) {
        if ($path !== '' && strpos($currentPath, $path) === 0) return 'true';
    }
    if (empty($paths) && $currentPath === '') return 'true'; // For dashboard
    return 'false';
}

function isActiveLink($path, $currentPath) {
    // Exact match for main links
    if (strpos($path, '?') === false) {
        return $path === $currentPath;
    }
    // Match for links with query strings (like statuses)
    return strpos($_SERVER['REQUEST_URI'], $path) !== false;
}

// --- CSS Classes ---
$activeParentClass = 'bg-gray-900 text-white';
$inactiveParentClass = 'hover:bg-gray-700 hover:text-white';
$activeChildClass = 'text-blue-400 font-bold';
$inactiveChildClass = 'text-gray-400 hover:text-white';
?>
<aside class="w-64 flex-shrink-0 bg-gray-800 text-gray-300 flex flex-col shadow-lg">
    <div class="h-16 flex items-center justify-center text-xl font-bold border-b border-gray-700 text-white">
        <a href="<?php echo APP_URL; ?>">پنل آی راز</a>
    </div>
    <nav class="flex-1 overflow-y-auto mt-4 space-y-1 px-2 text-sm" x-data="{}">
        
        <a href="<?php echo APP_URL; ?>/" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isActiveLink('', $currentPath) ? $activeParentClass : $inactiveParentClass; ?>">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>داشبورد</span>
        </a>

        <!-- Order Management -->
        <div x-data="{ isOpen: <?php echo isMenuOpen(['orders'], $currentPath); ?> }">
            <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen(['orders'], $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg><span>مدیریت سفارشات</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/orders" class="block py-2 px-3 rounded-md <?php echo isActiveLink('orders', $currentPath) && !isset($_GET['status_id']) ? $activeChildClass : $inactiveChildClass; ?>">همه سفارشات</a>
                <?php foreach ($active_statuses as $status): ?><a href="<?php echo APP_URL; ?>/orders?status_id=<?php echo $status->id; ?>" class="block py-2 px-3 rounded-md <?php echo isActiveLink('orders?status_id='.$status->id, $_SERVER['REQUEST_URI']) ? $activeChildClass : $inactiveChildClass; ?>"><?php echo e($status->status_name); ?></a><?php endforeach; ?>
            </div>
        </div>

        <a href="<?php echo APP_URL; ?>/tasks" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isActiveLink('tasks', $currentPath) ? $activeParentClass : $inactiveParentClass; ?>">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>تفکیک کار</span>
        </a>

        <!-- Deleted Orders -->
        <div x-data="{ isOpen: <?php echo isMenuOpen(['deleted-orders'], $currentPath); ?> }">
            <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen(['deleted-orders'], $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg><span>سفارشات حذف شده</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/deleted-orders" class="block py-2 px-3 rounded-md <?php echo isActiveLink('deleted-orders', $currentPath) && !isset($_GET['status_id']) ? $activeChildClass : $inactiveChildClass; ?>">همه حذف شده ها</a>
                <?php foreach ($deleted_statuses as $status): ?><a href="<?php echo APP_URL; ?>/deleted-orders?status_id=<?php echo $status->id; ?>" class="block py-2 px-3 rounded-md <?php echo isActiveLink('deleted-orders?status_id='.$status->id, $_SERVER['REQUEST_URI']) ? $activeChildClass : $inactiveChildClass; ?>"><?php echo e($status->status_name); ?></a><?php endforeach; ?>
            </div>
        </div>

        <a href="<?php echo APP_URL; ?>/suspended-orders" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isActiveLink('suspended-orders', $currentPath) ? $activeParentClass : $inactiveParentClass; ?>">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>سفارشات معلق شده</span>
        </a>

        <!-- Cancelled Orders -->
        <div x-data="{ isOpen: <?php echo isMenuOpen(['cancelled-orders'], $currentPath); ?> }">
             <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen(['cancelled-orders'], $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg><span>سفارشات کنسل شده</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
             </button>
             <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/cancelled-orders" class="block py-2 px-3 rounded-md <?php echo isActiveLink('cancelled-orders', $currentPath) && !isset($_GET['status_id']) ? $activeChildClass : $inactiveChildClass; ?>">همه کنسل شده ها</a>
                <?php foreach ($cancelled_statuses as $status): ?><a href="<?php echo APP_URL; ?>/cancelled-orders?status_id=<?php echo $status->id; ?>" class="block py-2 px-3 rounded-md <?php echo isActiveLink('cancelled-orders?status_id='.$status->id, $_SERVER['REQUEST_URI']) ? $activeChildClass : $inactiveChildClass; ?>"><?php echo e($status->status_name); ?></a><?php endforeach; ?>
            </div>
        </div>

        <a href="<?php echo APP_URL; ?>/suitcases" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isActiveLink('suitcases', $currentPath) ? $activeParentClass : $inactiveParentClass; ?>">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m0 10l8 4m-8-4v-4m16 4v-4"></path></svg>
            <span>مدیریت چمدان</span>
        </a>

        <!-- Financial Management -->
        <div x-data="{ isOpen: <?php echo isMenuOpen('financial', $currentPath); ?> }">
            <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen('financial', $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span>مدیریت مالی</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/financial/wallets/product" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/wallets/product', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">کیف پول کالا</a>
                <a href="<?php echo APP_URL; ?>/financial/wallets/shipping" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/wallets/shipping', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">کیف پول باربری</a>
                <a href="<?php echo APP_URL; ?>/financial/transactions/product" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/transactions/product', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">تراکنش کالا</a>
                <a href="<?php echo APP_URL; ?>/financial/transactions/shipping" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/transactions/shipping', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">تراکنش باربری</a>
                <a href="<?php echo APP_URL; ?>/financial/receipts/product" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/receipts/product', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">فیش واریزی کالا</a>
                <a href="<?php echo APP_URL; ?>/financial/receipts/shipping" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/receipts/shipping', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">فیش واریزی باربری</a>
                <a href="<?php echo APP_URL; ?>/financial/assign-credit" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/assign-credit', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">اختصاص اعتبار</a>
                <a href="<?php echo APP_URL; ?>/financial/sales-stats" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/sales-stats', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">آمار فروش</a>
                <a href="<?php echo APP_URL; ?>/financial/debtors" class="block py-2 px-3 rounded-md <?php echo isActiveLink('financial/debtors', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">لیست بدهکاران</a>
            </div>
        </div>

        <!-- User Management -->
        <div x-data="{ isOpen: <?php echo isMenuOpen(['users', 'roles'], $currentPath); ?> }">
            <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen(['users', 'roles'], $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a6 6 0 00-9-5.197M12 12a4 4 0 110-8 4 4 0 010 8z"></path></svg><span>مدیریت کاربران</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/users" class="block py-2 px-3 rounded-md <?php echo isActiveLink('users', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">مدیریت کاربران</a>
                <a href="<?php echo APP_URL; ?>/roles" class="block py-2 px-3 rounded-md <?php echo isActiveLink('roles', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">گروه کاربری</a>
            </div>
        </div>

        <!-- Initial Settings -->
        <div x-data="{ isOpen: <?php echo isMenuOpen(['admins', 'constants', 'settings'], $currentPath); ?> }">
            <button @click="isOpen = !isOpen" class="w-full flex justify-between items-center py-2.5 px-4 rounded-lg transition duration-200 <?php echo isMenuOpen(['admins', 'constants', 'settings'], $currentPath) === 'true' ? 'text-white' : $inactiveParentClass; ?>">
                <span class="flex items-center"><svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>تنظیمات اولیه</span></span>
                <svg :class="{'rotate-180': isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="isOpen" x-transition class="mt-1 space-y-1 pr-4 border-r-2 border-gray-600">
                <a href="<?php echo APP_URL; ?>/admins" class="block py-2 px-3 rounded-md <?php echo isActiveLink('admins', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">مدیریت مدیران میانی</a>
                <a href="<?php echo APP_URL; ?>/settings" class="block py-2 px-3 rounded-md <?php echo isActiveLink('settings', $currentPath) && !strpos($currentPath, 'sms') && !strpos($currentPath, 'orders') && !strpos($currentPath, 'tickets') ? $activeChildClass : $inactiveChildClass; ?>">تنظیمات سایت و برندینگ</a>
                <a href="<?php echo APP_URL; ?>/settings/sms" class="block py-2 px-3 rounded-md <?php echo isActiveLink('settings/sms', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">تنظیمات پیامک</a>
                <a href="<?php echo APP_URL; ?>/constants/order-statuses" class="block py-2 px-3 rounded-md <?php echo isActiveLink('constants/order-statuses', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">وضعیت سفارش</a>
                <a href="<?php echo APP_URL; ?>/constants/ticket-categories" class="block py-2 px-3 rounded-md <?php echo isActiveLink('constants/ticket-categories', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">دسته بندی تیکت</a>
                <a href="<?php echo APP_URL; ?>/constants/shipping-rates" class="block py-2 px-3 rounded-md <?php echo isActiveLink('constants/shipping-rates', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">نرخ باربری</a>
                <a href="<?php echo APP_URL; ?>/constants/sites" class="block py-2 px-3 rounded-md <?php echo isActiveLink('constants/sites', $currentPath) ? $activeChildClass : $inactiveChildClass; ?>">مدیریت سایت ها</a>
            </div>
        </div>
        
    </nav>
</aside>
