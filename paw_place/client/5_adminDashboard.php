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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin.css">

</head>
<body>

    <div class="flex h-screen">
    
        <!-- SIDEBAR -->
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 flex flex-col justify-between shadow-lg z-30 transition-all duration-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto">
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
                    <button onclick="switchView('staff')" id="nav-staff" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>👥</span> Staff Management
                    </button>
                    <button onclick="switchView('settings')" id="nav-settings" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>⚙️</span> Shop Settings
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
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-maroon transition focus:outline-none" title="Toggle Menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="truncate">
                        <h2 class="text-2xl font-black text-gray-800 truncate" id="page-title">Dashboard Overview</h2>
                        <p class="text-xs text-gray-500 truncate" id="page-subtitle">Welcome back, Admin</p>
                    </div>
                </div>
                <div class="text-sm font-bold text-maroon" id="current-date"></div>
            </header>

            <!-- CONTENT VIEWS -->
            <main class="flex-1 relative overflow-hidden p-4 sm:p-8">

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

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-4 flex justify-between items-center">
                                <span>Sales Trend (Last 7 Days)</span>
                                <button onclick="exportSalesReport()" class="text-[10px] bg-maroon text-white px-2 py-1 rounded uppercase tracking-widest font-bold">Export CSV</button>
                            </h4>
                            <div class="h-64 h-full">
                                <canvas id="salesTrendsChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-4">Category Performance</h4>
                            <div class="h-64 h-full flex items-center justify-center">
                                <canvas id="categoryChart"></canvas>
                            </div>
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

                <!-- VIEW 5: STAFF MANAGEMENT -->
                <div id="view-staff" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Staff Management</h3>
                        <button onclick="openAddStaffModal()" class="px-4 py-2 bg-maroon text-white rounded-lg font-bold text-sm hover:bg-red-900">
                            + Add Staff Member
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 overflow-hidden flex flex-col">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Name</th>
                                        <th class="p-4 text-left border-b">Username</th>
                                        <th class="p-4 text-left border-b">Role</th>
                                        <th class="p-4 text-left border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-table" class="bg-gray-50"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VIEW 6: SHOP SETTINGS -->
                <div id="view-settings" class="view-section h-full hidden flex flex-col gap-6 custom-scroll pb-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- General Settings -->
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">General Settings</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Store Name</label>
                                    <input type="text" id="setting-store-name" value="PAWS PLACE" class="w-full p-2 border rounded font-bold text-gray-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Welcome Message</label>
                                    <textarea id="setting-welcome" class="w-full p-2 border rounded text-sm text-gray-600 h-24">Welcome to Paws Place! Grab your favorites.</textarea>
                                </div>
                                <button onclick="saveGeneralSettings()" class="py-2 px-4 bg-maroon text-white rounded font-bold text-sm">Save Changes</button>
                            </div>
                        </div>

                        <!-- Store Hours -->
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Business Hours</h4>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Opens at</label>
                                        <input type="time" id="setting-open" value="08:00" class="w-full p-2 border rounded text-gray-700">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Closes at</label>
                                        <input type="time" id="setting-close" value="18:00" class="w-full p-2 border rounded text-gray-700">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 italic">Note: These hours will eventually be used to auto-lock the Kiosk side.</p>
                                <button onclick="saveStoreHours()" class="py-2 px-4 bg-maroon text-white rounded font-bold text-sm">Update Hours</button>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Staff Modal -->
    <div id="staff-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-md">
            <h3 class="text-xl font-black text-gray-800 mb-4" id="staff-modal-title">Add Staff Member</h3>
            <div class="space-y-4">
                <input type="hidden" id="staff-id">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Full Name</label>
                    <input type="text" id="staff-full-name" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Username</label>
                    <input type="text" id="staff-username" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Role</label>
                    <select id="staff-role" class="w-full p-2 border rounded">
                        <option value="Cashier">Cashier</option>
                        <option value="Barista">Barista</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div id="password-field">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Password</label>
                    <input type="password" id="staff-password" class="w-full p-2 border rounded" placeholder="Required for new staff">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeStaffModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded hover:bg-gray-200">Cancel</button>
                <button onclick="saveStaff()" class="px-4 py-2 bg-maroon text-white font-bold rounded hover:bg-red-800">Save Staff</button>
            </div>
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

    <!-- Alert Container -->
    <div id="alert-container" class="fixed bottom-4 right-4 z-50"></div>

    <script src="js/admin.js"></script>

</body>
</html>
