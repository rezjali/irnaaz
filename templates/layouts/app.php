<?php
// Fetch settings for layout elements like favicon and site name
$settingModel = new \App\Models\Setting();
$settings = $settingModel->getAllAsAssoc();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($settings['site_name'] ?? 'پنل مدیریت'); ?> - <?php echo isset($title) ? e($title) : 'داشبورد'; ?></title>
    
    <?php if (!empty($settings['site_favicon_url'])): ?>
        <link rel="icon" type="image/png" href="<?php echo APP_URL . '/' . e($settings['site_favicon_url']); ?>">
    <?php endif; ?>
    
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for UI interactions from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom & Dynamic Stylesheets -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/dynamic_style.php">
</head>
<body class="flex h-screen bg-gray-100">

    <!-- Sidebar Partial -->
    <?php require_once TEMPLATES_PATH . '/partials/_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Header Partial -->
        <?php require_once TEMPLATES_PATH . '/partials/_header.php'; ?>

        <!-- Dynamic Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
            <div class="container mx-auto">
                <!-- محتوای اصلی که از فایل‌های phtml خوانده می‌شود در اینجا تزریق می‌شود -->
                <?php echo $content; ?>
            </div>
        </main>

        <!-- Footer Partial -->
        <?php require_once TEMPLATES_PATH . '/partials/_footer.php'; ?>
    </div>

    <!-- Custom JavaScript File -->
    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
</body>
</html>
