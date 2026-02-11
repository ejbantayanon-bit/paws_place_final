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

function toggleSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
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
        staff: { title: 'Staff Management', subtitle: 'Manage employee accounts and roles' },
        settings: { title: 'Shop Settings', subtitle: 'Configure store preferences and hours' },
        logs: { title: 'Activity Logs', subtitle: 'View inventory adjustment history' }
    };

    document.getElementById('page-title').textContent = titles[viewName].title;
    document.getElementById('page-subtitle').textContent = titles[viewName].subtitle;

    if (viewName === 'dashboard') loadDashboard();
    if (viewName === 'menu') loadMenuItems();
    if (viewName === 'inventory') loadInventoryTable();
    if (viewName === 'staff') loadStaffView();
    if (viewName === 'settings') loadSettingsView();
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

        // Render charts
        initCharts(orders);

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

// --- STAFF MANAGEMENT ---
async function loadStaffView() {
    try {
        const res = await fetch(`${API_BASE}/get_users.php`);
        const data = await res.json();
        if (data.success) {
            const tbody = document.getElementById('staff-table');
            tbody.innerHTML = data.users.map(u => `
                <tr class="border-b hover:bg-white transition bg-gray-50">
                    <td class="p-4 font-bold text-gray-800">${u.full_name}</td>
                    <td class="p-4 text-gray-600">${u.username}</td>
                    <td class="p-4"><span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-bold uppercase">${u.role}</span></td>
                    <td class="p-4">
                        <button onclick='editStaff(${JSON.stringify(u)})' class="text-maroon font-bold hover:underline">Edit</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (e) {
        console.error('Error loading staff', e);
    }
}

function openAddStaffModal() {
    document.getElementById('staff-id').value = '';
    document.getElementById('staff-full-name').value = '';
    document.getElementById('staff-username').value = '';
    document.getElementById('staff-role').value = 'Cashier';
    document.getElementById('staff-password').value = '';
    document.getElementById('staff-modal-title').textContent = 'Add Staff Member';
    document.getElementById('staff-modal').classList.remove('hidden');
}

function editStaff(user) {
    document.getElementById('staff-id').value = user.user_id;
    document.getElementById('staff-full-name').value = user.full_name;
    document.getElementById('staff-username').value = user.username;
    document.getElementById('staff-role').value = user.role;
    document.getElementById('staff-password').value = ''; // Leave blank to keep existing
    document.getElementById('staff-modal-title').textContent = 'Edit Staff Member';
    document.getElementById('staff-modal').classList.remove('hidden');
}

function closeStaffModal() {
    document.getElementById('staff-modal').classList.add('hidden');
}

async function saveStaff() {
    const id = document.getElementById('staff-id').value;
    const body = {
        user_id: id || null,
        full_name: document.getElementById('staff-full-name').value,
        username: document.getElementById('staff-username').value,
        role: document.getElementById('staff-role').value,
        password: document.getElementById('staff-password').value
    };

    if (!body.full_name || !body.username) return showAlert('Name and Username are required', 'error');

    try {
        const res = await fetch(`${API_BASE}/save_staff.php`, {
            method: 'POST',
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            showAlert('Staff member saved!', 'success');
            closeStaffModal();
            loadStaffView();
        } else {
            showAlert(data.message, 'error');
        }
    } catch (e) {
        showAlert('Error connection to server', 'error');
    }
}

// --- SHOP SETTINGS ---
async function loadSettingsView() {
    try {
        const res = await fetch(`${API_BASE}/settings_handler.php?type=get`);
        const data = await res.json();
        if (data.success && data.settings) {
            document.getElementById('setting-store-name').value = data.settings.store_name || 'PAWS PLACE';
            document.getElementById('setting-welcome').value = data.settings.welcome_message || '';
            document.getElementById('setting-open').value = data.settings.open_time || '08:00';
            document.getElementById('setting-close').value = data.settings.close_time || '18:00';
        }
    } catch (e) {
        console.error('Error loading settings', e);
    }
}

async function saveGeneralSettings() {
    const storeName = document.getElementById('setting-store-name').value;
    const welcomeMajor = document.getElementById('setting-welcome').value;

    try {
        const res = await fetch(`${API_BASE}/settings_handler.php`, {
            method: 'POST',
            body: JSON.stringify({
                type: 'save_general',
                store_name: storeName,
                welcome_message: welcomeMajor
            })
        });
        const data = await res.json();
        if (data.success) showAlert('General settings saved!', 'success');
    } catch (e) { showAlert('Error saving settings', 'error'); }
}

async function saveStoreHours() {
    const open = document.getElementById('setting-open').value;
    const close = document.getElementById('setting-close').value;

    try {
        const res = await fetch(`${API_BASE}/settings_handler.php`, {
            method: 'POST',
            body: JSON.stringify({
                type: 'save_hours',
                open_time: open,
                close_time: close
            })
        });
        const data = await res.json();
        if (data.success) showAlert('Store hours updated!', 'success');
    } catch (e) { showAlert('Error saving hours', 'error'); }
}

// --- CHARTS & ANALYTICS ---
let salesChart = null;
let categoryChartObj = null;

function initCharts(orders) {
    if (!orders || orders.length === 0) return;

    const canvas1 = document.getElementById('salesTrendsChart');
    const canvas2 = document.getElementById('categoryChart');
    if (!canvas1 || !canvas2) return;

    // 1. Process Sales Trend (Last 7 Days)
    const last7Days = [...Array(7)].map((_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - i);
        return d.toISOString().split('T')[0];
    }).reverse();

    const dailySales = last7Days.map(date => {
        return orders
            .filter(o => o.time_placed && o.time_placed.startsWith(date))
            .reduce((sum, o) => sum + parseFloat(o.total_amount), 0);
    });

    if (salesChart) salesChart.destroy();
    salesChart = new Chart(canvas1, {
        type: 'line',
        data: {
            labels: last7Days.map(d => new Date(d).toLocaleDateString([], { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Revenue',
                data: dailySales,
                borderColor: '#800000',
                backgroundColor: 'rgba(128, 0, 0, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#800000'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => '₱' + value } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Process Category Performance
    // Note: This requires order_items which is a sub-array in the orders response
    const catSales = {};
    orders.forEach(o => {
        if (o.order_items) {
            o.order_items.forEach(item => {
                const cat = item.category_name || 'Other';
                catSales[cat] = (catSales[cat] || 0) + 1;
            });
        }
    });

    if (categoryChartObj) categoryChartObj.destroy();
    categoryChartObj = new Chart(canvas2, {
        type: 'doughnut',
        data: {
            labels: Object.keys(catSales),
            datasets: [{
                data: Object.values(catSales),
                backgroundColor: ['#800000', '#c53030', '#f56565', '#feb2b2', '#fed7d7', '#718096'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15, font: { size: 10 } } }
            },
            cutout: '70%'
        }
    });
}

function exportSalesReport() {
    showAlert('Preparing CSV Export...', 'info');
    fetch(`${API_BASE}/get_orders.php`)
        .then(res => res.json())
        .then(orders => {
            if (!orders || orders.length === 0) return showAlert('No orders to export', 'error');

            const headers = ['Order ID', 'Customer', 'Source', 'Total', 'Status', 'Date'];
            const rows = orders.map(o => [
                o.pre_order_code,
                o.customer_name || 'Walk-in',
                o.order_source,
                o.total_amount,
                o.status,
                o.time_placed
            ]);

            const csvContent = [headers, ...rows]
                .map(e => e.join(","))
                .join("\n");

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `sales_report_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showAlert('Sales report downloaded!', 'success');
        })
        .catch(e => showAlert('Failed to export data', 'error'));
}

// --- INITIALIZATION ---
formatDate();
loadDashboard();
