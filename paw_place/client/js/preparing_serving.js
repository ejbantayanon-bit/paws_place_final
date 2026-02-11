// Preparing and Serving Screen JavaScript

let refreshInterval;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
    // Auto-refresh every 5 seconds
    refreshInterval = setInterval(loadOrders, 5000);
});

// Load orders from API
async function loadOrders() {
    try {
        // Fetch preparing orders (status = PREPARING)
        const preparingResponse = await fetch('../server/api/get_orders.php?status=PREPARING');
        const preparingData = await preparingResponse.json();
        
        // Fetch ready/serving orders (status = READY)
        const servingResponse = await fetch('../server/api/get_orders.php?status=READY');
        const servingData = await servingResponse.json();
        
        if (preparingData.success) {
            displayOrders(preparingData.orders, 'preparing-orders', 'preparing-count');
        }
        
        if (servingData.success) {
            displayOrders(servingData.orders, 'serving-orders', 'serving-count');
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Display orders in the specified container
function displayOrders(orders, containerId, countId) {
    const container = document.getElementById(containerId);
    const countElement = document.getElementById(countId);
    
    // Update count
    countElement.textContent = orders.length;
    
    // Clear container
    container.innerHTML = '';
    
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div>No orders at the moment</div>
            </div>
        `;
        return;
    }
    
    // Display each order
    orders.forEach(order => {
        const orderCard = createOrderCard(order);
        container.appendChild(orderCard);
    });
}

// Create an order card element
function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'order-card';
    
    // Determine order code (use final_code if available, otherwise pre_order_code)
    const orderCode = order.final_code || order.pre_order_code || 'N/A';
    
    // Format time
    const timeStr = formatTime(order.time_paid || order.time_placed);
    
    // Create items HTML
    let itemsHTML = '';
    if (order.order_items && order.order_items.length > 0) {
        itemsHTML = order.order_items.map(item => `
            <div class="order-item">
                <span class="item-name">${escapeHtml(item.name || 'Unknown Item')}</span>
                <span class="item-quantity">×${item.quantity}</span>
            </div>
        `).join('');
    }
    
    // Determine source class
    const sourceClass = order.order_source === 'KIOSK' ? 'source-kiosk' : 'source-cashier';
    
    card.innerHTML = `
        <div class="order-header">
            <div class="order-code">#${escapeHtml(orderCode)}</div>
            <div class="order-time">${timeStr}</div>
        </div>
        <div class="customer-name">👤 ${escapeHtml(order.customer_name || 'Guest')}</div>
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

// Format time to relative or absolute
function formatTime(timeString) {
    if (!timeString) return 'N/A';
    
    const orderTime = new Date(timeString);
    const now = new Date();
    const diffMs = now - orderTime;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) {
        return 'Just now';
    } else if (diffMins < 60) {
        return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
    } else {
        const hours = orderTime.getHours().toString().padStart(2, '0');
        const minutes = orderTime.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
