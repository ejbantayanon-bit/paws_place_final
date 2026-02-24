<?php
session_start();

// Allow access if staff unlocked kiosk OR customer using kiosk
if (!isset($_SESSION['role'])) {
    header('Location: customer_login.php');
    exit;
}

// Get student/user name from session or localStorage (passed via JavaScript)
$userName = $_SESSION['full_name'] ?? $_SESSION['userName'] ?? 'Customer';
$userId = $_SESSION['user_id'] ?? $_SESSION['userId'] ?? null;
$isKiosk = $_SESSION['role'] === 'KIOSK';
?>
<script>
    const CURRENT_USER_NAME = "<?php echo htmlspecialchars($userName !== 'Customer' ? $userName : ''); ?>";
    const IS_GUEST = <?php echo ($userId ? 'false' : 'true'); ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <title>GrabHound Self-Service Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/kiosk.css">
</head>
<body>

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>
    <div id="modal-container" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden"></div>
    
    <!-- Order History Modal -->
    <div id="order-history-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40 hidden p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Order History</h3>
                <button onclick="closeOrderHistory()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="order-history-list" class="overflow-y-auto flex-1 p-4">
                <p class="text-center text-gray-500">Loading orders...</p>
            </div>
        </div>
    </div>

    <main class="kiosk-grid">
        <!-- LEFT COLUMN: Menu Selection -->
        <section class="bg-white p-6 flex flex-col h-full border-r border-gray-200 overflow-hidden">
            <header class="mb-4 flex justify-between items-center flex-none">
                <div class="flex items-center gap-4">
                    <button onclick="exitKiosk()" class="flex items-center justify-center w-12 h-12 bg-[#800000] hover:bg-red-900 text-white rounded-full shadow-md transition-all transform hover:scale-110 active:scale-95" title="Back to Login">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-3xl font-black text-[#800000]">GRABHOUND</h1>
                        <?php if ($isKiosk): ?>
                            <p class="text-[#800000] mt-1 text-sm font-semibold">Welcome, <?php echo htmlspecialchars($userName); ?>! 🐾</p>
                        <?php endif; ?>
                    </div>
                </div>
                <button onclick="openOrderHistory()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition" title="View your order history">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>My Orders</span>
                </button>
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
