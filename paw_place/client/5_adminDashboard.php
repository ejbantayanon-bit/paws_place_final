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
    <title>GrubHound Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <h1 class="font-black text-xl text-gray-800">GRUBHOUND</h1>
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
                    <button onclick="switchView('categories')" id="nav-categories" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>📂</span> Categories
                    </button>
                    <button onclick="switchView('history')" id="nav-history" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                         <span>📜</span> Sales History
                    </button>
                    <button onclick="switchView('employees')" id="nav-employees" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <span>👥</span> Employees
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
                <div id="view-dashboard" class="view-section h-full flex flex-col gap-8 overflow-y-auto custom-scroll pb-20">
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
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 relative min-h-[400px]">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-gray-800">Sales Trend</h3>
                                <div class="flex gap-2">
                                    <select id="sales-range" onchange="loadAnalytics()" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-maroon focus:ring focus:ring-maroon focus:ring-opacity-50 p-1">
                                        <option value="7days">Last 7 Days</option>
                                        <option value="30days">Last 30 Days</option>
                                        <option value="1year">Last 12 Months</option>
                                    </select>
                                </div>
                            </div>
                            <div class="h-[300px]">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 min-h-[400px]">
                            <h3 class="font-bold text-gray-800 mb-4">Top 5 Selling Items</h3>
                            <div class="h-[300px]">
                                <canvas id="topItemsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sales Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 w-full mb-6">
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

                <!-- VIEW 3: CATEGORY MANAGEMENT -->
                <div id="view-categories" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Category Management</h3>
                        <button onclick="openAddCategoryModal()" class="px-4 py-2 bg-maroon text-white rounded-lg font-bold text-sm hover:bg-red-900">
                            + Add Category
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b w-20">ID</th>
                                        <th class="p-4 text-left border-b">Category Name</th>
                                        <th class="p-4 text-center border-b w-32">Status</th>
                                        <th class="p-4 text-center border-b w-40">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categories-body" class="bg-white divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VIEW 4: SALES HISTORY -->
                <div id="view-history" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Sales History</h3>
                    </div>

                    <!-- Filter Bar (Staff POS Style) -->
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-wrap gap-3">
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
                            <button onclick="filterHistory()" class="px-3 py-1 bg-maroon text-white rounded font-bold text-sm">Filter</button>
                            <button onclick="resetFilter()" class="px-3 py-1 bg-gray-400 text-white rounded font-bold text-sm hover:bg-gray-500">Reset</button>
                            
                            <!-- Download Button Integrated -->
                             <button onclick="openExportModal()" class="px-3 py-1 bg-green-600 text-white rounded font-bold text-sm hover:bg-green-700 flex items-center gap-1 ml-auto">
                                <span>📥</span> Download
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Order ID</th>
                                        <th class="p-4 text-left border-b">Date/Time</th>
                                        <th class="p-4 text-left border-b">Type</th>
                                        <th class="p-4 text-left border-b w-1/3">Items</th>
                                        <th class="p-4 text-right border-b">Total</th>
                                        <th class="p-4 text-center border-b">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="history-table-body" class="bg-white divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="p-4 border-t border-gray-100 flex justify-center items-center gap-2 bg-gray-50">
                            <button onclick="previousPage()" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-bold text-maroon disabled:opacity-50" id="prev-btn">← Previous</button>
                            <div id="pagination-numbers" class="flex gap-1"></div>
                            <button onclick="nextPage()" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-bold text-maroon disabled:opacity-50" id="next-btn">Next →</button>
                        </div>
                    </div>
                </div>

                <!-- VIEW 5: EMPLOYEE MANAGEMENT -->
                <div id="view-employees" class="view-section h-full hidden flex flex-col gap-6">
                     <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">Employee Management</h3>
                        <button onclick="openAddUserModal()" class="px-4 py-2 bg-maroon text-white rounded-lg font-bold text-sm hover:bg-red-900">
                            + Add Employee
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Full Name</th>
                                        <th class="p-4 text-left border-b">Username</th>
                                        <th class="p-4 text-center border-b">Role</th>
                                        <th class="p-4 text-center border-b">Created At</th>
                                        <th class="p-4 text-center border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employees-body" class="bg-white divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VIEW 5: ACTIVITY LOGS -->
                <div id="view-logs" class="view-section h-full hidden flex flex-col gap-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-xl text-gray-800">System Activity Logs</h3>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto">
                            <table class="w-full text-sm text-gray-700 border-collapse">
                                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0">
                                    <tr>
                                        <th class="p-4 text-left border-b">Request ID</th>
                                        <th class="p-4 text-left border-b">User</th>
                                        <th class="p-4 text-center border-b">Action Type</th>
                                        <th class="p-4 text-left border-b">Description</th>
                                        <th class="p-4 text-left border-b">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="logs-body" class="bg-white divide-y divide-gray-100"></tbody>
                            </table>
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

    <!-- Alert Container -->
    <div id="alert-container" class="fixed bottom-4 right-4 z-50"></div>

    <script src="js/admin.js"></script>

</body>
</html>
