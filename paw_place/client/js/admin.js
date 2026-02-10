// --- API BASE URL ---
const API_BASE = '../server/api';

// --- STATE ---
let currentView = 'dashboard';

// --- UTILITY FUNCTIONS ---
function showAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `p-4 rounded-lg shadow-lg text-white ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} mb-2`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

function formatCurrency(amount) {
    return `₱${parseFloat(amount).toFixed(2)}`;
}

function formatDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

function switchView(viewName) {
    currentView = viewName;
    document.querySelectorAll('.view-section').forEach(v => v.classList.add('hidden'));
    document.getElementById(`view-${viewName}`).classList.remove('hidden');

    document.querySelectorAll('.sidebar-link').forEach(b => b.classList.remove('active'));
    document.getElementById(`nav-${viewName}`).classList.add('active');

    const titles = {
        dashboard: { title: 'Dashboard Overview', subtitle: 'Welcome back, Admin' },
        menu: { title: 'Menu Management', subtitle: 'Manage menu items and categories' },
        inventory: { title: 'Inventory Management', subtitle: 'Track and manage raw materials' },
        logs: { title: 'Activity Logs', subtitle: 'View inventory adjustment history' }
    };

    document.getElementById('page-title').textContent = titles[viewName].title;
    document.getElementById('page-subtitle').textContent = titles[viewName].subtitle;

    if (viewName === 'dashboard') loadDashboard();
    if (viewName === 'menu') loadMenuItems();
    if (viewName === 'inventory') loadInventoryTable();
    if (viewName === 'logs') loadActivityLogs();
}

function showModal(title, message, actions) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-message').textContent = message;

    const actionsContainer = document.getElementById('modal-actions');
    actionsContainer.innerHTML = '';

    actions.forEach(action => {
        const btn = document.createElement('button');
        btn.textContent = action.text;
        btn.className = `px-4 py-2 font-bold rounded shadow-sm transition ${action.style || 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`;

        btn.addEventListener('click', () => {
            if (typeof action.onclick === 'function') {
                action.onclick();
            } else if (typeof action.onclick === 'string') {
                const funcName = action.onclick.replace('()', '');
                if (window[funcName]) {
                    window[funcName]();
                } else {
                    eval(action.onclick);
                }
            }
        });

        actionsContainer.appendChild(btn);
    });

    document.getElementById('modal-overlay').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.add('hidden');
}

function logout() {
    // Show confirmation modal instead of browser confirm
    showModal('Logout Confirmation', 'Are you sure you want to logout? You will return to the login screen.', [
        { text: 'Cancel', onclick: 'closeModal()' },
        { text: 'Logout', onclick: 'confirmLogout()', style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

function confirmLogout() {
    window.location.href = '../server/logout.php';
}

// --- SALES CALCULATION ---
function calculateTotalSalesForDay(orders) {
    const today = new Date();
    const startOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    
    return orders.reduce((sum, order) => {
        const orderDate = new Date(order.time_placed);
        const orderDateOnly = new Date(orderDate.getFullYear(), orderDate.getMonth(), orderDate.getDate());
        
        // Only include orders from today
        if (orderDateOnly.getTime() === startOfDay.getTime()) {
            return sum + parseFloat(order.total_amount);
        }
        return sum;
    }, 0);
}

// --- DASHBOARD VIEW ---
async function loadDashboard() {
    try {
        const [ordersRes, inventoryRes] = await Promise.all([
            fetch(`${API_BASE}/get_orders.php`),
            fetch(`${API_BASE}/get_inventory.php`)
        ]);

        if (!ordersRes.ok || !inventoryRes.ok) throw new Error('Failed to load dashboard');

        const ordersData = await ordersRes.json();
        const inventory = await inventoryRes.json();

        // Extract orders from response
        const orders = ordersData.orders || ordersData;

        // Calculate stats
        const totalSales = calculateTotalSalesForDay(orders);
        const lowStockCount = inventory.filter(i => i.is_low_stock).length;

        // Filter today's orders for count
        const today = new Date();
        const startOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const todayOrders = orders.filter(order => {
            const orderDate = new Date(order.time_placed);
            const orderDateOnly = new Date(orderDate.getFullYear(), orderDate.getMonth(), orderDate.getDate());
            return orderDateOnly.getTime() === startOfDay.getTime();
        });

        document.getElementById('stat-sales').textContent = formatCurrency(totalSales);
        document.getElementById('stat-orders').textContent = todayOrders.length;
        document.getElementById('stat-low-stock').textContent = lowStockCount;

        // Render recent orders
        const tbody = document.getElementById('dashboard-orders');
        tbody.innerHTML = orders.slice(0, 10).map(order => `
            <tr class="border-b hover:bg-white transition">
                <td class="p-4 font-bold text-maroon">#${order.pre_order_code}</td>
                <td class="p-4">${order.order_items.length} items</td>
                <td class="p-4 font-bold">${formatCurrency(order.total_amount)}</td>
                <td class="p-4"><span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-800">${order.status}</span></td>
                <td class="p-4 text-gray-500">${new Date(order.created_at).toLocaleTimeString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading dashboard:', error);
        showAlert('Failed to load dashboard', 'error');
    }
}

// --- MENU MANAGEMENT ---
async function loadMenuItems() {
    try {
        const response = await fetch(`${API_BASE}/get_menu_items.php`);
        if (!response.ok) throw new Error('Failed to load menu items');

        const items = await response.json();
        const grid = document.getElementById('menu-grid');

        grid.innerHTML = items.map(item => `
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-gray-800">${item.name}</h4>
                    <span class="text-xs font-bold px-2 py-1 rounded ${item.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${item.is_available ? 'ACTIVE' : 'INACTIVE'}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mb-3">${item.description || 'No description'}</p>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-black text-maroon">${formatCurrency(item.base_price)}</span>
                    <button onclick="editMenuItem(${item.menu_item_id})" class="text-xs font-bold text-maroon hover:underline">Edit</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading menu items:', error);
        showAlert('Failed to load menu items', 'error');
    }
}

function openAddMenuModal() {
    alert('Add menu item - TODO: Implement modal');
}

function editMenuItem(itemId) {
    alert('Edit menu item ' + itemId + ' - TODO: Implement modal');
}

// --- INVENTORY MANAGEMENT ---
async function loadInventoryTable() {
    try {
        const response = await fetch(`${API_BASE}/get_inventory.php`);
        if (!response.ok) throw new Error('Failed to load inventory');

        const items = await response.json();
        const tbody = document.getElementById('inventory-table');

        tbody.innerHTML = items.map(item => `
            <tr class="border-b hover:bg-white transition ${item.is_low_stock ? 'bg-red-50' : 'bg-gray-50'}">
                <td class="p-4 font-bold text-gray-800">${item.name}</td>
                <td class="p-4 font-bold text-gray-800">${item.quantity_on_hand}</td>
                <td class="p-4 text-gray-600">${item.unit}</td>
                <td class="p-4">
                    ${item.is_low_stock ? '<span class="text-red-700 font-bold text-xs">⚠️ LOW STOCK</span>' : '<span class="text-green-700 font-bold text-xs">✓ OK</span>'}
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading inventory:', error);
        showAlert('Failed to load inventory', 'error');
    }
}

function openAdjustInventoryModal() {
    alert('Adjust inventory - TODO: Implement modal');
}

// --- ACTIVITY LOGS ---
async function loadActivityLogs() {
    try {
        const response = await fetch(`${API_BASE}/inventory_logs.php?limit=50`);
        if (!response.ok) throw new Error('Failed to load logs');

        const logs = await response.json();
        const tbody = document.getElementById('logs-table');

        tbody.innerHTML = logs.map(log => `
            <tr class="border-b hover:bg-white transition bg-gray-50">
                <td class="p-4 text-xs text-gray-500">${new Date(log.created_at).toLocaleString()}</td>
                <td class="p-4 font-bold text-gray-800">${log.raw_material_name || 'N/A'}</td>
                <td class="p-4 font-bold ${log.quantity_changed > 0 ? 'text-green-700' : 'text-red-700'}">${log.quantity_changed > 0 ? '+' : ''}${log.quantity_changed}</td>
                <td class="p-4 font-bold text-gray-800">${log.quantity_after}</td>
                <td class="p-4 text-sm text-gray-600">${log.change_reason || 'Order Fulfillment'}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading logs:', error);
        showAlert('Failed to load logs', 'error');
    }
}

// --- INITIALIZATION ---
formatDate();
loadDashboard();
