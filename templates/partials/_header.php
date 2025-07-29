<?php 
use App\Core\Auth;
$announcementModel = new \App\Models\Announcement();
$unread_count = $announcementModel->getUnreadCountForUser($_SESSION['user_id']);
?>
<header class="h-16 bg-white shadow-md flex justify-between items-center px-6">
    <h1 class="text-2xl font-semibold text-gray-800"><?php echo isset($title) ? e($title) : 'داشبورد'; ?></h1>
    <div class="flex items-center space-x-4 space-x-reverse">
        <!-- Notifications Dropdown -->
        <div class="relative">
            <a href="<?php echo APP_URL; ?>/announcements/user" class="relative">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <?php if ($unread_count > 0): ?>
                    <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                <span class="text-gray-700 hidden sm:inline"><?php echo e(Auth::user()->full_name); ?></span>
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                <a href="<?php echo APP_URL; ?>/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">پروفایل</a>
                <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">خروج</a>
            </div>
        </div>
    </div>
</header>
