<?php
session_start();

// Allow access only if a staff unlocked kiosk (Admin or Cashier)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin','Cashier'])) {
    header('Location: 1_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <title>Paws Place Self-Service Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/kiosk.css">
</head>
<body>

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>
    <div id="modal-container" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden"></div>

    <main class="kiosk-grid">
        <!-- LEFT COLUMN: Menu Selection -->
        <section class="bg-white p-6 flex flex-col h-full border-r border-gray-200 overflow-hidden">
            <header class="mb-4 flex justify-between items-center flex-none">
                <div class="flex items-center gap-3">
                    <button onclick="exitKiosk()" class="text-gray-300 hover:text-red-500 transition p-2" title="Staff Exit">🔒</button>
                    <div>
                        <h1 class="text-4xl font-black text-[#800000]">PAWS PLACE</h1>
                        <p class="text-gray-500 mt-1 text-sm font-medium tracking-wide">TAP TO ORDER</p>
                    </div>
                </div>
                <div class="text-5xl text-maroon">🐾</div>
            </header>
            
            <h3 class="font-bold text-gray-700 mb-2 text-sm uppercase tracking-wider flex-none">Categories</h3>
            <div id="category-filter" class="category-scroll mb-4 flex-none"></div>

            <!-- Menu Grid Container -->
            <div id="menu-items-container" class="menu-scroll flex-grow pr-2">
                <p class="text-center text-gray-500 col-span-full pt-20">Loading Menu...</p>
            </div>
        </section>

        <!-- RIGHT COLUMN: Order Cart & Submission (FIXED LAYOUT) -->
        <section class="bg-gray-50 p-6 flex flex-col h-full relative shadow-inner overflow-hidden">
            
            <!-- 1. Header (Fixed) -->
            <div class="flex-none flex justify-between items-center border-b border-gray-200 mb-2 pb-2">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Your Tray</h2>
                <button onclick="confirmClearCart()" id="empty-tray-btn" class="hidden text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-full transition uppercase tracking-wide">
                    Empty Tray
                </button>
            </div>
            
            <!-- 2. Cart List (Fills Remaining Space) -->
            <div id="cart-list" class="flex-1 overflow-y-auto min-h-0 space-y-3 p-2">
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <span class="text-4xl mb-2">🛒</span>
                    <p class="text-sm">Your tray is empty</p>
                </div>
            </div>

            <!-- 3. Footer (Fixed at Bottom) -->
            <div class="flex-none bg-white p-4 rounded-xl shadow-lg border border-gray-200 mt-4 z-10">
                <div class="flex justify-between font-medium text-lg text-gray-600 mb-1">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">₱0.00</span>
                </div>
                <div class="flex justify-between font-black text-4xl text-[#800000] mb-4">
                    <span>TOTAL:</span>
                    <span id="cart-total">₱0.00</span>
                </div>
                <button onclick="promptConfirmOrder()" class="touch-target w-full py-5 bg-[#800000] text-white font-black text-xl rounded-xl shadow-lg hover:bg-red-900 transform transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" id="place-order-btn" disabled>
                    PLACE ORDER
                </button>
            </div>
        </section>
    </main>

    <script src="js/kiosk.js"></script>
</body>
</html>
