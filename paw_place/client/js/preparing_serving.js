// Preparing and Serving Screen JavaScript
// Optimized for Premium Kitchen Display

let refreshInterval;
const API_BASE = '../server/api';

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    loadOrders();
    // Auto-refresh every 5 seconds (Fast Polling)
    refreshInterval = setInterval(loadOrders, 5000);
});

// Load orders from API
async function loadOrders() {
    try {
        // Fetch orders from get_orders.php
        // We fetch all and filter client-side for better performance and fewer requests
        const response = await fetch(`${API_BASE}/get_orders.php`);
        const data = await response.json();

        if (data.success && data.orders) {
            const preparing = data.orders.filter(o => o.status === 'PREPARING');
            const serving = data.orders.filter(o => o.status === 'READY');

            updateSection(preparing, 'preparing-orders', 'preparing-count');
            updateSection(serving, 'serving-orders', 'serving-count');
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Update a specific section (Preparing or Serving)
function updateSection(orders, containerId, countId) {
    const container = document.getElementById(containerId);
    const countElement = document.getElementById(countId);

    // Update count
    countElement.textContent = orders.length;

    // If no orders, show empty state
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">☕</div>
            </div>
        `;
        return;
    }

    // Update container content
    // We compare with current state to avoid flickering if possible
    const currentIds = Array.from(container.querySelectorAll('.order-card')).map(c => c.dataset.id);
    const newIds = orders.map(o => String(o.order_id));

    if (JSON.stringify(currentIds) === JSON.stringify(newIds)) return;

    container.innerHTML = '';
    orders.forEach(order => {
        const card = createOrderCard(order);
        container.appendChild(card);
    });
}

// Create a premium order card
function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'order-card';
    card.dataset.id = order.order_id;

    const orderCode = order.final_code || order.pre_order_code || 'N/A';
    const timeStr = formatTime(order.time_paid || order.time_placed);

    // Items list
    let itemsHTML = '';
    if (order.order_items && order.order_items.length > 0) {
        itemsHTML = order.order_items.map(item => `
            <div class="order-item">
                <span class="item-name">${escapeHtml(item.name)}</span>
                <span class="item-quantity">×${item.quantity}</span>
            </div>
        `).join('');
    }

    const sourceClass = order.order_source === 'KIOSK' ? 'source-kiosk' : 'source-cashier';

    card.innerHTML = `
        <div class="order-header">
            <div class="order-code">#${escapeHtml(orderCode)}</div>
            <div class="order-time">${timeStr}</div>
        </div>
        <div class="customer-name">${escapeHtml(order.customer_name || 'Walk-in Customer')}</div>
        <div class="order-items">
            ${itemsHTML}
        </div>
        <div class="order-footer">
            <div class="order-total">₱${parseFloat(order.total_amount).toFixed(2)}</div>
            <div class="order-source ${sourceClass}">${escapeHtml(order.order_source || 'N/A')}</div>
        </div>
    `;

    return card;
}

// Human-readable time
function formatTime(timeString) {
    if (!timeString) return 'Just now';
    const orderTime = new Date(timeString);
    const now = new Date();
    const diffMs = now - orderTime;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    return orderTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.addEventListener('beforeunload', () => {
    if (refreshInterval) clearInterval(refreshInterval);
});
