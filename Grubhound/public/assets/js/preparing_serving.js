// ========================================
// KITCHEN DISPLAY - preparing_serving.js
// Auto-refreshing split-screen display
// ========================================

const API_BASE = BASE_URL + 'api';
const REFRESH_INTERVAL = 3000; // 3 seconds

// --- Name Formatting ---
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

// --- Escape HTML ---
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// --- Live Clock ---
function updateClock() {
    const el = document.getElementById('live-clock');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

// --- Build Order Card ---
function createOrderCard(order, type) {
    const orderCode = order.pre_order_code || '???';
    const timeStr = new Date(order.time_placed).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const source = (order.order_source || 'N/A').toUpperCase();
    const sourceClass = source.includes('KIOSK') ? 'source-kiosk' : 'source-pos';

    let itemsHTML = '';
    if (order.order_items && order.order_items.length > 0) {
        itemsHTML = order.order_items.map(item => `
            <div class="order-item-row">
                <span class="item-name">${escapeHtml(item.name)}</span>
                <span class="item-qty">×${item.quantity}</span>
            </div>
            ${item.modifiers && item.modifiers.trim() && item.modifiers.trim() !== '[]' && item.modifiers.trim() !== '[ ]' ? `<div class="item-modifiers">+ ${escapeHtml(item.modifiers)}</div>` : ''}
        `).join('');
    }

    const card = document.createElement('div');
    card.className = `order-card ${type}`;
    card.innerHTML = `
        <div class="order-code">#${escapeHtml(orderCode)}</div>
        <div class="customer-name">${escapeHtml(formatCardName(order.customer_name))}</div>
    `;
    return card;
}

// --- Render Section ---
function renderSection(containerId, countId, orders, type) {
    const container = document.getElementById(containerId);
    const countEl = document.getElementById(countId);

    if (!container) return;

    countEl.textContent = orders.length;

    if (orders.length === 0) {
        const emptyIcon = type === 'preparing' ? '🍳' : '🎉';
        const emptyText = type === 'preparing' ? 'No orders to prepare' : 'No orders ready';
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">${emptyIcon}</div>
                <div class="empty-text">${emptyText}</div>
            </div>
        `;
        return;
    }

    container.innerHTML = '';
    orders.forEach(order => {
        container.appendChild(createOrderCard(order, type));
    });
}

// --- Fetch & Refresh ---
let lastPreparingKey = '';
let lastReadyKey = '';

async function fetchAndRender() {
    try {
        // Fetch preparing orders
        const prepRes = await fetch(`${API_BASE}/orders?status=PREPARING`);
        const prepData = await prepRes.json();
        const preparingOrders = prepData.orders || [];

        // Fetch ready orders
        const readyRes = await fetch(`${API_BASE}/orders?status=READY`);
        const readyData = await readyRes.json();
        const readyOrders = readyData.orders || [];

        // Smart comparison to avoid unnecessary DOM rebuilds
        const newPrepKey = preparingOrders.map(o => `${o.order_id}:${o.status}`).join('|');
        const newReadyKey = readyOrders.map(o => `${o.order_id}:${o.status}`).join('|');

        if (newPrepKey !== lastPreparingKey) {
            renderSection('preparing-orders', 'preparing-count', preparingOrders, 'preparing');
            lastPreparingKey = newPrepKey;
        }

        if (newReadyKey !== lastReadyKey) {
            renderSection('serving-orders', 'serving-count', readyOrders, 'ready');
            lastReadyKey = newReadyKey;
        }

    } catch (error) {
        console.error('Kitchen Display fetch error:', error);
    }
}

// --- Init ---
fetchAndRender();
setInterval(fetchAndRender, REFRESH_INTERVAL);
