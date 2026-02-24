<?php
session_start();
// If already logged in as staff, redirect accordingly
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'Admin') { header('Location: 5_adminDashboard.php'); exit; }
    if ($role === 'Cashier') { header('Location: 3_index.php'); exit; }
}

// If not logged in at all, redirect to customer login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'KIOSK') {
    header('Location: customer_login.php');
    exit;
}

// Get user info from session
$userName = $_SESSION['full_name'] ?? 'Customer';
$userDept = $_SESSION['department'] ?? '';
$userId = $_SESSION['user_id'] ?? '';
$firstName = explode(' ', trim($userName))[0];
$initial = strtoupper(substr($firstName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrabHound - Dashboard</title>
    <link rel="stylesheet" href="css/store_selection.css">
</head>
<body>

    <div class="layout">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <div class="brand-icon">🐕</div>
                    <div>
                        <div class="brand-name">GrabHound</div>
                        <div class="brand-sub">Foundation University</div>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <a href="#" class="nav-item active">
                        <span class="nav-icon">🏠</span>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <div class="user-card">
                    <div class="user-avatar"><?php echo $initial; ?></div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($firstName); ?></div>
                        <?php if ($userDept): ?>
                            <div class="user-dept"><?php echo htmlspecialchars($userDept); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="../server/logout.php" class="logout-btn">
                    <span>⏻</span> Log Out
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Welcome, <?php echo htmlspecialchars($firstName); ?>!</h1>
                    <p class="page-subtitle">Select a store to start ordering</p>
                </div>
                <div class="header-meta">
                    <span class="current-date" id="current-date"></span>
                </div>
            </div>

            <!-- Store Section -->
            <div class="section-header">
                <h2 class="section-title">Stores</h2>
            </div>

            <div class="store-grid">

                <!-- Paws Place (Active) -->
                <div class="store-card active" onclick="window.location.href='2_kiosk_ordering.php'">
                    <div class="card-status available">Available</div>
                    <div class="card-icon">🐾</div>
                    <div class="card-name">Paws Place</div>
                    <div class="card-desc">Coffee & Snacks</div>
                    <div class="card-action">Order Now →</div>
                </div>

                <!-- Pup Stop -->
                <div class="store-card disabled" onclick="showComingSoon()">
                    <div class="card-status soon">Coming Soon</div>
                    <div class="card-icon">🍔</div>
                    <div class="card-name">Pup Stop</div>
                    <div class="card-desc">Meals & Combos</div>
                </div>

                <!-- Kennel Main -->
                <div class="store-card disabled" onclick="showComingSoon()">
                    <div class="card-status soon">Coming Soon</div>
                    <div class="card-icon">🥤</div>
                    <div class="card-name">Kennel Main</div>
                    <div class="card-desc">Drinks & Smoothies</div>
                </div>

                <!-- Kennel North -->
                <div class="store-card disabled" onclick="showComingSoon()">
                    <div class="card-status soon">Coming Soon</div>
                    <div class="card-icon">🍕</div>
                    <div class="card-name">Kennel North</div>
                    <div class="card-desc">Quick Bites</div>
                </div>

            </div>
        </main>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
        // Set current date
        const dateEl = document.getElementById('current-date');
        if (dateEl) {
            const now = new Date();
            dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        function showComingSoon() {
            const toast = document.getElementById('toast');
            toast.textContent = '🚧 This store is coming soon!';
            toast.classList.add('show');
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }
    </script>
</body>
</html>
