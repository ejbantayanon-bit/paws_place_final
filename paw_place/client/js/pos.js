// --- API URL PREFIX ---
const API_BASE = '../server/api';

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

        // Use addEventListener instead of setting onclick via attribute inside a callback
        btn.addEventListener('click', () => {
            // Execute the action (can be a function reference or a string to evaluate)
            if (typeof action.onclick === 'function') {
                action.onclick();
            } else if (typeof action.onclick === 'string') {
                // For string callbacks (like 'closeModal()'), we use the global window object
                // This replaces the risky btn.setAttribute('onclick', action.onclick) pattern
                const funcName = action.onclick.replace('()', '');
                if (window[funcName]) {
                    window[funcName]();
                } else {
                    // Fallback for more complex strings if needed (e.g. 'delete(123)')
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

function formatCurrency(amount) {
    return `₱${parseFloat(amount).toFixed(2)}`;
}

/**
 * Formats name for Card Display: Initials of all names except last, plus full Last Name.
 * Example: "Dean Louie Ramirez Araula" -> "D. L. R. ARAULA"
 */
function formatCardName(name) {
    if (!name || name.trim() === '' || name.toLowerCase() === 'customer' || name.toLowerCase() === 'guest') {
        return 'WALK-IN';
    }
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].toUpperCase();

    const last = parts.pop().toUpperCase();
    const initials = parts.map(p => p.charAt(0).toUpperCase() + '.').join(''); // "D.L.R." (no spaces based on screenshot compact look, or add space if preferred. Screenshot looks like D.L.R.)
    // Actually screenshot has D.L.R. ARAULA. (With space between initials and last name).
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
    if (viewName === 'tracker') loadOrderTracker();
    if (viewName === 'inventory') loadInventoryView();
    if (viewName === 'history') loadHistory();
}

function logout() {
    // Show confirmation modal
    showModal('Logout Confirmation', 'Are you sure you want to logout? You will be returned to the login screen.', [
        { text: 'Cancel', onclick: 'closeModal()' },
        { text: 'Logout', onclick: 'confirmLogout()', style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

function confirmLogout() {
    window.location.href = '../server/logout.php';
}

// --- PREPARING ORDERS VIEW ---
async function loadPreparingOrders() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php?status=PREPARING`);
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
        const response = await fetch(`${API_BASE}/get_orders.php?status=READY`);
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
    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: 'READY' })
        });

        if (!response.ok) throw new Error('Failed to update order');

        showAlert('Order marked as READY', 'success');
        loadPreparingOrders();
    } catch (error) {
        console.error('Error updating order:', error);
        showAlert('Failed to update order', 'error');
    }
}

async function markOrderFinalComplete(orderId) {
    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: 'SERVED' })
        });

        if (!response.ok) throw new Error('Failed to update order');

        showAlert('Order marked as SERVED', 'success');
        loadReadyOrders();
    } catch (error) {
        console.error('Error updating order:', error);
        showAlert('Failed to update order', 'error');
    }
}

async function cancelPreparingOrder(orderId) {
    showModal('Cancel Order', 'Are you sure you want to cancel this order? The customer changed their mind.', [
        { text: 'No, Keep Order', onclick: 'closeModal()' },
        { text: 'Yes, Cancel Order', onclick: `confirmCancelPreparing(${orderId})`, style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

async function confirmCancelPreparing(orderId) {
    closeModal();
    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: 'CANCELLED' })
        });

        if (!response.ok) throw new Error('Failed to cancel order');

        showAlert('Order cancelled', 'success');
        loadPreparingOrders();
    } catch (error) {
        console.error('Error cancelling order:', error);
        showAlert('Failed to cancel order', 'error');
    }
}

// --- POS VIEW FUNCTIONS ---
async function fetchPendingOrders(isAuto = false) {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php?status=PENDING%20PAYMENT`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();

        // Simple check: if data length changed or we want to force update
        // For now, always update to ensure latest data/time is shown
        allOrders = data.orders || [];
        renderPendingOrders();
        if (!isAuto) showAlert('Orders refreshed', 'success');
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
        // Apply styling: Default + Selection highlight if this is the currently selected order
        let className = 'pending-order-card bg-white rounded-lg p-4 border-2 border-gray-200 cursor-pointer transition-all hover:shadow-md';
        if (currentPendingOrderId === order.order_id) {
            className += ' selected ring-2 ring-[#800000] bg-red-50';
        }
        card.className = className;
        card.onclick = () => selectOrder(order.order_id, order);

        const itemsText = order.order_items.length + ' item' + (order.order_items.length !== 1 ? 's' : '');
        // Match screenshot layout: Name prominent red, Order ID + Time gray small, Items count, Status bold
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

function selectOrder(orderId, order) {
    currentPendingOrderId = orderId;
    currentCart = order.order_items || [];

    document.querySelectorAll('.pending-order-card').forEach(c => c.classList.remove('selected', 'ring-2', 'ring-[#800000]', 'bg-red-50'));
    event.currentTarget.classList.add('selected', 'ring-2', 'ring-[#800000]', 'bg-red-50');

    // Update Selected Order Header
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

    document.getElementById('cash-tendered').addEventListener('input', (e) => {
        const cash = parseFloat(e.target.value) || 0;
        const change = Math.max(0, cash - subtotal);
        document.getElementById('change-due').textContent = formatCurrency(change);
    });
}

async function processOrder() {
    const cashInput = document.getElementById('cash-tendered');
    const cash = parseFloat(cashInput.value) || 0;
    const subtotal = currentCart.reduce((sum, item) => sum + (item.price_at_sale * item.quantity), 0);

    // Validate cash input
    if (cash === 0) {
        showAlert('Please enter cash amount', 'error');
        return;
    }

    if (cash < subtotal) {
        showAlert('Cash amount is insufficient', 'error');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: currentPendingOrderId, status: 'PREPARING', cash_paid: cash })
        });

        if (!response.ok) throw new Error('Failed to process order');

        showAlert('Order marked as PREPARING', 'success');
        currentCart = [];
        currentPendingOrderId = null;
        cashInput.value = '';
        document.getElementById('change-due').textContent = '₱0.00';

        // Reset Header
        document.getElementById('selected-order-details').innerHTML = `
            <h3 class="font-bold text-gray-800 text-lg">Selected Order</h3>
            <p class="text-xs text-gray-500 font-mono" id="order-source-label">No Selection</p>
        `;

        document.getElementById('cancel-btn').disabled = true;
        document.getElementById('pay-btn').disabled = true;
        document.querySelectorAll('.pending-order-card').forEach(c => c.classList.remove('selected'));
        renderCart();
        fetchPendingOrders();
    } catch (error) {
        console.error('Error processing order:', error);
        showAlert('Failed to process order', 'error');
    }
}

function cancelOrder() {
    if (!currentPendingOrderId) return;
    showModal('Cancel Order', 'Are you sure you want to cancel this pending order?', [
        { text: 'No, Keep Order', onclick: 'closeModal()' },
        { text: 'Yes, Cancel Order', onclick: 'confirmCancelPending()', style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

function confirmCancelPending() {
    closeModal();
    fetch(`${API_BASE}/update_order_status.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: currentPendingOrderId, status: 'CANCELLED' })
    }).then(() => {
        currentPendingOrderId = null;
        currentCart = [];
        renderCart();
        document.querySelectorAll('.pending-order-card').forEach(c => c.classList.remove('selected', 'ring-2', 'ring-[#800000]', 'bg-red-50'));

        // Reset Header
        document.getElementById('selected-order-details').innerHTML = `
            <h3 class="font-bold text-gray-800 text-lg">Selected Order</h3>
            <p class="text-xs text-gray-500 font-mono" id="order-source-label">No Selection</p>
        `;

        document.getElementById('cancel-btn').disabled = true;
        document.getElementById('pay-btn').disabled = true;
        showAlert('Order cancelled', 'success');
        fetchPendingOrders();
    }).catch(() => showAlert('Failed to cancel order', 'error'));
}

// --- ORDER TRACKER VIEW ---
async function loadOrderTracker() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php`);
        if (!response.ok) throw new Error('Failed to fetch orders');
        const orders = await response.json();

        const preparing = orders.filter(o => o.status === 'PREPARING');
        const ready = orders.filter(o => o.status === 'READY');

        document.getElementById('count-preparing').textContent = preparing.length;
        document.getElementById('count-ready').textContent = ready.length;

        document.getElementById('tracker-preparing-list').innerHTML = preparing.map(o => `
            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                <p class="font-bold text-gray-800">#${o.pre_order_code} ${o.customer_name ? `<span class='text-xs'>(${o.customer_name})</span>` : ''}</p>
                <p class="text-sm text-gray-600">${o.order_items.length} items</p>
            </div>
        `).join('');

        document.getElementById('tracker-ready-list').innerHTML = ready.map(o => `
            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                <p class="font-bold text-gray-800">#${o.pre_order_code} ${o.customer_name ? `<span class='text-xs'>(${o.customer_name})</span>` : ''}</p>
                <p class="text-sm text-gray-600">${o.order_items.length} items</p>
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
        // Fetch categories first to normalize names
        const catRes = await fetch(`${API_BASE}/get_categories.php`);
        const catData = await catRes.json();
        const categoriesMap = {};
        if (catData.success && Array.isArray(catData.categories)) {
            catData.categories.forEach(c => { categoriesMap[c.category_id] = c.name; });
        }

        // Fetch ALL menu items (including hidden)
        const menuRes = await fetch(`${API_BASE}/get_menu_items.php?include_hidden=1`);
        const menuData = await menuRes.json();

        if (menuData.success && menuData.items) {
            inventoryMenu = menuData.items.map(item => ({
                ...item,
                category: ([1, 2].includes(Number(item.category_id)) ? 'Coffee' : (categoriesMap[item.category_id] || 'Uncategorized')),
                icon: getIconForCategoryName(categoriesMap[item.category_id] || '')
            }));

            // Set default category if not set or not in current menu
            const cats = [...new Set(inventoryMenu.map(i => i.category))];
            if (!cats.includes(activeInventoryCategory) && cats.length > 0) {
                activeInventoryCategory = cats[0];
            }

            renderInventoryCategories();
            renderInventoryMenu();
        }

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
    try {
        const response = await fetch(`${API_BASE}/toggle_menu_availability.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: menuItemId, is_available: newStatus })
        });

        const data = await response.json();
        if (data.success) {
            showAlert(`Item status updated to ${newStatus ? 'AVAILABLE' : 'UNAVAILABLE'}`, 'success');
            loadInventoryView(); // Refresh view
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error toggling availability:', error);
        showAlert('Failed to update availability', 'error');
    }
}

// --- HISTORY VIEW ---
// --- PAGINATION VARIABLES ---
let allHistoryOrders = [];
let filteredHistoryOrders = [];
let currentHistoryPage = 1;
const itemsPerPage = 10;

async function loadHistory() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php?view=history`);
        if (!response.ok) throw new Error('Failed to fetch history');
        const data = await response.json();
        // Backend now filters for COMPLETED and CANCELLED when view=history
        allHistoryOrders = data.orders || [];
        filteredHistoryOrders = allHistoryOrders;
        currentHistoryPage = 1;
        renderHistoryPage();
    } catch (error) {
        console.error('Error loading history:', error);
        showAlert('Failed to load history', 'error');
    }
}

function renderHistoryPage() {
    const startIndex = (currentHistoryPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageOrders = filteredHistoryOrders.slice(startIndex, endIndex);

    const tbody = document.getElementById('history-table-body');
    tbody.innerHTML = pageOrders.length === 0
        ? '<tr><td colspan="6" class="p-4 text-center text-gray-500">No orders found</td></tr>'
        : pageOrders.map(order => {
            const itemsList = order.order_items.map(i => `${i.name}×${i.quantity}`).join(', ');
            return `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">#${order.pre_order_code} ${order.customer_name ? `<br><span class='text-xs text-gray-500'>${order.customer_name}</span>` : ''}</td>
                    <td class="p-4">${new Date(order.time_placed).toLocaleString()}</td>
                    <td class="p-4 text-xs font-bold uppercase">${order.order_source}</td>
                    <td class="p-4 text-sm">${itemsList}</td>
                    <td class="p-4 font-bold">${formatCurrency(order.total_amount)}</td>
                    <td class="p-4"><span class="text-xs font-bold px-2 py-1 rounded ${order.status === 'SERVED' ? 'bg-green-100 text-green-800' : (order.status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')}">${order.status}</span></td>
                </tr>
            `;
        }).join('');

    updatePaginationControls();
}

function updatePaginationControls() {
    const totalPages = Math.ceil(filteredHistoryOrders.length / itemsPerPage);
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const numbersContainer = document.getElementById('pagination-numbers');

    prevBtn.disabled = currentHistoryPage === 1 || totalPages === 0;
    nextBtn.disabled = currentHistoryPage === totalPages || totalPages === 0;

    numbersContainer.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `px-3 py-2 rounded-lg font-bold ${i === currentHistoryPage ? 'bg-maroon text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'}`;
        btn.onclick = () => goToPage(i);
        numbersContainer.appendChild(btn);
    }
}

function previousPage() {
    if (currentHistoryPage > 1) {
        currentHistoryPage--;
        renderHistoryPage();
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredHistoryOrders.length / itemsPerPage);
    if (currentHistoryPage < totalPages) {
        currentHistoryPage++;
        renderHistoryPage();
    }
}

function goToPage(pageNum) {
    currentHistoryPage = pageNum;
    renderHistoryPage();
}

function filterHistory() {
    const dateFrom = document.getElementById('history-date-from').value;
    const dateTo = document.getElementById('history-date-to').value;
    const status = document.getElementById('history-status').value;
    const searchProduct = document.getElementById('history-search').value.toLowerCase();

    filteredHistoryOrders = allHistoryOrders.filter(order => {
        // Date filtering
        if (dateFrom) {
            const orderDate = new Date(order.time_placed).toLocaleDateString();
            const fromDate = new Date(dateFrom).toLocaleDateString();
            if (orderDate < fromDate) return false;
        }

        if (dateTo) {
            const orderDate = new Date(order.time_placed).toLocaleDateString();
            const toDate = new Date(dateTo).toLocaleDateString();
            if (orderDate > toDate) return false;
        }

        // Status filtering
        if (status && order.status !== status) return false;

        // Product search filtering
        if (searchProduct) {
            const hasProduct = order.order_items.some(item =>
                item.name.toLowerCase().includes(searchProduct)
            );
            if (!hasProduct) return false;
        }

        return true;
    });

    currentHistoryPage = 1;
    renderHistoryPage();
}

function resetFilter() {
    document.getElementById('history-date-from').value = '';
    document.getElementById('history-date-to').value = '';
    document.getElementById('history-status').value = '';
    document.getElementById('history-search').value = '';

    filteredHistoryOrders = allHistoryOrders;
    currentHistoryPage = 1;
    renderHistoryPage();
}

// --- INITIALIZATION ---
fetchPendingOrders();
// Auto-fetch pending orders every 3 seconds to show new Kiosk orders immediately
setInterval(() => fetchPendingOrders(true), 3000);

