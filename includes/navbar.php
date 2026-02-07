<!-- Bottom Navigation -->
    <nav class="navbar">
        <a href="home.php" class="<?php echo ($activePage === 'home') ? 'active' : ''; ?>"><i class='bx bx-home-alt-2'></i></a>
        <a href="dashboard.php" class="<?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>"><i class='bx bx-bar-chart-alt-2'></i></a>
        <a href="leaderboard.php" class="<?php echo ($activePage === 'leaderboard') ? 'active' : ''; ?>"><i class='bx bx-trophy'></i></a>
        <a href="progress.php" class="<?php echo ($activePage === 'progress') ? 'active' : ''; ?>"><i class='bx bx-body'></i></a>
        <a href="profile.php" class="<?php echo ($activePage === 'profile') ? 'active' : ''; ?>"><i class='bx bx-user'></i></a>
    </nav>