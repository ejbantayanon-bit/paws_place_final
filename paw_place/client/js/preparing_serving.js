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

// Pagination State
const PAGINATION = {
    preparing: { page: 0, interval: null, itemsPerPage: 12 }, // Grid view
    serving: { page: 0, interval: null, itemsPerPage: 12 }    // Grid view
};

// Cycle pages every 10 seconds
const PAGE_CYCLE_DURATION = 10000;

// Load orders from API
async function loadOrders() {
    try {
        // Fetch orders from get_orders.php
        const response = await fetch(`${API_BASE}/get_orders.php`);
        const data = await response.json();

        if (data.success && data.orders) {
            // Update globals
            currentPreparingOrders = data.orders.filter(o => o.status === 'PREPARING');
            currentServingOrders = data.orders.filter(o => o.status === 'READY');

            // Initial Render (or maintain current page if valid)
            updateSection(currentPreparingOrders, 'preparing-orders', 'preparing-count', 'preparing');
            updateSection(currentServingOrders, 'serving-orders', 'serving-count', 'serving');
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Global State Storage for Cycler
let currentPreparingOrders = [];
let currentServingOrders = [];

// Update a specific section with pagination
function updateSection(orders, containerId, countId, type) {
    const container = document.getElementById(containerId);
    const countElement = document.getElementById(countId);

    // Update total count
    if (countElement) countElement.textContent = orders.length;

    // Handle empty state
    if (orders.length === 0) {
        container.innerHTML = `<div class="empty-state"><div class="empty-state-icon">☕</div></div>`;
        return;
    }

    // Determine pagination config
    const config = PAGINATION[type];
    const totalPages = Math.ceil(orders.length / config.itemsPerPage);

    // Reset loop if data changed cleanly
    if (config.page >= totalPages) config.page = 0;

    // Slice data for current page
    const start = config.page * config.itemsPerPage;
    const end = start + config.itemsPerPage;
    const visibleOrders = orders.slice(start, end);

    // Render items
    renderItems(container, visibleOrders);

    // Add page indicator if multiple pages
    if (totalPages > 1) {
        addPageIndicator(container, config.page + 1, totalPages);
    }
}

function renderItems(container, orders) {
    container.innerHTML = '';
    orders.forEach(order => {
        container.appendChild(createOrderCard(order));
    });
}

function addPageIndicator(container, current, total) {
    const indicator = document.createElement('div');
    indicator.className = 'page-indicator';
    indicator.style.cssText = 'position: absolute; bottom: 15px; right: 15px; padding: 6px 12px; border-radius: 8px; font-size: 14px; z-index: 10;';
    indicator.textContent = `PAGE ${current} / ${total}`;
    container.appendChild(indicator);
}

// Clock Logic
function updateClock() {
    const clock = document.getElementById('live-clock');
    if (clock) {
        const now = new Date();
        clock.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}
setInterval(updateClock, 1000);
updateClock();

// Global cycler to advance visible pages
setInterval(() => {
    if (currentPreparingOrders.length > PAGINATION.preparing.itemsPerPage) {
        PAGINATION.preparing.page = (PAGINATION.preparing.page + 1) % Math.ceil(currentPreparingOrders.length / PAGINATION.preparing.itemsPerPage);
        updateSection(currentPreparingOrders, 'preparing-orders', 'preparing-count', 'preparing');
    }
    if (currentServingOrders.length > PAGINATION.serving.itemsPerPage) {
        PAGINATION.serving.page = (PAGINATION.serving.page + 1) % Math.ceil(currentServingOrders.length / PAGINATION.serving.itemsPerPage);
        updateSection(currentServingOrders, 'serving-orders', 'serving-count', 'serving');
    }
}, PAGE_CYCLE_DURATION);


// Create a premium order card
// Create a premium order card
function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'order-card';
    card.dataset.id = order.order_id;

    const orderCode = order.final_code || order.pre_order_code || 'N/A';

    // Calculate time ago
    const startTime = new Date(order.time_paid || order.time_placed);
    const timeAgo = getTimeAgo(startTime);

    const formattedName = formatCustomerName(order.customer_name);

    // Simplified Card: Order # + Name only
    card.innerHTML = `
        <div class="card-content">
            <div class="order-header">
                <div class="order-code">#${escapeHtml(orderCode)}</div>
                <div class="timer-badge">${timeAgo}</div>
            </div>
            
            <div class="customer-name">${escapeHtml(formattedName)}</div>
        </div>
    `;

    return card;
}

// Format Name: "Firstname Lastname" -> "F. LASTNAME"
function formatCustomerName(fullname) {
    if (!fullname || fullname.trim() === '') return 'GUEST';

    const parts = fullname.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].toUpperCase();

    // Last word is surname, everything before are first/middle names
    const surname = parts.pop().toUpperCase();

    // Get initials of all first names
    const initials = parts.map(n => n.charAt(0).toUpperCase() + '.').join(' ');

    return `${initials} ${surname}`;
}

// Get Time Ago (e.g. "5m ago")
function getTimeAgo(timeString) {
    if (!timeString) return 'Just now';
    const diff = new Date() - new Date(timeString);
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'Just now';
    if (mins > 60) return Math.floor(mins / 60) + 'h ago';
    return mins + 'm ago';
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