<?php
session_start();
// Optional: Add authentication check if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preparing & Serving - Paws Place</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/preparing_serving.css">
</head>
<body>
    <!-- Background Image -->
    <div class="background-container">
        <img src="../image/Paws place.jpeg" alt="Cafe Background" class="background-image">
        <div class="background-overlay"></div>
    </div>

    <!-- Main Content Box -->
    <div class="content-box">
        <div class="content-inner">
            <!-- Header -->
            <div class="header">
                <div class="header-logo">🐾</div>
                <h1 class="header-title">PAWS PLACE KITCHEN DISPLAY</h1>
            </div>

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
    </div>

    <script src="js/preparing_serving.js"></script>
</body>
</html>