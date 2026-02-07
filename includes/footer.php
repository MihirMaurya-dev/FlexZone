<?php
if (!isset($basePath)) {
    $basePath = '../';
}
?>
    <script src="<?php echo $basePath; ?>js/theme.js?v=1.3"></script>
    <?php if (isset($activePage) && file_exists(__DIR__ . "/../js/{$activePage}.js")): ?>
    <script src="<?php echo $basePath; ?>js/<?php echo $activePage; ?>.js?v=1.5"></script>
    <?php endif; ?>
    <?php if ($activePage === 'home'): ?>
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('tour_completed')) {
                introJs().setOptions({
                    showProgress: true,
                    exitOnOverlayClick: false
                }).onexit(function() {
                    localStorage.setItem('tour_completed', 'true');
                }).start();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>