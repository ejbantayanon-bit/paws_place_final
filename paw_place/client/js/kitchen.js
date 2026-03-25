// --- API URL PREFIX ---
const API_BASE = '../server/api';

// Helper to set monthly date defaults (1st of month to Today)
function setDefaultDateRange(fromId, toId) {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    const fromEl = document.getElementById(fromId);
    const toEl = document.getElementById(toId);
    if (fromEl) fromEl.value = `${year}-${month}-01`;
    if (toEl) toEl.value = `${year}-${month}-${day}`;
}

let viewMode = 'grid'; // For inventory
let posViewMode = 'grid'; // For order queue

// Helper to clean item names
function cleanName(name) {
    if (!name) return "";
    return name.replace(/\s*@\s*[\d.]+/g, '').replace(/\s*\(Hot\)/gi, '').trim();
}

function setViewMode(mode) {
    viewMode = mode;
    const gridBtn = document.getElementById('view-grid-btn');
    const listBtn = document.getElementById('view-list-btn');

    if (mode === 'grid') {
        gridBtn.classList.add('bg-white', 'shadow-sm', 'text-maroon');
        gridBtn.classList.remove('text-gray-400');
        listBtn.classList.remove('bg-white', 'shadow-sm', 'text-maroon');
        listBtn.classList.add('text-gray-400');
    } else {
        listBtn.classList.add('bg-white', 'shadow-sm', 'text-maroon');
        listBtn.classList.remove('text-gray-400');
        gridBtn.classList.remove('bg-white', 'shadow-sm', 'text-maroon');
        gridBtn.classList.add('text-gray-400');
    }
    renderInventoryMenu();
}

function setPosViewMode(mode) {
    posViewMode = mode;
    const gridBtn = document.getElementById('pos-view-grid-btn');
    const listBtn = document.getElementById('pos-view-list-btn');

    if (mode === 'grid') {
        gridBtn.classList.add('bg-maroon', 'text-white', 'shadow-sm');
        gridBtn.classList.remove('text-gray-400', 'hover:text-gray-600');
        listBtn.classList.remove('bg-maroon', 'text-white', 'shadow-sm');
        listBtn.classList.add('text-gray-400', 'hover:text-gray-600');
    } else {
        listBtn.classList.add('bg-maroon', 'text-white', 'shadow-sm');
        listBtn.classList.remove('text-gray-400', 'hover:text-gray-600');
        gridBtn.classList.remove('bg-maroon', 'text-white', 'shadow-sm');
        gridBtn.classList.add('text-gray-400', 'hover:text-gray-600');
    }
    renderAllOrders();
}

// --- STATE MANAGEMENT ---
let currentCart = [];
let currentSelectedOrderId = null;
let currentSelectedOrderStatus = null;
let isSidebarHidden = false;
let allOrders = [];
let inventoryMenu = [];
let activeInventoryCategory = 'Milktea';
let allHistoryOrders = [];
let filteredHistoryOrders = [];
let currentHistoryPage = 1;
const itemsPerPage = 10;



// --- UTILITY ---
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
            if (typeof action.onclick === 'function') action.onclick();
            else if (typeof action.onclick === 'string') {
                const funcName = action.onclick.replace('()', '');
                if (window[funcName]) window[funcName]();
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
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
}

function formatCardName(name) {
    if (!name || name.toLowerCase() === 'customer' || name.toLowerCase() === 'guest') return 'WALK-IN';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].toUpperCase();
    const last = parts.pop().toUpperCase();
    const initials = parts.map(p => p.charAt(0).toUpperCase() + '.').join('');
    return `${initials} ${last}`;
}

function toggleSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    sidebar.classList.toggle('hidden');
}

function switchView(viewName) {
    document.querySelectorAll('.view-section').forEach(v => v.classList.add('hidden'));
    document.getElementById(`view-${viewName}`).classList.remove('hidden');
    document.querySelectorAll('.sidebar-link').forEach(b => b.classList.remove('active'));
    document.getElementById(`nav-${viewName}`).classList.add('active');

    const titles = {
        pos: { title: 'Order Queue', subtitle: 'Monitor and update order status' },
        preparing: { title: 'Preparing Orders', subtitle: 'Orders currently being prepared' },
        ready: { title: 'Ready Orders', subtitle: 'Orders waiting for pickup' },
        inventory: { title: 'Availability Control', subtitle: 'Manage menu items' },
        history: { title: 'Order History', subtitle: 'Recent orders log' }
    };
    document.getElementById('page-title').textContent = titles[viewName].title;
    document.getElementById('page-subtitle').textContent = titles[viewName].subtitle;

    if (viewName === 'pos') fetchAllOrders();
    if (viewName === 'inventory') loadInventoryView();
    if (viewName === 'history') {
        setDefaultDateRange('history-date-from', 'history-date-to');
        loadHistory();
    }
}

function openStoreSelectionModal() {
    const modal = document.getElementById('store-selection-overlay');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeStoreSelectionModal() {
    const modal = document.getElementById('store-selection-overlay');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function switchStore(store) {
    try {
        const res = await fetch(`${API_BASE}/set_assigned_store.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ store })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('current-store-label').textContent = store;
            closeStoreSelectionModal();
            // Refresh current view
            const activeNav = document.querySelector('.sidebar-link.active');
            if (activeNav) {
                const view = activeNav.id.replace('nav-', '');
                if (view === 'pos') fetchAllOrders();
                else if (view === 'inventory') loadInventoryView();
                else if (view === 'history') loadHistory();
            }
            showAlert(`Switched to ${store}`, 'success');
            // Update sidebar label too
            const sidebarStoreLabel = document.querySelector('.text-maroon.font-black.mt-1');
            if (sidebarStoreLabel) sidebarStoreLabel.textContent = `${store.toUpperCase()} KITCHEN`;
        }
    } catch (e) { showAlert('Failed to switch store', 'error'); }
}

function logout() {
    showModal('Logout', 'Are you sure you want to logout?', [
        { text: 'Cancel', onclick: 'closeModal()' },
        { text: 'Logout', onclick: () => window.location.href = '../server/logout.php', style: 'bg-red-600 text-white' }
    ]);
}

// --- QUEUE VIEW ---
async function fetchAllOrders() {
    try {
        const response = await fetch(`${API_BASE}/get_orders.php`);
        const data = await response.json();
        // Only show orders that are already paid/being prepared or ready
        allOrders = (data.orders || []).filter(o => ['PREPARING', 'READY'].includes(o.status));
        renderAllOrders();
    } catch (err) { showAlert('Failed to fetch orders', 'error'); }
}

function renderAllOrders() {
    const grid = document.getElementById('pending-orders-grid');
    if (posViewMode === 'list') grid.className = "flex flex-col gap-3";
    else grid.className = "grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4";
    grid.innerHTML = '';

    allOrders.forEach(order => {
        const card = document.createElement('div');
        const isSelected = currentSelectedOrderId === order.order_id;
        card.className = `bg-white rounded-lg p-4 border-2 shadow-sm cursor-pointer transition-all hover:shadow-md ${isSelected ? 'ring-2 ring-maroon bg-red-50 border-maroon' : 'border-gray-200'}`;
        card.onclick = () => selectOrder(order);

        const time = new Date(order.time_placed).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const itemsCount = `${order.order_items.length} Item${order.order_items.length > 1 ? 's' : ''}`;

        let statusBadge = '';
        if (order.status === 'PREPARING') statusBadge = '<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[9px] font-black rounded-full">PREPARING</span>';
        else if (order.status === 'READY') statusBadge = '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-[9px] font-black rounded-full">READY</span>';
        else if (order.status === 'PENDING PAYMENT') statusBadge = '<span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-black rounded-full">PENDING</span>';
        else statusBadge = `<span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-black rounded-full">${order.status}</span>`;

        card.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-black text-[#800000] uppercase truncate w-2/3">${formatCardName(order.customer_name)}</h3>
                <span class="text-[10px] font-bold text-gray-400">#${order.pre_order_code}</span>
            </div>
            <div class="flex justify-between items-end mt-4">
                <div>
                   <p class="text-xs font-bold text-gray-500">${itemsCount} • ${time}</p>
                   <div class="mt-2 text-[10px] font-black uppercase tracking-widest">${statusBadge}</div>
                </div>
                <p class="font-black text-maroon">${formatCurrency(order.total_amount)}</p>
            </div>
        `;
        grid.appendChild(card);
    });
}

function selectOrder(order) {
    currentSelectedOrderId = order.order_id;
    currentSelectedOrderStatus = order.status;
    currentCart = order.order_items || [];
    renderAllOrders();

    document.getElementById('selected-order-details').innerHTML = `
        <h3 class="font-bold text-gray-800 text-lg">Order Details</h3>
        <div class="mt-4">
            <h2 class="text-xl font-black text-[#800000] uppercase">${formatCardName(order.customer_name)}</h2>
            <p class="text-xs text-gray-500 font-bold font-mono">Order #${order.pre_order_code} • ${order.status}</p>
        </div>
    `;

    const cartList = document.getElementById('cart-list');
    cartList.innerHTML = currentCart.map(item => `
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <p class="font-bold text-gray-800">${cleanName(item.name)} <span class="text-xs text-gray-400">×${item.quantity}</span></p>
            ${item.modifiers && item.modifiers !== '[]' ? `<p class="text-[10px] text-gray-500">+ ${item.modifiers}</p>` : ''}
        </div>
    `).join('');

    // Enable/Disable buttons based on current status
    // PREPARING is allowed if Pending Payment or Preparing (to re-confirm)
    document.getElementById('status-preparing-btn').disabled = (order.status === 'READY' || order.status === 'SERVED');
    // READY is allowed if Preparing or Pending Payment
    document.getElementById('status-ready-btn').disabled = (order.status === 'READY' || order.status === 'SERVED');
    // SERVED is only allowed if READY
    document.getElementById('status-served-btn').disabled = (order.status !== 'READY');
    document.getElementById('status-cancel-btn').disabled = false;
}

async function updateSelectedStatus(newStatus) {
    if (!currentSelectedOrderId) return;

    // If cancelling, show prompt
    if (newStatus === 'CANCELLED') {
        showModal('Cancel Order', 'Are you sure you want to cancel this order?', [
            { text: 'No', onclick: 'closeModal()' },
            { text: 'Yes, Cancel', onclick: () => confirmStatusUpdate('CANCELLED'), style: 'bg-red-600 text-white' }
        ]);
    } else {
        confirmStatusUpdate(newStatus);
    }
}

async function confirmStatusUpdate(newStatus) {
    closeModal();
    try {
        const response = await fetch(`${API_BASE}/update_order_status.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: currentSelectedOrderId, status: newStatus })
        });
        if (!response.ok) throw new Error();

        showAlert(`Order marked as ${newStatus}`, 'success');
        currentSelectedOrderId = null;
        document.getElementById('selected-order-details').innerHTML = `<h3 class="font-bold text-gray-800 text-lg">Order Details</h3><p class="text-xs text-gray-500 font-mono">No Selection</p>`;
        document.getElementById('cart-list').innerHTML = '<div class="h-full flex flex-col items-center justify-center text-gray-300"><span class="text-gray-300 mb-2"><i class="ph-duotone ph-arrow-left" style="font-size:40px"></i></span><p class="text-sm text-center px-4">Select an order to update its status.</p></div>';

        // Disable buttons
        ['preparing', 'ready', 'served', 'cancel'].forEach(b => document.getElementById(`status-${b}-btn`).disabled = true);

        fetchAllOrders();
        // If served, refresh history if visible
        const activeNav = document.querySelector('.sidebar-link.active');
        if (activeNav && activeNav.id === 'nav-history') loadHistory();
    } catch (err) { showAlert('Failed to update status', 'error'); }
}

// --- INVENTORY & HISTORY (Robust implementation) ---
async function loadInventoryView() {
    try {
        const catRes = await fetch(`${API_BASE}/get_categories.php`);
        const catData = await catRes.json();
        const categoriesMap = {};
        const categoriesIconMap = {};
        if (catData.categories) {
            catData.categories.forEach(c => {
                categoriesMap[c.category_id] = c.name;
                categoriesIconMap[c.category_id] = c.icon;
            });
        }

        const menuRes = await fetch(`${API_BASE}/get_menu_items.php?include_hidden=1`);
        const menuData = await menuRes.json();
        if (menuData.items) {
            inventoryMenu = menuData.items.map(i => {
                const catName = ([1, 2].includes(Number(i.category_id)) ? 'Coffee' : (categoriesMap[i.category_id] || 'Other'));
                return {
                    ...i,
                    category: catName,
                    icon: categoriesIconMap[i.category_id] || '<i class="ph-duotone ph-fork-knife"></i>'
                };
            });

            const cats = [...new Set(inventoryMenu.map(i => i.category))];
            if (!cats.includes(activeInventoryCategory) && cats.length > 0) activeInventoryCategory = cats[0];

            renderInventoryCategories();
            renderInventoryMenu();
        }
    } catch (e) { showAlert('Failed to load inventory', 'error'); }
}

function renderInventoryCategories() {
    const container = document.getElementById('inventory-categories');
    const categories = [...new Set(inventoryMenu.map(i => i.category))];
    container.innerHTML = categories.map(cat => `
        <button onclick="switchInventoryCategory('${cat}')" class="flex flex-col items-center justify-center p-2 rounded-xl border-2 w-24 h-20 flex-shrink-0 ${cat === activeInventoryCategory ? 'bg-maroon text-white border-maroon' : 'bg-white border-gray-100'}">
            <span class="text-2xl mb-1">${inventoryMenu.find(c => c.category === cat)?.icon || '<i class="ph-duotone ph-fork-knife"></i>'}</span>
            <span class="text-[9px] font-bold uppercase">${cat}</span>
        </button>`).join('');
}

function switchInventoryCategory(cat) {
    activeInventoryCategory = cat;
    renderInventoryCategories();
    renderInventoryMenu();
}

function renderInventoryMenu() {
    const grid = document.getElementById('inventory-menu-grid');
    if (!grid) return;

    if (viewMode === 'list') {
        grid.className = "flex flex-col gap-2 overflow-y-auto pr-2 custom-scroll flex-1";
    } else {
        grid.className = "grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-6 overflow-y-auto pr-2 custom-scroll flex-1";
    }

    const filtered = inventoryMenu.filter(i => i.category === activeInventoryCategory);

    if (viewMode === 'grid') {
        grid.innerHTML = filtered.map(item => `
            <div onclick="toggleAvailability(${item.item_id}, ${!item.is_available})" class="relative h-fit group cursor-pointer bg-white rounded-xl shadow-sm border-2 transition-all hover:shadow-md ${item.is_available ? 'border-transparent' : 'border-red-200 opacity-75'}">
                <div class="p-4 flex flex-col items-center">
                    <div class="text-4xl mb-2">${item.icon}</div>
                    <p class="text-sm font-bold text-gray-800 text-center line-clamp-2 h-10">${cleanName(item.name)}</p>
                    <div class="mt-2 text-[10px] font-black px-2 py-1 rounded-full w-full text-center ${item.is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                        ${item.is_available ? 'AVAILABLE' : 'UNAVAILABLE'}
                    </div>
                </div>
                ${!item.is_available ? '<div class="absolute inset-0 bg-red-50 bg-opacity-20 flex items-center justify-center rounded-xl"></div>' : ''}
            </div>
        `).join('');
    } else {
        grid.innerHTML = filtered.map(item => `
            <div onclick="toggleAvailability(${item.item_id}, ${!item.is_available})" class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border-2 transition-all hover:shadow-md cursor-pointer ${item.is_available ? 'border-transparent' : 'border-red-200 opacity-75'}">
                <div class="flex items-center gap-4">
                    <div class="text-2xl">${item.icon}</div>
                    <h5 class="font-bold text-gray-800">${cleanName(item.name)}</h5>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest ${item.is_available ? 'text-green-600' : 'text-red-600'}">
                        ${item.is_available ? 'Active' : 'Inactive'}
                    </span>
                    <div class="w-12 h-6 rounded-full relative transition-all ${item.is_available ? 'bg-green-500' : 'bg-gray-300'}">
                        <div class="absolute w-4 h-4 bg-white rounded-full top-1 transition-all ${item.is_available ? 'right-1' : 'left-1'}"></div>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

async function toggleAvailability(id, status) {
    try {
        const res = await fetch(`${API_BASE}/toggle_menu_availability.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: id, is_available: status })
        });
        const data = await res.json();
        if (data.success) {
            showAlert(`Item ${status ? 'available' : 'unavailable'}`, 'success');
            loadInventoryView();
        }
    } catch (e) { showAlert('Failed to update availability', 'error'); }
}

// History
async function loadHistory() {
    try {
        const res = await fetch(`${API_BASE}/get_orders.php?view=history`);
        const data = await res.json();
        allHistoryOrders = data.orders || [];
        filterHistory();
    } catch (e) { showAlert('Failed to load history', 'error'); }
}

function filterHistory() {
    const dateFrom = document.getElementById('history-date-from').value;
    const dateTo = document.getElementById('history-date-to').value;
    const status = document.getElementById('history-status').value;
    const search = document.getElementById('history-search').value.toLowerCase();

    filteredHistoryOrders = allHistoryOrders.filter(o => {
        if (dateFrom) {
            const d = new Date(o.time_placed);
            const orderDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            if (orderDate < dateFrom) return false;
        }

        if (dateTo) {
            const d = new Date(o.time_placed);
            const orderDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            if (orderDate > dateTo) return false;
        }
        if (status && o.status !== status) return false;
        if (search) {
            const order_items = Array.isArray(o.order_items) ? o.order_items : [];
            const matchItems = order_items.some(i => (i.name || '').toLowerCase().includes(search));
            const matchName = o.customer_name && o.customer_name.toLowerCase().includes(search);
            const matchCode = o.pre_order_code && o.pre_order_code.toLowerCase().includes(search);
            const matchFinalCode = o.final_code && o.final_code.toLowerCase().includes(search);
            if (!matchItems && !matchName && !matchCode && !matchFinalCode) return false;
        }
        return true;
    });

    currentHistoryPage = 1;
    renderHistoryPage();
}

function renderHistoryPage() {
    const start = (currentHistoryPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageOrders = filteredHistoryOrders.slice(start, end);
    const tbody = document.getElementById('history-table-body');

    tbody.innerHTML = pageOrders.length === 0
        ? '<tr><td colspan="6" class="p-8 text-center text-gray-400">No orders found in history</td></tr>'
        : pageOrders.map(h => {
            const items = h.order_items.map(i => `${cleanName(i.name)}×${i.quantity}`).join(', ');
            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-800">#${h.pre_order_code}</td>
                    <td class="p-4 text-xs text-gray-500">${new Date(h.time_placed).toLocaleString()}</td>
                    <td class="p-4 text-[10px] font-black uppercase text-maroon">${h.order_source}</td>
                    <td class="p-4 text-xs text-gray-600 max-w-xs truncate">${items}</td>
                    <td class="p-4 font-black text-gray-800">${formatCurrency(h.total_amount)}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-[9px] font-black ${h.status === 'SERVED' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                            ${h.status}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');

    updatePagination();
}

function updatePagination() {
    const totalPages = Math.ceil(filteredHistoryOrders.length / itemsPerPage);
    document.getElementById('prev-btn').disabled = currentHistoryPage === 1;
    document.getElementById('next-btn').disabled = currentHistoryPage >= totalPages;

    const numbers = document.getElementById('pagination-numbers');
    numbers.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        if (i > 5 && i < totalPages) { // Simple ellipsis logic
            if (i === 6) numbers.innerHTML += '<span class="px-2">...</span>';
            continue;
        }
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded-lg font-bold text-sm ${i === currentHistoryPage ? 'bg-maroon text-white' : 'bg-white border border-gray-200 text-gray-600'}`;
        btn.onclick = () => { currentHistoryPage = i; renderHistoryPage(); };
        numbers.appendChild(btn);
    }
}

function resetFilter() {
    setDefaultDateRange('history-date-from', 'history-date-to');
    document.getElementById('history-status').value = '';
    document.getElementById('history-search').value = '';
    filterHistory();
}

function previousPage() { if (currentHistoryPage > 1) { currentHistoryPage--; renderHistoryPage(); } }
function nextPage() { if (currentHistoryPage < Math.ceil(filteredHistoryOrders.length / itemsPerPage)) { currentHistoryPage++; renderHistoryPage(); } }

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    fetchAllOrders();
    // Auto-refresh queue every 5 seconds
    setInterval(fetchAllOrders, 5000);
});
