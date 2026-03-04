<?php
include __DIR__ . '/../server/auth_check.php';
// Only Admin and Kitchen allowed to access Kitchen Terminal
if (!in_array($current_user_role, ['Admin','Kitchen'])) {
    header('Location: staff_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>GrabHound Kitchen Terminal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/pos.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
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
    </script>
</head>
<body>

    <div class="flex h-screen">
    
        <!-- SIDEBAR NAVIGATION -->
        <aside id="main-sidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shadow-lg z-20 transition-all duration-300">
            <div>
                <div class="p-6 border-b border-gray-100 flex items-center gap-3 h-20">
                    <div class="flex-none">
                        <img src="img/lgoo.png" alt="Logo" class="object-contain" style="width: 40px; height: 40px;">
                    </div>
                    <div>
                        <h1 class="font-black text-xl text-gray-800 whitespace-nowrap overflow-hidden text-ellipsis" style="max-width: 140px;" title="<?= htmlspecialchars($current_assigned_store) ?>">
                            <?= $current_assigned_store === 'All' ? 'GRABHOUND' : strtoupper(htmlspecialchars($current_assigned_store)) ?>
                        </h1>
                        <p class="text-xs text-gray-500 font-bold tracking-widest">KITCHEN TERMINAL</p>
                    </div>
                </div>
                
                <nav class="mt-6 space-y-1">
                    <button onclick="switchView('pos')" id="nav-pos" class="sidebar-link active w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <i class="ph-duotone ph-receipt" style="font-size:20px"></i> Order Queue
                    </button>
                    <button onclick="switchView('inventory')" id="nav-inventory" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <i class="ph-duotone ph-package" style="font-size:20px"></i> Availability
                    </button>
                    <button onclick="switchView('history')" id="nav-history" class="sidebar-link w-full text-left px-6 py-4 flex items-center gap-3 text-gray-600">
                        <i class="ph-duotone ph-calendar-blank" style="font-size:20px"></i> Order History
                    </button>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                <div class="bg-gray-50 p-3 rounded-lg mb-3 border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1">Logged in as</p>
                    <p class="font-extrabold text-gray-800 text-sm truncate uppercase" id="staff-name"><?= htmlspecialchars($current_user_name) ?></p>
                    <p class="text-xs text-maroon font-black mt-1 bg-red-50 inline-block px-2 py-0.5 rounded-md border border-red-100"><?= htmlspecialchars($current_assigned_store) ?> KITCHEN</p>
                </div>
                <button onclick="logout()" class="w-full py-2 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition text-sm">
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50">
            
            <!-- GLOBAL TOP HEADER -->
            <?php
            $allowed_stores = ['Paws Place', 'Pup Stop', 'Kennel Main', 'Kennel North'];
            $current_assigned_store = $_SESSION['assigned_store'] ?? 'Paws Place';
            ?>
            <header class="bg-white border-b border-gray-200 h-20 flex items-center px-6 justify-between shadow-sm z-10">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-maroon transition focus:outline-none" title="Toggle Menu">
                        <i class="ph ph-list text-2xl"></i>
                    </button>
                    
                    <div>
                        <h2 class="text-xl font-black text-gray-800" id="page-title">Order Queue</h2>
                        <p class="text-xs text-gray-500" id="page-subtitle">Monitor and update order status</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button id="store-switcher-btn" onclick="toggleStoreDropdown(event)" class="flex items-center gap-2 px-4 py-2 bg-red-50 text-maroon rounded-xl border border-red-100 font-black text-xs uppercase tracking-wider transition-all hover:bg-red-100">
                            <i class="ph-duotone ph-storefront text-lg"></i>
                            <span id="current-store-label"><?= htmlspecialchars($current_assigned_store) ?></span>
                            <i class="ph ph-caret-down font-bold"></i>
                        </button>
                        <div id="store-dropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl hidden z-50 overflow-hidden transform origin-top-right">
                            <?php foreach ($allowed_stores as $s): 
                                if ($s === $current_assigned_store) continue; // Hide current store
                            ?>
                                <button onclick="switchStore('<?= $s ?>')" class="w-full text-left px-4 py-3 text-xs font-bold text-gray-700 hover:bg-red-50 hover:text-maroon transition-colors flex items-center justify-between">
                                    <?= htmlspecialchars($s) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA -->
            <main class="flex-1 relative overflow-hidden">
                
                <!-- VIEW 1: ORDER QUEUE (ALL PENDING/PREPARING/READY) -->
                <div id="view-pos" class="view-section h-full flex">
                    <div class="flex-1 flex flex-col h-full">
                        <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Layout:</span>
                                <div class="flex bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                                    <button onclick="setPosViewMode('grid')" id="pos-view-grid-btn" class="flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all font-black text-[10px] uppercase tracking-tighter bg-maroon text-white shadow-sm">
                                        <i class="ph-bold ph-squares-four text-base"></i>
                                        <span>Grid</span>
                                    </button>
                                    <button onclick="setPosViewMode('list')" id="pos-view-list-btn" class="flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all font-black text-[10px] uppercase tracking-tighter text-gray-400 hover:text-gray-600">
                                        <i class="ph-bold ph-list text-base"></i>
                                        <span>List</span>
                                    </button>
                                </div>
                            </div>
                            <button onclick="fetchAllOrders()" class="text-maroon font-bold text-sm hover:underline flex items-center gap-1">
                                <span>⟳</span> Refresh All
                            </button>
                        </div>
                        <div id="pending-orders-container" class="p-6 custom-scroll flex-1">
                            <div id="pending-orders-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
                        </div>
                    </div>

                    <div class="w-96 bg-white border-l border-gray-200 h-full flex flex-col shadow-xl">
                        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <div id="selected-order-details">
                                <h3 class="font-bold text-gray-800 text-lg">Order Details</h3>
                                <p class="text-xs text-gray-500 font-mono" id="order-source-label">No Selection</p>
                            </div>
                        </div>
                        
                        <div id="cart-list" class="flex-1 custom-scroll p-4 space-y-2">
                            <div class="h-full flex flex-col items-center justify-center text-gray-300">
                                <span class="text-gray-300 mb-2"><i class="ph-duotone ph-arrow-left" style="font-size:40px"></i></span>
                                <p class="text-sm text-center px-4">Select an order to update its status.</p>
                            </div>
                        </div>

                        <div class="p-6 bg-white border-t border-gray-200 shadow-lg" id="status-update-panel">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Update Status</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="updateSelectedStatus('PREPARING')" id="status-preparing-btn" disabled class="py-4 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 disabled:opacity-30 disabled:cursor-not-allowed shadow-md text-sm">
                                    PREPARING
                                </button>
                                <button onclick="updateSelectedStatus('READY')" id="status-ready-btn" disabled class="py-4 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 disabled:opacity-30 disabled:cursor-not-allowed shadow-md text-sm">
                                    READY
                                </button>
                                <button onclick="updateSelectedStatus('SERVED')" id="status-served-btn" disabled class="py-4 bg-maroon text-white font-bold rounded-lg hover:bg-red-900 disabled:opacity-30 disabled:cursor-not-allowed shadow-md text-sm col-span-2">
                                    MARK AS SERVED
                                </button>
                                <button onclick="updateSelectedStatus('CANCELLED')" id="status-cancel-btn" disabled class="py-2 bg-gray-100 text-red-500 font-bold rounded-lg hover:bg-red-50 disabled:opacity-30 disabled:cursor-not-allowed border border-red-100 text-xs col-span-2 mt-2">
                                    CANCEL ORDER
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REDUNDANT VIEW SECTIONS REMOVED -->

                <!-- VIEW 2: AVAILABILITY CONTROL -->
                <div id="view-inventory" class="view-section h-full hidden flex flex-col bg-gray-50 overflow-hidden">
                    <div class="flex-1 flex flex-col p-6 overflow-hidden">
                        <div class="mb-6 flex-none">
                            <h3 class="font-black text-2xl text-gray-800 mb-2">Menu Availability</h3>
                            <p class="text-sm text-gray-500 mb-4">Toggle items available in the ordering terminal.</p>
                            
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Select Category</h4>
                                <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner border border-gray-200">
                                    <button onclick="setViewMode('grid')" id="view-grid-btn" class="flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all font-black text-[10px] uppercase tracking-tighter bg-white shadow-sm text-maroon">
                                        <i class="ph-bold ph-squares-four text-base"></i>
                                        <span>Grid</span>
                                    </button>
                                    <button onclick="setViewMode('list')" id="view-list-btn" class="flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all font-black text-[10px] uppercase tracking-tighter text-gray-400 hover:text-gray-600">
                                        <i class="ph-bold ph-list text-base"></i>
                                        <span>List</span>
                                    </button>
                                </div>
                            </div>
                            <div id="inventory-categories" class="flex gap-2 overflow-x-auto pb-2 custom-scroll no-scrollbar">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <div id="inventory-menu-grid" class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-6 overflow-y-auto pr-2 custom-scroll flex-1">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- VIEW 5: HISTORY -->
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
                                            <th class="p-4 border-b w-1/3">Items</th>
                                            <th class="p-4 border-b">Total</th>
                                            <th class="p-4 border-b">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history-table-body" class="text-sm text-gray-700"></tbody>
                                </table>
                            </div>
                        </div>
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

    <script src="js/kitchen.js"></script>

</body>
</html>
