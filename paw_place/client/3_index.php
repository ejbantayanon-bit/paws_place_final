<?php
include __DIR__ . '/../server/auth_check.php';
// Only Admin and Cashier allowed to access POS
if (!in_array($current_user_role, ['Admin','Cashier'])) {
    header('Location: ../client/1_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place Staff POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/pos.css">
</head>
<body>

    <div class="flex h-screen">
        
        <!-- SIDEBAR NAVIGATION -->
        <aside id="main-sidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shadow-lg z-20 transition-all duration-300">
            <div>
                <div class="p-6 border-b border-gray-100 flex items-center gap-3 h-20">
                    <div class="text-3xl text-maroon">🐾</div>
                    <div>
                        <h1 class="font-black text-xl text-gray-800">PAWS PLACE</h1>
                        <p class="text-xs text-gray-500 font-bold tracking-widest">STAFF TERMINAL</p>
                    </div>
                </div>
                
                <nav class="mt-6 space-y-1">
                    <button onclick="switchView('pos')" id="nav-pos" class="sidebar-link active w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>💳</span> Order Processing
                    </button>
                    <button onclick="switchView('manual')" id="nav-manual" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📝</span> Walk-in Order
                    </button>
                    <button onclick="switchView('tracker')" id="nav-tracker" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>🍳</span> Order Tracker
                    </button>
                    <button onclick="switchView('inventory')" id="nav-inventory" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📦</span> Availability Control
                    </button>
                    <button onclick="switchView('history')" id="nav-history" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📅</span> Sales History
                    </button>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                    <p class="text-xs text-gray-500 uppercase font-bold">Logged in as</p>
                    <p class="font-bold text-gray-800 truncate" id="staff-name"><?= htmlspecialchars($current_user_name) ?></p>
                </div>
                <button onclick="logout()" class="w-full py-2 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition text-sm">
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50">
            
            <!-- GLOBAL TOP HEADER -->
            <header class="bg-white border-b border-gray-200 h-20 flex items-center px-6 justify-between shadow-sm z-10">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-maroon transition focus:outline-none" title="Toggle Menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div>
                        <h2 class="text-xl font-black text-gray-800" id="page-title">Order Processing</h2>
                        <p class="text-xs text-gray-500" id="page-subtitle">Manage incoming kiosk orders</p>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA -->
            <main class="flex-1 relative overflow-hidden">
                
                <!-- VIEW 1: POS / ORDER PROCESSING -->
                <div id="view-pos" class="view-section h-full flex">
                    <div class="flex-1 flex flex-col h-full">
                        <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-end">
                             <button onclick="fetchPendingOrders()" class="text-maroon font-bold text-sm hover:underline flex items-center gap-1">
                                <span>⟳</span> Refresh Queue
                            </button>
                        </div>
                        <div id="pending-orders-container" class="p-6 custom-scroll flex-1">
                            <div id="pending-orders-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
                        </div>
                    </div>

                    <div class="w-96 bg-white border-l border-gray-200 h-full flex flex-col shadow-xl">
                        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Selected Order</h3>
                                <p class="text-xs text-gray-500 font-mono" id="order-source-label">No Selection</p>
                            </div>
                            <button onclick="cancelOrder()" id="cancel-btn" disabled class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded border border-red-100 disabled:opacity-30">
                                CANCEL
                            </button>
                        </div>
                        
                        <div id="cart-list" class="flex-1 custom-scroll p-4 space-y-3">
                            <div class="h-full flex flex-col items-center justify-center text-gray-300">
                                <span class="text-4xl mb-2">👈</span>
                                <p class="text-sm text-center px-4">Select a pending order.</p>
                            </div>
                        </div>

                        <div class="p-6 bg-white border-t border-gray-200 shadow-lg">
                            <div class="flex justify-between mb-2 text-sm text-gray-600">
                                <span>Subtotal</span><span id="cart-subtotal">₱0.00</span>
                            </div>
                            <div class="flex justify-between mb-6 text-3xl font-black text-maroon">
                                <span>Total</span><span id="cart-total">₱0.00</span>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">Cash Tendered</label>
                                    <input type="number" id="cash-tendered" placeholder="0.00" class="w-full p-3 border rounded-lg font-bold text-xl focus:outline-none focus:border-maroon focus:ring-1 focus:ring-maroon">
                                </div>
                                <div class="flex justify-between bg-green-50 p-3 rounded-lg border border-green-100">
                                    <span class="text-sm font-bold text-green-800">Change Due:</span>
                                    <span id="change-due" class="text-lg font-black text-green-700">₱0.00</span>
                                </div>
                                <button onclick="processOrder()" id="pay-btn" disabled class="w-full py-4 bg-maroon text-white font-bold rounded-lg hover:bg-red-900 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                                    CONFIRM & PRINT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 4: WALK-IN / MANUAL ORDER (EMBEDDED KIOSK) -->
                <div id="view-manual" class="view-section h-full hidden flex flex-col w-full">
                    <!-- Embed Kiosk via Iframe -->
                    <iframe src="2_kiosk_ordering.html" class="w-full h-full border-none" title="Walk-in Order Interface"></iframe>
                </div>

                <!-- VIEW 5: ORDER TRACKER -->
                <div id="view-tracker" class="view-section h-full hidden flex flex-col">
                    <div class="flex-1 flex p-8 gap-8 overflow-hidden">
                        <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-yellow-500 p-4 text-white font-black text-xl flex justify-between items-center">
                                <span>⏱️ KITCHEN PREPARING</span>
                                <span class="text-sm bg-white text-yellow-600 px-2 py-1 rounded-full" id="count-preparing">0</span>
                            </div>
                            <div id="tracker-preparing-list" class="p-4 custom-scroll flex-1 space-y-3 bg-gray-50"></div>
                        </div>
                        <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-green-600 p-4 text-white font-black text-xl flex justify-between items-center">
                                <span>✅ READY / SERVED</span>
                                <span class="text-sm bg-white text-green-700 px-2 py-1 rounded-full" id="count-ready">0</span>
                            </div>
                            <div id="tracker-ready-list" class="p-4 custom-scroll flex-1 space-y-3 bg-gray-50"></div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: AVAILABILITY CONTROL -->
                <div id="view-inventory" class="view-section h-full hidden flex flex-col">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 custom-scroll flex-1">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                            <h3 class="font-bold text-xl mb-4 text-maroon border-b pb-2">Menu Items</h3>
                            <div id="inventory-menu-list" class="space-y-3"></div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                            <h3 class="font-bold text-xl mb-4 text-gray-700 border-b pb-2">Raw Materials (Critical)</h3>
                            <div id="inventory-raw-list" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 3: SALES HISTORY -->
                <div id="view-history" class="view-section h-full hidden flex flex-col bg-gray-50">
                     <div class="p-6 bg-gray-50 border-b border-gray-200 shadow-sm flex justify-end">
                        <div class="bg-white p-2 rounded-lg border flex gap-2">
                            <input type="date" id="history-date" class="p-2 border rounded text-sm">
                            <input type="text" id="history-search" placeholder="Search Product..." class="p-2 border rounded text-sm w-48">
                            <button onclick="filterHistory()" class="px-4 py-2 bg-maroon text-white rounded font-bold text-sm">Search</button>
                        </div>
                    </div>
                    <div class="p-8 custom-scroll flex-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="p-4 border-b">Order ID</th>
                                        <th class="p-4 border-b">Date/Time</th>
                                        <th class="p-4 border-b">Type</th>
                                        <th class="p-4 border-b">Items</th>
                                        <th class="p-4 border-b">Total</th>
                                        <th class="p-4 border-b">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="history-table-body" class="text-sm text-gray-700"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Alert Toast -->
    <div id="alert-container" class="fixed bottom-4 right-4 z-50"></div>

    <script src="js/pos.js"></script>

</body>
</html>
