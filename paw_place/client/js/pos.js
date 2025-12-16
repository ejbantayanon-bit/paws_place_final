// --- API URL PREFIX ---
const API_BASE = '../server/api';

// --- STATE MANAGEMENT ---
let currentCart = [];
let currentPendingOrderId = null;
let isSidebarHidden = false;
let allOrders = [];
let allMenuItems = [];
let menuMap = {}; // menu_item_id -> name lookup
let seenOrderIds = new Set();
let initialLoad = true; // don't mark orders as 'new' on first load
const NEW_BADGE_TIMEOUT_MS = 30000; // auto-clear NEW badge after 30s

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

// Load menu items once so we can display product names in orders
async function loadMenuItems() {
    try {
        const res = await fetch(`${API_BASE}/get_menu_items.php`);
        if (!res.ok) throw new Error('Failed to load menu items');
        const data = await res.json();
        const items = Array.isArray(data.items) ? data.items : [];
        items.forEach(i => {
            // API returns `item_id` for menu items
            if (i.item_id) menuMap[Number(i.item_id)] = i.name;
        });
    } catch (error) {
        console.error('Error loading menu items:', error);
    }
} 

function toggleSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    isSidebarHidden = !isSidebarHidden;
    sidebar.classList.toggle('hidden');
}

function switchView(viewName) {
    document.querySelectorAll('.view-section').forEach(v => v.classList.add('hidden'));
    document.getElementById(`view-${viewName}`).classList.remove('hidden');
    
    document.querySelectorAll('.sidebar-link').forEach(b => b.classList.remove('active'));
    document.getElementById(`nav-${viewName}`).classList.add('active');

    const titles = {
        pos: { title: 'Order Processing', subtitle: 'Manage incoming kiosk orders' },
        manual: { title: 'Walk-in Order', subtitle: 'Create orders manually' },
        tracker: { title: 'Order Tracker', subtitle: 'Track kitchen progress' },
        inventory: { title: 'Availability Control', subtitle: 'Manage item availability' },
        history: { title: 'Sales History', subtitle: 'View order history' }
    };
    
    document.getElementById('page-title').textContent = titles[viewName].title;
    document.getElementById('page-subtitle').textContent = titles[viewName].subtitle;

    if (viewName === 'tracker') loadOrderTracker();
    if (viewName === 'inventory') loadInventoryView();
    if (viewName === 'history') loadHistory();
}

function logout() {
    window.location.href = '../server/logout.php';
}

// --- POS VIEW FUNCTIONS ---
async function fetchPendingOrders(showToast = true) {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php?status=PENDING%20PAYMENT`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();
        // API returns { success: true, orders: [...] }
        const orders = Array.isArray(data.orders) ? data.orders : [];

        // Detect newly arrived orders (skip marking on initial load)
        orders.forEach(o => {
            if (!initialLoad && !seenOrderIds.has(o.order_id)) {
                o.is_new = true;
                // clear the 'new' flag after a short timeout so UI doesn't stay permanently highlighted
                setTimeout(() => {
                    const found = allOrders.find(x => x.order_id === o.order_id);
                    if (found) {
                        found.is_new = false;
                        renderPendingOrders();
                    }
                }, NEW_BADGE_TIMEOUT_MS);
            }
            seenOrderIds.add(o.order_id);
        });

        allOrders = orders;
        renderPendingOrders();
        if (initialLoad) initialLoad = false;
        if (showToast) showAlert('Orders refreshed', 'success');
    } catch (error) {
        console.error('Error fetching orders:', error);
        if (showToast) showAlert('Failed to load orders', 'error');
    }
} 

function renderPendingOrders() {
    const grid = document.getElementById('pending-orders-grid');
    grid.innerHTML = '';
    
    (allOrders || []).forEach(order => {
        const card = document.createElement('div');
        card.className = 'pending-order-card bg-white rounded-lg p-4 border-2 border-gray-200';
        card.dataset.orderId = order.order_id;
        // pass event so we can highlight the selected card reliably
        card.onclick = (e) => selectOrder(order.order_id, order, e);
        
        const itemCount = Array.isArray(order.order_items) ? order.order_items.length : 0;
        const itemsText = itemCount + ' item' + (itemCount !== 1 ? 's' : '');
        // API returns time_placed, not created_at
        const placedAt = order.time_placed ? new Date(order.time_placed).toLocaleString() : '';
        const newBadge = order.is_new ? `<span class="new-badge ml-2">NEW</span>` : '';
        card.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="font-bold text-gray-800">#${order.pre_order_code}${newBadge}</p>
                    <p class="text-xs text-gray-500">${placedAt}</p>
                </div>
                <span class="text-lg font-bold text-maroon">${formatCurrency(order.total_amount)}</span>
            </div>
            <p class="text-xs text-gray-600">${itemsText}</p>
            <div class="mt-2 text-xs font-bold text-gray-700 uppercase">
                ${order.status}
            </div>
        `;
        grid.appendChild(card);
        // preserve selection if this is the currently selected order
        if (currentPendingOrderId && currentPendingOrderId === order.order_id) {
            card.classList.add('selected');
        }
    });
} 

function selectOrder(orderId, order, evt) {
    currentPendingOrderId = orderId;
    currentCart = order.order_items || [];

    // mark as not new and forget it so it won't be highlighted again
    const found = allOrders.find(o => o.order_id === orderId);
    if (found) {
        found.is_new = false;
        seenOrderIds.delete(orderId);
    }

    // re-render the list so the NEW badge is removed and selection is applied consistently
    renderPendingOrders();

    // add selected class to the newly-rendered card
    const card = document.querySelector(`.pending-order-card[data-order-id="${orderId}"]`);
    if (card) card.classList.add('selected');

    document.getElementById('order-source-label').textContent = `Order #${order.pre_order_code}`;
    document.getElementById('cancel-btn').disabled = false;
    document.getElementById('pay-btn').disabled = false;

    renderCart();
} 

function renderCart() {
    const cartList = document.getElementById('cart-list');
    const subtotal = currentCart.reduce((sum, item) => sum + (item.price_at_sale * item.quantity), 0);
    
    cartList.innerHTML = currentCart.map(item => {
        const name = menuMap[item.menu_item_id] || menuMap[Number(item.menu_item_id)] || `Item #${item.menu_item_id}`;
        let modifiers = [];
        if (item.modifiers) {
            try { modifiers = (typeof item.modifiers === 'string') ? JSON.parse(item.modifiers) : item.modifiers; } catch(e) { modifiers = []; }
        }
        const addonsText = (modifiers && modifiers.length) ? `<p class="text-xs text-gray-500 mt-1">+ ${modifiers.join(', ')}</p>` : '';
        return `
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="font-bold text-gray-800">${name}</p>
                    <p class="text-sm text-gray-600">× ${item.quantity}</p>
                    ${addonsText}
                </div>
                <p class="font-bold text-maroon">${formatCurrency(item.price_at_sale * item.quantity)}</p>
            </div>
        </div>
        `;
    }).join('');

    document.getElementById('cart-subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('cart-total').textContent = formatCurrency(subtotal);

    document.getElementById('cash-tendered').addEventListener('input', (e) => {
        const cash = parseFloat(e.target.value) || 0;
        const change = Math.max(0, cash - subtotal);
        document.getElementById('change-due').textContent = formatCurrency(change);
    });
}

async function processOrder() {
    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: currentPendingOrderId, status: 'PREPARING' })
        });
        
        if (!response.ok) throw new Error('Failed to process order');
        
        showAlert('Order marked as PREPARING', 'success');
        currentCart = [];
        currentPendingOrderId = null;
        fetchPendingOrders();
    } catch (error) {
        console.error('Error processing order:', error);
        showAlert('Failed to process order', 'error');
    }
}

function cancelOrder() {
    if (!currentPendingOrderId) return;
    if (confirm('Cancel this order?')) {
        fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: currentPendingOrderId, status: 'CANCELLED' })
        }).then(() => {
            showAlert('Order cancelled', 'success');
            fetchPendingOrders();
        }).catch(() => showAlert('Failed to cancel order', 'error'));
    }
}

// --- ORDER TRACKER VIEW ---
async function loadOrderTracker() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();
        const orders = Array.isArray(data.orders) ? data.orders : [];

        const preparing = orders.filter(o => o.status === 'PREPARING');
        const ready = orders.filter(o => o.status === 'READY');

        document.getElementById('count-preparing').textContent = preparing.length;
        document.getElementById('count-ready').textContent = ready.length;

        document.getElementById('tracker-preparing-list').innerHTML = preparing.map(o => `
            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                <p class="font-bold text-gray-800">#${o.pre_order_code}</p>
                <p class="text-sm text-gray-600">${(o.order_items || []).length} items</p>
            </div>
        `).join('');

        document.getElementById('tracker-ready-list').innerHTML = ready.map(o => `
            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                <p class="font-bold text-gray-800">#${o.pre_order_code}</p>
                <p class="text-sm text-gray-600">${(o.order_items || []).length} items</p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading tracker:', error);
        showAlert('Failed to load tracker', 'error');
    }
}

// --- INVENTORY VIEW ---
async function loadInventoryView() {
    try {
        const [menuRes, inventoryRes] = await Promise.all([
            fetch(`${API_BASE}/get_menu_items.php`),
            fetch(`${API_BASE}/get_inventory.php`)
        ]);

        if (!menuRes.ok || !inventoryRes.ok) throw new Error('Failed to fetch inventory');

        const menuData = await menuRes.json();
        const inventoryData = await inventoryRes.json();
        const menuItems = Array.isArray(menuData.items) ? menuData.items : [];
        const rawMaterials = Array.isArray(inventoryData.inventory) ? inventoryData.inventory : [];

        document.getElementById('inventory-menu-list').innerHTML = menuItems.map(item => `
            <div class="flex justify-between items-center p-3 border rounded-lg">
                <span class="font-bold text-gray-800">${item.name}</span>
                <button onclick="toggleMenuAvailability(${item.item_id})" class="px-3 py-1 text-xs font-bold rounded ${item.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${item.is_available ? 'AVAILABLE' : 'UNAVAILABLE'}
                </button>
            </div>
        `).join('');

        document.getElementById('inventory-raw-list').innerHTML = rawMaterials.map(item => `
            <div class="flex justify-between items-center p-3 border rounded-lg ${item.is_low_stock ? 'border-red-200 bg-red-50' : ''}">
                <div>
                    <p class="font-bold text-gray-800">${item.name}</p>
                    <p class="text-sm ${item.is_low_stock ? 'text-red-700 font-bold' : 'text-gray-500'}">${item.quantity_on_hand} ${item.unit_of_measure || item.unit}</p>
                </div>
                ${item.is_low_stock ? '<span class="text-red-700 font-bold text-sm">LOW STOCK</span>' : ''}
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading inventory:', error);
        showAlert('Failed to load inventory', 'error');
    }
}

function toggleMenuAvailability(menuItemId) {
    console.log('Toggle availability for menu item:', menuItemId);
    // TODO: Implement menu item availability update
}

// --- HISTORY VIEW ---
async function loadHistory() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php`);
        if (!response.ok) throw new Error('Failed to fetch history');
        const data = await response.json();
        const orders = Array.isArray(data.orders) ? data.orders : [];

        const tbody = document.getElementById('history-table-body');
        tbody.innerHTML = orders.map(order => {
            const itemsList = (order.order_items || []).map(i => `${i.menu_item_id}×${i.quantity}`).join(', ');
            const placed = order.time_placed ? new Date(order.time_placed).toLocaleString() : '';
            return `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">#${order.pre_order_code}</td>
                    <td class="p-4">${placed}</td>
                    <td class="p-4 text-xs font-bold uppercase">${order.order_source}</td>
                    <td class="p-4 text-sm">${itemsList}</td>
                    <td class="p-4 font-bold">${formatCurrency(order.total_amount)}</td>
                    <td class="p-4"><span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-800">${order.status}</span></td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading history:', error);
        showAlert('Failed to load history', 'error');
    }
}

function filterHistory() {
    // TODO: Implement history filtering by date and search
}

// --- INITIALIZATION ---
// Load menu lookup then fetch orders
loadMenuItems().then(() => fetchPendingOrders());
// Poll for new orders every 5 seconds (quiet polling)
setInterval(() => fetchPendingOrders(false), 5000);
