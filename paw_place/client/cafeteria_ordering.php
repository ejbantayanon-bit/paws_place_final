<?php
session_start();

// Auth guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'KIOSK') {
    header('Location: store_selection.php');
    exit;
}

$userName = $_SESSION['full_name'] ?? 'Customer';
$userId = $_SESSION['user_id'] ?? '';
$isKiosk = true;

// Get location info from URL
$locationId = $_GET['location_id'] ?? '';
$storeName = $_GET['store'] ?? 'Cafeteria';
?>
<script>
    const CURRENT_USER_NAME = "<?php echo htmlspecialchars($userName); ?>";
    const CURRENT_USER_ID = "<?php echo htmlspecialchars($userId); ?>";
    const LOCATION_ID = "<?php echo htmlspecialchars($locationId); ?>";
    const STORE_NAME = "<?php echo htmlspecialchars($storeName); ?>";
    const IS_GUEST = <?php echo ($userId ? 'false' : 'true'); ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GrabHound - <?php echo htmlspecialchars($storeName); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/cafeteria_ordering.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
</head>
<body>

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>
    <div id="modal-container" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden"></div>

    <main class="kiosk-grid">
        <!-- LEFT COLUMN: Menu Selection -->
        <section class="bg-white p-6 flex flex-col h-full border-r border-gray-200 overflow-hidden">
            <header class="mb-4 flex justify-between items-center flex-none">
                <div class="flex items-center gap-4">
                    <button onclick="goBack()" class="flex items-center justify-center w-12 h-12 bg-[#800000] hover:bg-red-900 text-white rounded-full shadow-md transition-all transform hover:scale-110 active:scale-95" title="Back to Stores">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-3xl font-black text-[#800000]"><?php echo htmlspecialchars(strtoupper($storeName)); ?></h1>
                        <p class="text-[#800000] mt-1 text-sm font-semibold">Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
                    </div>
                </div>
            </header>
            
            <h3 class="font-bold text-gray-700 mb-2 text-sm uppercase tracking-wider flex-none">Categories</h3>
            <div id="category-filter" class="category-scroll mb-4 flex-none"></div>

            <!-- Menu Grid Container -->
            <div id="menu-items-container" class="menu-scroll flex-grow pr-2">
                <p class="text-center text-gray-500 col-span-full pt-20">Loading Menu...</p>
            </div>
        </section>

        <!-- RIGHT COLUMN: Order Cart -->
        <section class="bg-gray-50 p-6 flex flex-col h-full relative shadow-inner overflow-hidden">
            
            <!-- 1. Header -->
            <div class="flex-none flex justify-between items-center border-b border-gray-200 mb-2 pb-2">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Your Tray</h2>
                <span id="cart-count" class="text-sm text-gray-500">0 items</span>
            </div>
            
            <!-- 2. Cart List -->
            <div id="cart-list" class="flex-1 overflow-y-auto min-h-0 space-y-3 p-2">
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <span class="text-gray-300 mb-2"><i class="ph-duotone ph-shopping-cart" style="font-size:40px"></i></span>
                    <p class="text-sm">Your tray is empty</p>
                </div>
            </div>

            <!-- 3. Footer -->
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

    <script src="js/cafeteria_ordering.js?v=<?php echo time(); ?>"></script>
</body>
</html>
