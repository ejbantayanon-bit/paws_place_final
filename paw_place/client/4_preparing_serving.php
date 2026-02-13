<?php
session_start();
// Allow access for staff roles or direct access
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place - Kitchen Display</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/preparing_serving.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="brand-icon">🐾</span>
            <h1 class="brand-title">KITCHEN DISPLAY</h1>
        </div>
        <div class="top-bar-right">
            <span class="clock" id="live-clock"></span>
            <a href="3_index.php" class="back-link">← Staff POS</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Split Screen Container -->
        <div class="split-container">
            <!-- Preparing Side -->
            <div class="section preparing-section">
                <div class="section-header preparing-header">
                    <h2 class="section-title">🔥 PREPARING</h2>
                    <div class="order-count" id="preparing-count">0</div>
                </div>
                <div class="orders-grid" id="preparing-orders">
                    <!-- Orders will be dynamically loaded here -->
                </div>
            </div>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Now Serving Side -->
            <div class="section serving-section">
                <div class="section-header serving-header">
                    <h2 class="section-title">✅ NOW SERVING</h2>
                    <div class="order-count" id="serving-count">0</div>
                </div>
                <div class="orders-grid" id="serving-orders">
                    <!-- Orders will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/preparing_serving.js"></script>
</body>
</html>
