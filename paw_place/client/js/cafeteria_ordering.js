/**
 * Cafeteria Ordering - JavaScript
 * Handles category loading, item display, and cart management
 * for non-Paws Place stores via the cafeteria API.
 */

let cart = [];
let categories = [];
let currentCategory = null;

const CATEGORY_ICONS = {
    'Coffee': '<i class="ph-duotone ph-coffee"></i>',
    'Milktea': '<i class="ph-duotone ph-coffee"></i>',
    'Milk Tea': '<i class="ph-duotone ph-coffee"></i>',
    'Fruity Soda': '<svg viewBox="0 0 256 256" style="width:1em;height:1em;display:inline-block;vertical-align:middle;"><path d="M192,104H64a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8H192a8,8,0,0,1,8,8V96A8,8,0,0,1,192,104Z" fill="currentColor" opacity="0.2"/><path d="M192,104H64a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8H192a8,8,0,0,1,8,8V96A8,8,0,0,1,192,104Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M72,104l16,112.5a16.2,16.2,0,0,0,16,13.8h48a16.2,16.2,0,0,0,16-13.8L184,104" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M144,72V56a8,8,0,0,1,8-8h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>',
    'Fruity': '<i class="ph-duotone ph-orange-slice"></i>',
    'Specialty': '<i class="ph-duotone ph-star"></i>',
    'Add Ons': '<i class="ph-duotone ph-plus-circle"></i>',
    'Ice Cream': '<i class="ph-duotone ph-ice-cream"></i>',
    'Ice Cream in Cups': '<i class="ph-duotone ph-bowl-food"></i>',
    'Ice Cream Bar': '<i class="ph-duotone ph-popsicle"></i>',
    'Milk Drink': '<i class="ph-duotone ph-beer-bottle"></i>',
    'Drinks': '<i class="ph-duotone ph-drop"></i>',
    'Snacks': '<i class="ph-duotone ph-cookie"></i>',
    'Bread': '<i class="ph-duotone ph-bread"></i>',
    'Food': '<i class="ph-duotone ph-hamburger"></i>',
    'Candy': '<i class="ph-duotone ph-cookie"></i>',
    'Fruit': '<i class="ph-duotone ph-apple-podcasts-logo"></i>',
    'Vending': '<i class="ph-duotone ph-vibrate"></i>',
    'Default': '<i class="ph-duotone ph-fork-knife"></i>'
};

function getIconForCategoryName(name) {
    const n = (name || '').toLowerCase();
    if (!n) return CATEGORY_ICONS['Default'];
    if (n.includes('coffee')) return CATEGORY_ICONS['Coffee'] || '<i class="ph-duotone ph-coffee"></i>';
    if (n.includes('milk tea') || n.includes('milktea')) return CATEGORY_ICONS['Milk Tea'] || '<i class="ph-duotone ph-coffee"></i>';
    if (n.includes('ice cream')) return CATEGORY_ICONS['Ice Cream'] || '<i class="ph-duotone ph-ice-cream"></i>';
    if (n.includes('bread') || n.includes('pastry')) return CATEGORY_ICONS['Bread'] || '<i class="ph-duotone ph-bread"></i>';
    if (n.includes('candy') || n.includes('sweet')) return CATEGORY_ICONS['Candy'] || '<i class="ph-duotone ph-cookie"></i>';
    if (n.includes('fruit')) return CATEGORY_ICONS['Fruit'] || '<i class="ph-duotone ph-apple"></i>';
    if (n.includes('snack')) return CATEGORY_ICONS['Snacks'] || '<i class="ph-duotone ph-cookie"></i>';
    if (n.includes('food') || n.includes('meal')) return CATEGORY_ICONS['Food'] || '<i class="ph-duotone ph-fork-knife"></i>';
    if (n.includes('drink')) return CATEGORY_ICONS['Drinks'] || '<i class="ph-duotone ph-drop"></i>';
    return CATEGORY_ICONS['Default'];
}

// ===== INITIALIZATION =====

document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
});

function goBack() {
    window.location.href = 'store_selection.php';
}

// ===== CATEGORIES =====

async function loadCategories() {
    const container = document.getElementById('category-filter');
    container.innerHTML = '<div class="loading-spinner"></div>';

    try {
        let url = '../server/api/get_cafeteria_categories.php';
        if (LOCATION_ID) {
            url += `?location_id=${encodeURIComponent(LOCATION_ID)}`;
        }
        const res = await fetch(url);
        const data = await res.json();
        console.log('Categories response:', data);

        if (data.success && Array.isArray(data.categories)) {
            categories = data.categories;
        } else if (data.success && data.categories) {
            // Maybe it's wrapped differently
            categories = Object.values(data.categories);
        } else {
            container.innerHTML = '<p class="text-sm text-red-500">Failed to load categories</p>';
            return;
        }

        if (categories.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-500">No categories available</p>';
            return;
        }

        renderCategories();
        // Select first category by default
        if (categories.length > 0) {
            selectCategory(categories[0]);
        } else {
            container.innerHTML = '<p class="text-sm text-gray-500">No categories available</p>';
        }
    } catch (err) {
        console.error('Error loading categories:', err);
        container.innerHTML = '<p class="text-sm text-red-500">Failed to connect to server</p>';
    }
}

function renderCategories() {
    const container = document.getElementById('category-filter');
    container.innerHTML = '';

    categories.forEach(cat => {
        const name = typeof cat === 'string' ? cat : (cat.category || cat.name || cat.category_name || '');
        const icon = getIconForCategoryName(name);
        const btn = document.createElement('button');
        btn.className = 'category-btn';
        btn.innerHTML = `
            <span class="category-icon">${icon}</span>
            <span class="category-name">${name}</span>
        `;
        btn.onclick = () => selectCategory(cat);
        btn.dataset.category = name;
        container.appendChild(btn);
    });
}

function selectCategory(cat) {
    const name = typeof cat === 'string' ? cat : (cat.category || cat.name || cat.category_name || '');
    currentCategory = name;

    // Update active state
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.category === currentCategory);
    });

    loadItems(currentCategory);
}

// ===== ITEMS =====

async function loadItems(category) {
    const container = document.getElementById('menu-items-container');
    container.innerHTML = '<div class="loading-spinner">Loading items...</div>';

    try {
        let url = `../server/api/get_cafeteria_items.php?category=${encodeURIComponent(category)}`;
        if (LOCATION_ID) {
            url += `&location_id=${encodeURIComponent(LOCATION_ID)}`;
        }

        const res = await fetch(url);
        const data = await res.json();
        console.log('Items response:', data);

        let items = [];
        if (data.success && Array.isArray(data.items)) {
            items = data.items;
        } else if (data.success && data.items) {
            items = Object.values(data.items);
        }

        if (items.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="icon"><i class="ph-duotone ph-inbox" style="font-size:48px"></i></span>
                    <p>No items in this category</p>
                </div>`;
            return;
        }

        renderItems(items);
    } catch (err) {
        console.error('Error loading items:', err);
        container.innerHTML = `
            <div class="empty-state">
                <span class="icon"><i class="ph-duotone ph-warning" style="font-size:48px"></i></span>
                <p>Failed to load items</p>
            </div>`;
    }
}

function renderItems(items) {
    const container = document.getElementById('menu-items-container');
    container.innerHTML = '';

    const grid = document.createElement('div');
    grid.className = 'menu-grid';

    items.forEach(item => {
        const id = item.id || item.item_id || '';
        const name = item.name || item.item_name || 'Unknown Item';
        const price = parseFloat(item.price || item.item_price || 0);
        const available = item.availability !== false && item.availability !== 0 && item.status !== 'unavailable';
        const image = item.image || item.image_url || item.item_image || null;

        const card = document.createElement('div');
        card.className = `menu-card ${available ? '' : 'unavailable'}`;

        if (available) {
            card.onclick = () => addToCart({ id, name, price, image });
        }

        card.innerHTML = `
            ${image
                ? `<img src="${image}" alt="${name}" class="menu-card-image" onerror="this.parentElement.querySelector('.menu-card-image').style.display='none'; this.parentElement.querySelector('.menu-card-placeholder-fallback').style.display='flex';">`
                : ''}
            <div class="menu-card-placeholder ${image ? 'menu-card-placeholder-fallback' : ''}" style="${image ? 'display:none;' : ''}"><i class="ph-duotone ph-fork-knife" style="font-size:40px;color:#9ca3af"></i></div>
            <div class="menu-card-name">${escHtml(name)}</div>
            <div class="menu-card-price">₱${price.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            ${available
                ? '<span class="menu-card-status available">Available</span>'
                : '<span class="menu-card-status unavailable">Sold Out</span>'}
        `;

        grid.appendChild(card);
    });

    container.appendChild(grid);
}

// ===== CART =====

function addToCart(item) {
    const existing = cart.find(c => c.id === item.id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ ...item, qty: 1 });
    }
    renderCart();
    showAlert(`Added ${item.name}`, 'success');
}

function updateCartQty(itemId, delta) {
    const item = cart.find(c => c.id === itemId);
    if (!item) return;

    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(c => c.id !== itemId);
    }
    renderCart();
}

function removeFromCart(itemId) {
    cart = cart.filter(c => c.id !== itemId);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-list');
    const countEl = document.getElementById('cart-count');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total');
    const orderBtn = document.getElementById('place-order-btn');

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <span class="text-gray-300 mb-2"><i class="ph-duotone ph-shopping-cart" style="font-size:40px"></i></span>
                <p class="text-sm">Your tray is empty</p>
            </div>`;
        countEl.textContent = '0 items';
        subtotalEl.textContent = '₱0.00';
        totalEl.textContent = '₱0.00';
        orderBtn.disabled = true;
        return;
    }

    const totalItems = cart.reduce((sum, c) => sum + c.qty, 0);
    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);

    countEl.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;

    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-info">
                <div class="cart-item-name">${escHtml(item.name)}</div>
                <div class="cart-item-price">₱${(item.price * item.qty).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            <div class="cart-item-controls">
                ${item.qty === 1
            ? `<button class="cart-qty-btn remove" onclick="removeFromCart('${item.id}')">✕</button>`
            : `<button class="cart-qty-btn" onclick="updateCartQty('${item.id}', -1)">−</button>`
        }
                <span class="cart-qty">${item.qty}</span>
                <button class="cart-qty-btn" onclick="updateCartQty('${item.id}', 1)">+</button>
            </div>
        </div>
    `).join('');

    subtotalEl.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
    totalEl.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
    orderBtn.disabled = false;
}

// ===== ORDER =====

function promptConfirmOrder() {
    if (cart.length === 0) return;

    const modal = document.getElementById('modal-container');
    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);

    const itemsList = cart.map(item =>
        `<div class="flex justify-between text-sm py-1">
            <span>${escHtml(item.name)} × ${item.qty}</span>
            <span class="font-semibold">₱${(item.price * item.qty).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
        </div>`
    ).join('');

    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-black text-gray-800 mb-4">Confirm Order</h3>
            <div class="border-t border-b border-gray-200 py-3 mb-4 max-h-48 overflow-y-auto">
                ${itemsList}
            </div>
            <div class="flex justify-between text-2xl font-black text-[#800000] mb-6">
                <span>TOTAL</span>
                <span>₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
            </div>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                <button onclick="placeOrder()" class="flex-1 py-3 bg-[#800000] text-white rounded-xl font-bold hover:bg-red-900 transition">Confirm</button>
            </div>
        </div>
    `;
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-container').classList.add('hidden');
}

async function placeOrder() {
    closeModal();
    const orderBtn = document.getElementById('place-order-btn');
    orderBtn.disabled = true;
    orderBtn.textContent = 'PLACING ORDER...';

    try {
        // Build order data
        const orderData = {
            store: STORE_NAME,
            location_id: LOCATION_ID,
            customer_name: CURRENT_USER_NAME,
            customer_id: CURRENT_USER_ID,
            items: cart.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.qty
            })),
            total: cart.reduce((sum, c) => sum + (c.price * c.qty), 0)
        };

        // For now, show success since there's no cafeteria order API yet
        // TODO: Replace with actual order API when available
        console.log('Order data:', orderData);

        showAlert('Order placed successfully!', 'success');
        cart = [];
        renderCart();
        orderBtn.textContent = 'PLACE ORDER';

    } catch (err) {
        console.error('Order error:', err);
        showAlert('Failed to place order. Please try again.', 'error');
        orderBtn.disabled = false;
        orderBtn.textContent = 'PLACE ORDER';
    }
}

// ===== UTILITIES =====

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    if (!container) return;
    const colors = { info: 'bg-blue-500', success: 'bg-green-600', error: 'bg-red-600' };
    const alert = document.createElement('div');
    alert.className = `${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-lg mb-2`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 2500);
}
