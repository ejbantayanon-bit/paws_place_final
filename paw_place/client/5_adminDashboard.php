<?php
include __DIR__ . '/../server/auth_check.php';
// Only Admin allowed
if ($current_user_role !== 'Admin') {
    header('Location: ../client/1_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="flex h-screen">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shadow-lg z-20">
            <div>
                <div class="p-6 border-b border-gray-100 flex items-center gap-3 h-20">
                    <div class="text-3xl text-maroon">🐾</div>
                    <div>
                        <h1 class="font-black text-xl text-gray-800">PAWS PLACE</h1>
                        <p class="text-xs text-gray-500 font-bold tracking-widest">ADMIN PANEL</p>
                    </div>
                </div>

                <nav class="mt-6 space-y-1">
                    <button onclick="switchView('dashboard')" id="nav-dashboard" class="sidebar-link active w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📊</span> Dashboard
                    </button>
                    <button onclick="switchView('menu')" id="nav-menu" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>🍽️</span> Menu Management
                    </button>
                    <button onclick="switchView('inventory')" id="nav-inventory" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📦</span> Inventory & Stock
                    </button>
                    <button onclick="switchView('logs')" id="nav-logs" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📜</span> Activity Logs
                    </button>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                    <p class="text-xs text-gray-500 uppercase font-bold">Administrator</p>
                    <p class="font-bold text-gray-800 truncate" id="admin-name"><?= htmlspecialchars($current_user_name) ?></p>
                </div>
                <button onclick="logout()" class="w-full py-2 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition text-sm">
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50">
            
            <!-- HEADER -->
            <header class="bg-white border-b border-gray-200 h-20 flex items-center px-8 justify-between shadow-sm z-10">
                <div>
                    <h2 class="text-2xl font-black text-gray-800" id="page-title">Dashboard Overview</h2>
                    <p class="text-xs text-gray-500" id="page-subtitle">Welcome back, Admin</p>
                </div>
                <div class="text-sm font-bold text-maroon" id="current-date"></div>
            </header>

            <!-- CONTENT VIEWS -->
            <main class="flex-1 relative overflow-hidden p-8">

                <!-- VIEW 1: DASHBOARD OVERVIEW -->
                <div id="view-dashboard" class="view-section h-full flex flex-col gap-8 custom-scroll">
                    <!-- Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase">Total Sales (Today)</p>
                            <p class="text-3xl font-black text-maroon mt-1" id="stat-sales">₱0.00</p>
                        </div>
                        <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase">Total Orders</p>
                            <p class="text-3xl font-black text-gray-800 mt-1" id="stat-orders">0</p>
                        </div>
                        <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase">Low Stock Alerts</p>
                            <p class="text-3xl font-black text-red-600 mt-1" id="stat-low-stock">0</p>
                        </div>
                    </div>

                    <!-- Recent Sales Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="font-bold text-lg text-gray-800">Recent Transactions</h3>
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Order ID</th>
                                        <th class="p-4 text-left border-b">Items</th>
                                        <th class="p-4 text-left border-b">Total</th>
                                        <th class="p-4 text-left border-b">Status</th>
                                        <th class="p-4 text-left border-b">Time</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-orders" class="bg-gray-50"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: MENU MANAGEMENT -->
                <div id="view-menu" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Menu Items</h3>
                        <button onclick="openAddMenuModal()" class="px-4 py-2 bg-maroon text-white rounded-lg font-bold text-sm hover:bg-red-900">
                            + Add Item
                        </button>
                    </div>
                    
                    <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 custom-scroll flex-1"></div>
                </div>

                <!-- VIEW 3: INVENTORY MANAGEMENT -->
                <div id="view-inventory" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Raw Materials Stock</h3>
                        <button onclick="openAdjustInventoryModal()" class="px-4 py-2 bg-maroon text-white rounded-lg font-bold text-sm hover:bg-red-900">
                            ⚙️ Adjust Stock
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 overflow-hidden flex flex-col">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Material</th>
                                        <th class="p-4 text-left border-b">Stock</th>
                                        <th class="p-4 text-left border-b">Unit</th>
                                        <th class="p-4 text-left border-b">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-table" class="bg-gray-50"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VIEW 4: ACTIVITY LOGS -->
                <div id="view-logs" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Inventory Logs</h3>
                        <button onclick="loadActivityLogs()" class="px-2 py-1 text-sm text-maroon font-bold hover:underline">
                            ⟳ Refresh
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 overflow-hidden flex flex-col">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Timestamp</th>
                                        <th class="p-4 text-left border-b">Material</th>
                                        <th class="p-4 text-left border-b">Change</th>
                                        <th class="p-4 text-left border-b">New Stock</th>
                                        <th class="p-4 text-left border-b">Reason</th>
                                    </tr>
                                </thead>
                                <tbody id="logs-table" class="bg-gray-50"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alert-container" class="fixed bottom-4 right-4 z-50"></div>

    <!-- ADJUST INVENTORY MODAL -->
    <div id="adjustInventoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-96 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Adjust Inventory</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Quantity Change</label>
                <input type="number" id="adjust-quantity" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-maroon" placeholder="Enter quantity (positive or negative)">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Reason</label>
                <select id="adjust-reason" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-maroon">
                    <option value="">Select reason...</option>
                    <option value="Stock Received">Stock Received</option>
                    <option value="Order Fulfillment">Order Fulfillment</option>
                    <option value="Waste/Spoilage">Waste/Spoilage</option>
                    <option value="Inventory Correction">Inventory Correction</option>
                    <option value="Manual Adjustment">Manual Adjustment</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button onclick="submitAdjustInventory()" class="flex-1 bg-maroon text-white py-2 rounded-lg font-bold hover:bg-red-900">
                    Adjust
                </button>
                <button onclick="closeModal('adjustInventoryModal')" class="flex-1 bg-gray-200 text-gray-800 py-2 rounded-lg font-bold hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>

</body>
</html>
