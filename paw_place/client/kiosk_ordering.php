<?php
session_start();

// Allow access if staff unlocked kiosk OR customer using kiosk
if (!isset($_SESSION['role'])) {
    header('Location: store_selection.php');
    exit;
}

// Get student/user name from session or localStorage (passed via JavaScript)
$userName = $_SESSION['full_name'] ?? $_SESSION['userName'] ?? 'Customer';
$userId = $_SESSION['user_id'] ?? $_SESSION['userId'] ?? null;
$isKiosk = $_SESSION['role'] === 'KIOSK';
?>
<script>
    // Force disable zoom on iOS
    document.addEventListener('touchstart', function(event) {
        if (event.touches.length > 1) {
            event.preventDefault();
        }
    }, { passive: false });

    document.addEventListener('gesturestart', function(event) {
        event.preventDefault();
    });

    const CURRENT_USER_NAME = "<?php echo htmlspecialchars($userName !== 'Customer' ? $userName : ''); ?>";
    const IS_GUEST = <?php echo ($userId ? 'false' : 'true'); ?>;
    const CURRENT_STORE_ID = 'paws-place';
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <title>GrabHound Self-Service Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/kiosk.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
    <style>
        /* Toast Notification Styles */
        .toast {
            position: fixed;
            bottom: 32px;
            right: 432px; /* Next to the 400px cart */
            background: #1f2937;
            color: #f9fafb;
            font-size: 13px;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 1001;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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
                    <button onclick="exitKiosk()" class="flex items-center justify-center w-12 h-12 bg-[#800000] text-white rounded-full shadow-lg transition-all transform hover:scale-110 active:scale-95" title="Back to Login">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-3">
                            <img src="img/lgoo.png" alt="Logo" class="h-6 sm:h-8 object-contain">
                            <h1 class="text-2xl sm:text-4xl font-black text-[#800000] tracking-tighter uppercase leading-none">PAWS PLACE</h1>
                        </div>
                        <p class="text-[10px] sm:text-xs font-black text-[#800000] mt-1 uppercase tracking-widest leading-none opacity-70">Welcome, <?php echo explode(' ', trim($userName))[0]; ?>!</p>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" id="store-dropdown-btn" class="flex items-center gap-2 px-4 py-2 bg-[#800000] hover:bg-red-900 text-white rounded-lg text-sm font-semibold shadow-sm uppercase tracking-tighter transition-all">
                        <span id="current-store-display">Paws Place</span>
                        <svg class="w-4 h-4 ml-1 fill-current opacity-70 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="store-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl transition-all z-50 overflow-hidden transform opacity-0 invisible scale-95 origin-top-right">
                        <ul class="py-1 flex flex-col bg-white">
                            <li><button onclick="switchStore('paws-place')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold flex items-center justify-between transition-colors border-l-4 border-[#800000] bg-red-50 text-[#800000] cursor-default">
                                <span>Paws Place</span>
                                <span class="text-[10px] bg-[#800000] text-white py-0.5 px-2 rounded-full uppercase tracking-widest font-black">Current</span>
                            </button></li>
                            <li><button onclick="switchStore('pup-stop')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-[#800000] focus:bg-red-50 focus:text-[#800000] transition-colors border-l-4 border-transparent hover:border-[#800000]">Pup Stop</button></li>
                            <li><button onclick="switchStore('kennel-main')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-[#800000] focus:bg-red-50 focus:text-[#800000] transition-colors border-l-4 border-transparent hover:border-[#800000]">Kennel Main</button></li>
                            <li><button onclick="switchStore('kennel-north')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-[#800000] focus:bg-red-50 focus:text-[#800000] transition-colors border-l-4 border-transparent hover:border-[#800000]">Kennel North</button></li>
                        </ul>
                    </div>
                </div>
            </header>
            
            <div class="flex items-center justify-between mb-2 flex-none">
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Categories</h3>
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Layout:</span>
                    <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner border border-gray-200">
                        <button onclick="setViewMode('grid')" id="view-grid-btn" class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-1.5 rounded-bold transition-all font-black text-[9px] sm:text-[10px] uppercase tracking-tighter bg-white shadow-sm text-[#800000]">
                            <i class="ph-bold ph-squares-four text-sm sm:text-base"></i>
                            <span>Grid</span>
                        </button>
                        <button onclick="setViewMode('list')" id="view-list-btn" class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-1.5 rounded-bold transition-all font-black text-[9px] sm:text-[10px] uppercase tracking-tighter text-gray-400 hover:text-gray-600">
                            <i class="ph-bold ph-list text-sm sm:text-base"></i>
                            <span>List</span>
                        </button>
                    </div>
                </div>
            </div>
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
                <h2 class="cart-header">My Order <span id="cart-count" class="hidden">0</span></h2>
            </div>
            
            <!-- 2. Cart List (Fills Remaining Space) -->
            <div id="cart-list" class="flex-1 overflow-y-auto min-h-0 space-y-3 p-2">
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <span class="text-[#800000] opacity-30 mb-2"><i class="ph-duotone ph-shopping-cart" style="font-size:40px"></i></span>
                    <p class="text-sm">Your tray is empty</p>
                </div>
            </div>

            <!-- 3. Footer (Fixed at Bottom) -->
            <div class="flex-none bg-white p-4 rounded-xl shadow-lg border border-gray-200 mt-4 z-10">
                <div class="flex justify-between font-medium text-lg text-gray-600 mb-1">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">₱0.00</span>
                </div>
                <div class="flex justify-between font-black text-2xl sm:text-4xl text-[#800000] mb-4">
                    <span>TOTAL:</span>
                    <span id="cart-total">₱0.00</span>
                </div>
                <button onclick="promptConfirmOrder()" class="touch-target w-full py-5 bg-[#800000] text-white font-black text-xl rounded-xl shadow-lg hover:bg-red-900 transform transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" id="place-order-btn" disabled>
                    PLACE ORDER
                </button>
            </div>
        </section>
    </main>

    <!-- Toast Container -->
    <div id="toast" class="toast"></div>

    <!-- Hidden audio for notification -->
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script src="js/kiosk.js?v=<?php echo time(); ?>"></script>
    <script>
        // Notification Logic for Kiosk Page
        const userIdForNotif = '<?php echo $_SESSION['user_id'] ?? ''; ?>';
        let lastNotifStatuses = {};
        let isFirstLoadNotif = true;

        async function pollOrderStatus() {
            if (!userIdForNotif) return;
            try {
                const res = await fetch(`../server/api/get_student_orders.php?student_id=${encodeURIComponent(userIdForNotif)}`);
                const data = await res.json();
                if (data.success && Array.isArray(data.orders)) {
                    data.orders.forEach(order => {
                        const id = order.order_id;
                        const status = (order.status || '').toUpperCase();
                        const old = lastNotifStatuses[id];

                        if (!isFirstLoadNotif && old && old !== 'READY' && status === 'READY') {
                            showNotifToast(order.final_code || order.pre_order_code, order.order_source);
                            playNotifSoundEffect();
                        }
                        lastNotifStatuses[id] = status;
                    });
                    isFirstLoadNotif = false;
                }
            } catch (err) { console.error('Poll error:', err); }
        }

        function showNotifToast(code, store) {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.innerHTML = `
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="background:#800000; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);">
                        <i class="ph-duotone ph-bell-ringing" style="font-size:20px"></i>
                    </div>
                    <div>
                        <div style="font-weight:900; font-size:11px; text-transform:uppercase; color:#800000; letter-spacing:0.5px; opacity:0.6;">Order Alert</div>
                        <div style="font-size:13px; font-weight:700; color:#1f2937;">Order #${code} is READY at ${store}!</div>
                    </div>
                </div>
            `;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 5000);
        }

        function playNotifSoundEffect() {
            const sound = document.getElementById('notif-sound');
            if (sound) { sound.currentTime = 0; sound.play().catch(e => {}); }
        }

        if (userIdForNotif) {
            setInterval(pollOrderStatus, 10000);
            pollOrderStatus(); 
        }
    </script>
</body>
</html>
