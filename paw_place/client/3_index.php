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
                    <button onclick="switchView('preparing')" id="nav-preparing" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>👨‍🍳</span> Preparing Orders
                    </button>
                    <button onclick="switchView('ready')" id="nav-ready" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>✅</span> Ready Orders
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
                            <div id="selected-order-details">
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
                                    Confirm Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: PREPARING ORDERS -->
                <div id="view-preparing" class="view-section h-full hidden flex flex-col">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-end">
                        <button onclick="loadPreparingOrders()" class="text-maroon font-bold text-sm hover:underline flex items-center gap-1">
                            <span>⟳</span> Refresh
                        </button>
                    </div>
                    <div id="preparing-orders-container" class="p-6 custom-scroll flex-1">
                        <div id="preparing-orders-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
                    </div>
                </div>

                <!-- VIEW 2.5: READY ORDERS -->
                <div id="view-ready" class="view-section h-full hidden flex flex-col">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-end">
                        <button onclick="loadReadyOrders()" class="text-maroon font-bold text-sm hover:underline flex items-center gap-1">
                            <span>⟳</span> Refresh
                        </button>
                    </div>
                    <div id="ready-orders-container" class="p-6 custom-scroll flex-1">
                        <div id="ready-orders-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
                    </div>
                </div>



                <!-- VIEW 2: AVAILABILITY CONTROL -->
                <div id="view-inventory" class="view-section h-full hidden flex flex-col bg-gray-50 overflow-hidden">
                    <!-- MENU ITEM AVAILABILITY (Full Width Grid) -->
                    <div class="flex-1 flex flex-col p-6 overflow-hidden">
                        <div class="mb-6 flex-none">
                            <h3 class="font-black text-2xl text-gray-800 mb-2">Menu Availability</h3>
                            <p class="text-sm text-gray-500 mb-4">Click any item to toggle its availability in the kiosk.</p>
                            
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Select Category</h4>
                            <div id="inventory-categories" class="flex gap-2 overflow-x-auto pb-2 custom-scroll no-scrollbar">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <div id="inventory-menu-grid" class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-6 overflow-y-auto pr-2 custom-scroll flex-1">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- VIEW 3: SALES HISTORY -->
                <div id="view-history" class="view-section h-full hidden flex flex-col bg-gray-50">
                     <div class="p-6 bg-gray-50 border-b border-gray-200 shadow-sm">
                        <div class="bg-white p-4 rounded-lg border flex flex-wrap gap-3 mb-4">
                            <div class="flex gap-2">
                                <label class="text-xs font-bold text-gray-500 uppercase flex items-center">Date From:</label>
                                <input type="date" id="history-date-from" class="p-2 border rounded text-sm">
                            </div>
                            <div class="flex gap-2">
                                <label class="text-xs font-bold text-gray-500 uppercase flex items-center">Date To:</label>
                                <input type="date" id="history-date-to" class="p-2 border rounded text-sm">
                            </div>
                            <div class="flex gap-2">
                                <label class="text-xs font-bold text-gray-500 uppercase flex items-center">Status:</label>
                                <select id="history-status" class="p-2 border rounded text-sm">
                                    <option value="">All Status</option>
                                    <option value="SERVED">Served</option>
                                    <option value="PREPARING">Preparing</option>
                                    <option value="READY">Ready</option>
                                    <option value="CANCELLED">Cancelled</option>
                                </select>
                            </div>
                            <div class="flex gap-2 flex-1">
                                <input type="text" id="history-search" placeholder="Search Product..." class="p-2 border rounded text-sm flex-1">
                                <button onclick="filterHistory()" class="px-4 py-2 bg-maroon text-white rounded font-bold text-sm">Filter</button>
                                <button onclick="resetFilter()" class="px-4 py-2 bg-gray-400 text-white rounded font-bold text-sm hover:bg-gray-500">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 custom-scroll flex-1 flex flex-col overflow-hidden">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col">
                            <div class="overflow-y-auto flex-1">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold sticky top-0">
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
                        
                        <!-- Pagination Controls -->
                        <div class="mt-6 flex justify-center items-center gap-2">
                            <button onclick="previousPage()" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-bold text-maroon disabled:opacity-50" id="prev-btn">← Previous</button>
                            <div id="pagination-numbers" class="flex gap-1"></div>
                            <button onclick="nextPage()" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-bold text-maroon disabled:opacity-50" id="next-btn">Next →</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Generic Modal -->
    <div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-md transform transition-all scale-100">
            <h3 class="text-xl font-black text-gray-800 mb-2" id="modal-title">Confirmation</h3>
            <p class="text-gray-600 mb-6" id="modal-message">Are you sure you want to proceed?</p>
            <div class="flex justify-end gap-3" id="modal-actions">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded hover:bg-gray-200">Cancel</button>
                <button class="px-4 py-2 bg-maroon text-white font-bold rounded hover:bg-red-800">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Alert Toast -->
    <div id="alert-container" class="fixed bottom-4 right-4 z-50"></div>

    <script src="js/pos.js"></script>

</body>
</html>
