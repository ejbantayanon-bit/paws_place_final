<?php
session_start();
// Optional: Add authentication check if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preparing & Serving - GrabHound</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/preparing_serving.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Top Header Bar -->
    <header class="bg-white p-4 flex justify-between items-center shadow-sm border-b border-gray-200 z-10 relative">
        <div class="flex items-center gap-4">
            <!-- Icon/Logo Area Removed -->
            <div>
                <h1 class="text-3xl font-black text-[#800000] tracking-tight">GRABHOUND</h1>
                <p class="text-gray-500 text-sm font-semibold tracking-wide uppercase">Kitchen Display System</p>
            </div>
        </div>
        
        <div class="header-right flex items-center gap-6">
            <div id="live-clock" class="text-2xl font-bold text-gray-700 font-mono tracking-widest bg-gray-100 px-4 py-2 rounded-lg">00:00:00 PM</div>
        </div>
    </header>

    <!-- Main Split Layout -->
    <main class="split-container">
        <!-- Preparing Side -->
        <div class="section preparing-section">
            <div class="section-header">
                <div class="header-left">
                    <h2 class="title">PREPARING</h2>
                </div>
                <div class="count-badge" id="preparing-count">0</div>
            </div>
            <div class="orders-grid" id="preparing-orders">
                <!-- Content -->
            </div>
        </div>

        <!-- Now Serving Side -->
        <div class="section serving-section">
            <div class="section-header">
                <div class="header-left">
                    <h2 class="title">NOW SERVING</h2>
                </div>
                <div class="count-badge" id="serving-count">0</div>
            </div>
            <div class="orders-grid" id="serving-orders">
                <!-- Content -->
            </div>
        </div>
    </main>

    <script src="js/preparing_serving.js?v=<?php echo time(); ?>"></script>
</body>
</html>