<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foundation University - Select Store</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/store_selection.css') ?>">
    <script>
        const BASE_URL = '<?= base_url() ?>';
    </script>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <div class="top-bar-title">Foundation University</div>
            <div class="top-bar-subtitle">GrubHound Kiosk</div>
        </div>
        <a href="<?= base_url() ?>" class="back-btn">&#8592; Back</a>
    </div>

    <!-- Main Content -->
    <div class="store-container">
        <div class="heading-section">
            <h1>Choose a Store</h1>
            <p>Select where you'd like to order</p>
        </div>

        <div class="store-grid">
            <!-- Pup Stop (Coming Soon) -->
            <div class="store-card disabled" data-store="pupstop" onclick="showComingSoon()">
                <div class="store-name">Pup Stop</div>
                <div class="coming-soon-badge">Coming Soon</div>
            </div>

            <!-- Paws Place (Active) -->
            <div class="store-card active" data-store="pawsplace" onclick="window.location.href='<?= base_url('kiosk/login') ?>'">
                <div class="store-name">Paws Place</div>
                <div class="active-badge">Order Now</div>
            </div>

            <!-- Kennel (Coming Soon) -->
            <div class="store-card disabled" data-store="kennel" onclick="showComingSoon()">
                <div class="store-name">Kennel</div>
                <div class="coming-soon-badge">Coming Soon</div>
            </div>

            <!-- Queue Bar (Coming Soon) -->
            <div class="store-card disabled" data-store="queuebar" onclick="showComingSoon()">
                <div class="store-name">Queue Bar</div>
                <div class="coming-soon-badge">Coming Soon</div>
            </div>
        </div>
    </div>

    <p class="footer-text">Foundation University &bull; Self-Service Ordering</p>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
        function showComingSoon() {
            const toast = document.getElementById('toast');
            toast.textContent = 'This store is coming soon!';
            toast.classList.add('show');
            if (window._toastTimer) clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }
    </script>
</body>
</html>
