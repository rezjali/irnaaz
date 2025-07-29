<?php
header("Content-type: text/css; charset: UTF-8");
require_once __DIR__ . '/../config.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/models/Setting.php';
$settingModel = new \App\Models\Setting();
$settings = $settingModel->getAllAsAssoc();

$primaryColor = $settings['theme_primary_color'] ?? '#0d6efd';
$sidebarBg = $settings['theme_sidebar_bg'] ?? '#212529';
$sidebarText = $settings['theme_sidebar_text'] ?? '#adb5bd';
$bodyBg = $settings['theme_body_bg'] ?? '#f8f9fa';
$cardBg = $settings['theme_card_bg'] ?? '#ffffff';
$loginBgUrl = !empty($settings['login_bg_url']) ? APP_URL . '/' . $settings['login_bg_url'] : '';
?>

:root {
    --theme-primary: <?php echo $primaryColor; ?>;
    --theme-sidebar-bg: <?php echo $sidebarBg; ?>;
    --theme-sidebar-text: <?php echo $sidebarText; ?>;
    --theme-body-bg: <?php echo $bodyBg; ?>;
    --theme-card-bg: <?php echo $cardBg; ?>;
}

body { background-color: var(--theme-body-bg); }
.sidebar { background-color: var(--theme-sidebar-bg); }
.sidebar a, .sidebar button { color: var(--theme-sidebar-text); }
.sidebar a.active, .sidebar button.active, .sidebar a:hover, .sidebar button:hover { background-color: var(--theme-primary); color: #ffffff; }
.bg-white { background-color: var(--theme-card-bg) !important; }

.bg-blue-600 { background-color: var(--theme-primary) !important; }
.hover\:bg-blue-700:hover { background-color: var(--theme-primary) !important; filter: brightness(0.9); }
.text-blue-600 { color: var(--theme-primary) !important; }
.border-blue-500:focus { border-color: var(--theme-primary) !important; }
.ring-blue-500:focus { --tw-ring-color: var(--theme-primary) !important; }

<?php if ($loginBgUrl): ?>
body.login-page {
    background-image: url('<?php echo $loginBgUrl; ?>');
    background-size: cover;
    background-position: center;
}
<?php endif; ?>
