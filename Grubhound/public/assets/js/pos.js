// --- API URL PREFIX ---
const API_BASE = BASE_URL + 'api';

// --- STATE MANAGEMENT ---
let currentCart = [];
let currentPendingOrderId = null;
let isSidebarHidden = false;
let allOrders = [];
let allMenuItems = [];
let inventoryMenu = [];
let activeInventoryCategory = 'Milktea';

const CATEGORY_ICONS = {
    'Coffee': '☕',
    'Milktea': '🧋',
    'Milk Tea': '🧋',
    'Fruity Soda': '🥤',
    'Fruity': '🥤',
    'Specialty': '🌟',
    'Add Ons': '➕',
    'Ice Cream': '🍨',
    'Ice Cream in Cups': '🍨',
    'Ice Cream Bar': '🍦',
    'Milk Drink': '🥛',
    'Default': '🍽️'
};

function getIconForCategoryName(name) {
    const n = (name || '').toLowerCase();
    if (!n) return CATEGORY_ICONS['Default'];
    if (n.includes('coffee')) return CATEGORY_ICONS['Coffee'] || '☕';
    if (n.includes('milk tea') || n.includes('milktea') || (n.includes('milk') && n.includes('tea'))) return CATEGORY_ICONS['Milk Tea'] || '🧋';
    if (n.includes('milk') && !n.includes('tea')) return CATEGORY_ICONS['Milk Drink'] || '🥛';
    if (n.includes('soda') || n.includes('fruity')) return CATEGORY_ICONS['Fruity Soda'] || '🥤';
    if (n.includes('specialty')) return CATEGORY_ICONS['Specialty'] || '🌟';
    if (n.includes('add') || n.includes('addon') || n.includes('add ons')) return CATEGORY_ICONS['Add Ons'] || '➕';
    if (n.includes('ice cream bar') || n.includes('ice-cream bar')) return CATEGORY_ICONS['Ice Cream Bar'] || '🍦';
    if (n.includes('ice cream') || n.includes('ice')) return CATEGORY_ICONS['Ice Cream'] || '🍨';
    return CATEGORY_ICONS['Default'];
}

// --- UTILITY FUNCTIONS ---
function showAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `p-4 rounded-lg shadow-lg text-white ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} mb-2`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
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
                    // Parse args if needed, simple implementation for now
                    const match = action.onclick.match(/(\w+)\((.*)\)/);
                    if (match) {
                        const f = match[1];
                        const args = match[2];
                        if (window[f]) window[f](args);
                    }
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

function formatCurrency(amount) {
    return `₱${parseFloat(amount).toFixed(2)}`;
}

function formatCardName(name) {
    if (!name || name.trim() === '' || name.toLowerCase() === 'customer' || name.toLowerCase() === 'guest') {
        return 'WALK-IN';
    }
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].toUpperCase();

    const last = parts.pop().toUpperCase();
    const initials = parts.map(p => p.charAt(0).toUpperCase() + '.').join('');
    return `${initials} ${last}`;
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
        preparing: { title: 'Preparing Orders', subtitle: 'Orders ready to be prepared' },
        ready: { title: 'Ready Orders', subtitle: 'Orders ready for pickup' },

        inventory: { title: 'Availability Control', subtitle: 'Manage item availability' },
        history: { title: 'Sales History', subtitle: 'View order history' }
    };

    document.getElementById('page-title').textContent = titles[viewName].title;
    document.getElementById('page-subtitle').textContent = titles[viewName].subtitle;

    if (viewName === 'preparing') loadPreparingOrders();
    if (viewName === 'ready') loadReadyOrders();
    if (viewName === 'inventory') loadInventoryView();
    if (viewName === 'history') loadHistory();
}

function logout() {
    showModal('Logout Confirmation', 'Are you sure you want to logout?', [
        { text: 'Cancel', onclick: closeModal },
        { text: 'Logout', onclick: confirmLogout, style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

function confirmLogout() {
    window.location.href = BASE_URL + 'auth/logout';
}

// --- PREPARING ORDERS VIEW ---
async function loadPreparingOrders() {
    try {
        const response = await fetch(`${API_BASE}/orders?status=PREPARING`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();
        const orders = data.orders || [];

        const grid = document.getElementById('preparing-orders-grid');
        grid.innerHTML = '';

        if (orders.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><p class="text-lg">No orders to prepare</p></div>';
            return;
        }

        orders.forEach(order => {
            const itemsText = order.order_items.length + ' item' + (order.order_items.length !== 1 ? 's' : '');
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg p-4 border-2 border-yellow-200 shadow-md';

            const itemsList = order.order_items.map(item => `
                <div class="text-xs py-1 border-b border-gray-100 last:border-0">
                    <span class="font-bold">${item.name}</span> <span class="text-gray-500">×${item.quantity}</span>
                    ${item.modifiers && item.modifiers.trim() ? `<div class="text-gray-400">+ ${item.modifiers}</div>` : ''}
                </div>
            `).join('');

            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-lg text-gray-800">#${order.pre_order_code} ${order.customer_name ? `<span class='text-sm text-maroon'>(${order.customer_name})</span>` : ''}</p>
                        <p class="text-xs text-gray-500">${new Date(order.time_placed).toLocaleString()}</p>
                    </div>
                    <span class="text-sm font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded">PREPARING</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg mb-3 max-h-40 overflow-y-auto">
                    ${itemsList}
                </div>
                <p class="text-xs text-gray-600 mb-3">${itemsText}</p>
                <div class="bg-blue-50 p-3 rounded-lg mb-3 border border-blue-100">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs font-bold text-gray-600">Total:</span>
                        <span class="text-sm font-bold text-maroon">${formatCurrency(order.total_amount)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs font-bold text-gray-600">Amount Paid:</span>
                        <span class="text-sm font-bold text-green-600">${formatCurrency(order.cash_paid || order.total_amount)}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="markOrderReady(${order.order_id})" class="flex-1 py-2 bg-green-600 text-white font-bold rounded text-xs hover:bg-green-700">
                        Mark Ready
                    </button>
                    <button onclick="cancelPreparingOrder(${order.order_id})" class="flex-1 py-2 bg-red-600 text-white font-bold rounded text-xs hover:bg-red-700">
                        Cancel
                    </button>
                </div>
            `;
            grid.appendChild(card);
        });
    } catch (error) {
        console.error('Error loading preparing orders:', error);
        showAlert('Failed to load preparing orders', 'error');
    }
}

async function loadReadyOrders() {
    try {
        const response = await fetch(`${API_BASE}/orders?status=READY`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();
        const orders = data.orders || [];

        const grid = document.getElementById('ready-orders-grid');
        grid.innerHTML = '';

        if (orders.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><p class="text-lg">No ready orders</p></div>';
            return;
        }

        orders.forEach(order => {
            const itemsText = order.order_items.length + ' item' + (order.order_items.length !== 1 ? 's' : '');
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg p-4 border-2 border-green-200 shadow-md';

            const itemsList = order.order_items.map(item => `
                <div class="text-xs py-1 border-b border-gray-100 last:border-0">
                    <span class="font-bold">${item.name}</span> <span class="text-gray-500">×${item.quantity}</span>
                    ${item.modifiers && item.modifiers.trim() ? `<div class="text-gray-400">+ ${item.modifiers}</div>` : ''}
                </div>
            `).join('');

            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-lg text-gray-800">#${order.pre_order_code} ${order.customer_name ? `<span class='text-sm text-maroon'>(${order.customer_name})</span>` : ''}</p>
                        <p class="text-xs text-gray-500">${new Date(order.time_placed).toLocaleString()}</p>
                    </div>
                    <span class="text-sm font-bold text-green-600 bg-green-50 px-2 py-1 rounded">READY</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg mb-3 max-h-40 overflow-y-auto">
                    ${itemsList}
                </div>
                <p class="text-xs text-gray-600 mb-3">${itemsText}</p>
                <div class="flex gap-2">
                    <button onclick="markOrderFinalComplete(${order.order_id})" class="flex-1 py-2 bg-maroon text-white font-bold rounded text-xs hover:bg-red-800">
                        Mark Complete (Served)
                    </button>
                </div>
            `;
            grid.appendChild(card);
        });
    } catch (error) {
        console.error('Error loading ready orders:', error);
        showAlert('Failed to load ready orders', 'error');
    }
}

async function markOrderReady(orderId) {
    // TODO: Create update_order API
    // Placeholder using fetch to new endpoint
    showAlert('Feature Coming Soon: Update Order Status', 'info');
    /*
    try {
        const response = await fetch(`${API_BASE}/orders/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: 'READY' })
        });
        // ...
    } catch (error) { ... }
    */
}

async function markOrderFinalComplete(orderId) {
    showAlert('Feature Coming Soon: Mark Served', 'info');
}

async function cancelPreparingOrder(orderId) {
    showModal('Cancel Order', 'Cancel this order?', [
        { text: 'No', onclick: closeModal },
        { text: 'Yes', onclick: () => { closeModal(); showAlert('Feature Coming Soon: Cancel', 'info'); }, style: 'bg-red-600 text-white' }
    ]);
}


// --- POS VIEW FUNCTIONS ---
async function fetchPendingOrders(isAuto = false) {
    try {
        const response = await fetch(`${API_BASE}/orders?status=PENDING PAYMENT`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();

        allOrders = data.orders || [];
        renderPendingOrders();
        if (!isAuto && allOrders.length > 0) showAlert('Orders refreshed', 'success');
        if (!isAuto && allOrders.length === 0) showAlert('No pending orders', 'info');
    } catch (error) {
        console.error('Error fetching orders:', error);
        if (!isAuto) showAlert('Failed to load orders', 'error');
    }
}

function renderPendingOrders() {
    const grid = document.getElementById('pending-orders-grid');
    grid.innerHTML = '';

    allOrders.forEach(order => {
        const card = document.createElement('div');
        let className = 'pending-order-card bg-white rounded-lg p-4 border-2 border-gray-200 cursor-pointer transition-all hover:shadow-md';
        if (currentPendingOrderId === order.order_id) {
            className += ' selected ring-2 ring-[#800000] bg-red-50';
        }
        card.className = className;
        // Bind click event with closure to pass order data
        card.addEventListener('click', () => selectOrder(order.order_id, order, card));

        const itemsText = order.order_items.length + ' item' + (order.order_items.length !== 1 ? 's' : '');
        const timeString = new Date(order.time_placed).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        card.innerHTML = `
            <div class="mb-1">
                <h3 class="font-black text-lg text-[#800000] leading-tight uppercase truncate">
                    ${formatCardName(order.customer_name)}
                </h3>
                <div class="flex items-center text-xs font-bold text-gray-500 mt-1">
                    <span>#${order.pre_order_code}</span>
                    <span class="mx-1">•</span>
                    <span>${timeString}</span>
                </div>
            </div>
            
            <div class="mb-3">
                 <p class="text-xs text-gray-500 font-medium">${itemsText}</p>
            </div>

            <div class="flex justify-between items-end">
                <span class="text-[10px] font-black text-gray-700 uppercase tracking-wide">
                    ${order.status}
                </span>
                <span class="text-lg font-black text-[#800000]">${formatCurrency(order.total_amount)}</span>
            </div>
        `;
        grid.appendChild(card);
    });
}

function selectOrder(orderId, order, cardElement) {
    currentPendingOrderId = orderId;
    currentCart = order.order_items || [];

    document.querySelectorAll('.pending-order-card').forEach(c => c.classList.remove('selected', 'ring-2', 'ring-[#800000]', 'bg-red-50'));
    cardElement.classList.add('selected', 'ring-2', 'ring-[#800000]', 'bg-red-50');

    const customerBlock = document.getElementById('selected-order-details');
    customerBlock.innerHTML = `
        <h3 class="font-bold text-gray-800 text-lg">Selected Order</h3>
        <div class="mt-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">CUSTOMER</p>
            <h2 class="text-xl font-black text-[#800000] uppercase leading-tight mb-1">
                ${formatCardName(order.customer_name) || 'WALK-IN CUSTOMER'}
            </h2>
            <p class="text-xs text-gray-500 font-bold font-mono">Order #${order.pre_order_code}</p>
        </div>
    `;
    document.getElementById('cancel-btn').disabled = false;
    document.getElementById('pay-btn').disabled = false;

    renderCart();
}

function renderCart() {
    const cartList = document.getElementById('cart-list');
    const subtotal = currentCart.reduce((sum, item) => sum + (item.price_at_sale * item.quantity), 0);

    cartList.innerHTML = currentCart.map(item => `
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="font-bold text-gray-800">${item.name || 'Unknown Item'}</p>
                    <p class="text-xs text-gray-500">${item.modifiers ? `Add ons: ${item.modifiers}` : ''}</p>
                    <p class="text-sm text-gray-600 mt-1">× ${item.quantity}</p>
                </div>
                <p class="font-bold text-maroon">${formatCurrency(item.price_at_sale * item.quantity)}</p>
            </div>
        </div>
    `).join('');

    document.getElementById('cart-subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('cart-total').textContent = formatCurrency(subtotal);
}
// Add Event Listener for Cash Input properly
document.addEventListener('DOMContentLoaded', () => {
    const cashInput = document.getElementById('cash-tendered');
    if (cashInput) {
        cashInput.addEventListener('input', (e) => {
            const subtotal = currentCart.reduce((sum, item) => sum + (item.price_at_sale * item.quantity), 0);
            const cash = parseFloat(e.target.value) || 0;
            const change = Math.max(0, cash - subtotal);
            document.getElementById('change-due').textContent = formatCurrency(change);
        });
    }
    // Init View
    fetchPendingOrders(true);
});

async function processOrder() {
    showAlert('Feature Coming Soon: Process Payment', 'info');
}

function cancelOrder() {
    if (!currentPendingOrderId) return;
    showModal('Cancel Order', 'Cancel this pending order?', [
        { text: 'No', onclick: closeModal },
        { text: 'Yes', onclick: () => { closeModal(); showAlert('Feature Coming Soon: Cancel', 'info'); }, style: 'bg-red-600 text-white' }
    ]);
}

// --- INVENTORY VIEW ---
async function loadInventoryView() {
    try {
        const catRes = await fetch(`${API_BASE}/categories`);
        const catData = await catRes.json();
        const categories = catData.categories || [];
        const categoriesMap = {};
        categories.forEach(c => { categoriesMap[c.category_id] = c.name; });

        const menuRes = await fetch(`${API_BASE}/products?include_hidden=1`);
        const menuData = await menuRes.json();
        const items = menuData.items || [];

        inventoryMenu = items.map(item => ({
            ...item,
            category: ([1, 2].includes(Number(item.category_id)) ? 'Coffee' : (categoriesMap[item.category_id] || 'Uncategorized')),
            icon: getIconForCategoryName(categoriesMap[item.category_id] || '')
        }));

        const cats = [...new Set(inventoryMenu.map(i => i.category))];
        if (!cats.includes(activeInventoryCategory) && cats.length > 0) {
            activeInventoryCategory = cats[0];
        }

        renderInventoryCategories();
        renderInventoryMenu();

    } catch (error) {
        console.error('Error loading inventory:', error);
        showAlert('Failed to load inventory', 'error');
    }
}

function renderInventoryCategories() {
    const container = document.getElementById('inventory-categories');
    if (!container) return;

    const categories = [...new Set(inventoryMenu.map(item => item.category))];
    container.innerHTML = categories.map(cat => {
        const isActive = cat === activeInventoryCategory;
        const icon = getIconForCategoryName(cat);
        return `
            <button onclick="switchInventoryCategory('${cat}')" class="flex flex-col items-center justify-center p-2 rounded-xl border-2 transition-all w-24 h-20 flex-shrink-0 ${isActive ? 'bg-maroon text-white border-maroon shadow-md' : 'bg-white text-gray-600 border-gray-100 hover:border-maroon'}">
                <span class="text-2xl mb-1">${icon}</span>
                <span class="text-[9px] font-bold uppercase tracking-tight text-center leading-none">${cat}</span>
            </button>
        `;
    }).join('');
}

function switchInventoryCategory(cat) {
    activeInventoryCategory = cat;
    renderInventoryCategories();
    renderInventoryMenu();
}

function renderInventoryMenu() {
    const grid = document.getElementById('inventory-menu-grid');
    if (!grid) return;

    const filtered = inventoryMenu.filter(item => item.category === activeInventoryCategory);
    grid.innerHTML = filtered.map(item => `
        <div onclick="toggleMenuAvailability(${item.item_id}, ${!item.is_available})" class="relative h-fit group cursor-pointer bg-white rounded-xl shadow-sm border-2 transition-all hover:shadow-md ${item.is_available ? 'border-transparent' : 'border-red-200 opacity-75'}">
            <div class="p-4 flex flex-col items-center">
                <div class="text-4xl mb-2">${item.icon}</div>
                <p class="text-sm font-bold text-gray-800 text-center line-clamp-2 h-10">${item.name}</p>
                <div class="mt-2 text-[10px] font-black px-2 py-1 rounded-full w-28 text-center ${item.is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                    ${item.is_available ? 'AVAILABLE' : 'UNAVAILABLE'}
                </div>
            </div>
            ${!item.is_available ? '<div class="absolute inset-0 bg-red-50 bg-opacity-20 flex items-center justify-center rounded-xl"></div>' : ''}
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="bg-gray-800 text-white text-[10px] px-2 py-0.5 rounded">Click to Toggle</div>
            </div>
        </div>
    `).join('');
}

async function toggleMenuAvailability(menuItemId, newStatus) {
    showAlert('Feature Coming Soon: Toggle Availability', 'info');
}

// --- HISTORY VIEW ---
// Placeholder for History
async function loadHistory() {
    // reuse existing logic but point to new API
}
