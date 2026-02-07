<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($basePath)) {
    $basePath = '../';
}
if (!isset($activePage)) {
    $activePage = basename($_SERVER['PHP_SELF'], '.php');
}
$pageTitle = isset($pageTitle) ? $pageTitle : 'FlexZone';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        const API_BASE = '<?php echo $basePath; ?>php/';
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/style.css?v=1.1">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Page Specific Assets -->
    <?php if ($activePage === 'home'): ?>
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <?php elseif ($activePage === 'join'): ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/join.css">
    <?php elseif ($activePage === 'onboarding'): ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/onboarding.css">
    <?php elseif ($activePage === 'index'): ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/landing.css">
    <?php endif; ?>
    <!-- Conditional Scripts -->
    <?php if ($activePage === 'dashboard' || $activePage === 'progress'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    <?php if (isset($extraCss)) echo '<link rel="stylesheet" href="' . $extraCss . '">'; ?>
    <?php if (isset($extraScript)) echo '<script src="' . $extraScript . '" defer></script>'; ?>
</head>
<body class="<?php echo isset($bodyClass) ? $bodyClass : ''; ?>">