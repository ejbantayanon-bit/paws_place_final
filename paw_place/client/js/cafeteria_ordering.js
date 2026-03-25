/**
 * Cafeteria Ordering - JavaScript
 * Handles category loading, item display, and cart management
 * for non-Paws Place stores via the cafeteria API.
 */

let cart = [];
let categories = [];
let currentCategory = null;
let currentItemsCache = "";
let initialStoreValue = "";
let selectedItemForModal = null;
let viewMode = 'grid'; // Default view mode

function setViewMode(mode) {
    viewMode = mode;
    // Update button styles
    const gridBtn = document.getElementById('view-grid-btn');
    const listBtn = document.getElementById('view-list-btn');

    if (mode === 'grid') {
        gridBtn.classList.add('bg-white', 'shadow-sm', 'text-[#800000]');
        gridBtn.classList.remove('text-gray-400');
        listBtn.classList.remove('bg-white', 'shadow-sm', 'text-[#800000]');
        listBtn.classList.add('text-gray-400');
    } else {
        listBtn.classList.add('bg-white', 'shadow-sm', 'text-[#800000]');
        listBtn.classList.remove('text-gray-400');
        gridBtn.classList.remove('bg-white', 'shadow-sm', 'text-[#800000]');
        gridBtn.classList.add('text-gray-400');
    }

    const items = JSON.parse(currentItemsCache || "[]");
    renderItems(items);
}

// Helper to clean item names (remove @ price and (Hot))
function cleanName(name) {
    if (!name) return "";
    return name.replace(/\s*@\s*[\d.]+/g, '').replace(/\s*\(Hot\)/gi, '').trim();
}



// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    // Custom dropdown toggler logic
    const dropdownBtn = document.getElementById('store-dropdown-btn');
    const dropdownMenu = document.getElementById('store-dropdown-menu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('opacity-0');
            dropdownMenu.classList.toggle('invisible');
            dropdownMenu.classList.toggle('scale-95');
        });

        // Close when clicking completely outside
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const switcher = document.getElementById('store-switcher');
    if (switcher) initialStoreValue = switcher.value;

    loadStoreLocations();
    loadCategories();
    // Auto-poll menu every 2 seconds to sync availability changes immediately (like Paws Place)
    setInterval(() => loadCategories(true), 2000);
});

async function loadStoreLocations() {
    try {
        const res = await fetch('../server/api/get_locations.php');
        const data = await res.json();
        if (data.success && data.locations) {
            window.allLocations = data.locations;

            const ul = document.querySelector('#store-dropdown-menu ul');
            if (ul) {
                ul.innerHTML = '';
                data.locations.forEach(loc => {
                    const isCurrent = typeof LOCATION_ID !== 'undefined' && Number(loc.location_id) === Number(LOCATION_ID);
                    const li = document.createElement('li');

                    if (isCurrent) {
                        const displayEl = document.getElementById('current-store-display');
                        if (displayEl) displayEl.textContent = loc.name;
                        li.innerHTML = `<button onclick="switchStore('${loc.slug}')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold flex items-center justify-between transition-colors border-l-4 border-[#800000] bg-red-50 text-[#800000] cursor-default">
                                <span>${loc.name}</span>
                                <span class="text-[10px] bg-[#800000] text-white py-0.5 px-2 rounded-full uppercase tracking-widest font-black">Current</span>
                            </button>`;
                    } else {
                        li.innerHTML = `<button onclick="switchStore('${loc.slug}')" class="appearance-none w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-[#800000] focus:bg-red-50 focus:text-[#800000] transition-colors border-l-4 border-transparent hover:border-[#800000]">${loc.name}</button>`;
                    }
                    ul.appendChild(li);
                });
            }
        }
    } catch (e) {
        console.error('Failed to load store locations:', e);
    }
}

function goBack() {
    window.location.href = 'store_selection.php';
}

// ===== CATEGORIES =====

async function loadCategories(isAuto = false) {
    const container = document.getElementById('category-filter');
    if (!container) return;

    try {
        let url = '../server/api/get_cafeteria_categories.php';
        if (LOCATION_ID) {
            url += `?location_id=${encodeURIComponent(LOCATION_ID)}`;
        }
        const res = await fetch(url);
        const data = await res.json();

        let newCategories = [];
        if (data.success && Array.isArray(data.categories)) {
            newCategories = data.categories;
        } else if (data.success && data.categories) {
            newCategories = Object.values(data.categories);
        } else {
            if (!isAuto) container.innerHTML = '<p class="text-sm text-red-500">Failed to load categories</p>';
            return;
        }

        // Only re-render if categories changed or it's not an auto-poll
        const currentKey = categories.map(c => c.category || c).join('|');
        const newKey = newCategories.map(c => c.category || c).join('|');

        if (isAuto && currentKey === newKey) {
            // Even if names haven't changed, we might want to refresh items if current category changed availability
            // But usually loadItems is called on change. 
            // Paws Place polls ALL items. We poll categories.
            // Let's also poll items for current category.
            if (currentCategory) loadItems(currentCategory, true);
            return;
        }

        categories = newCategories;
        renderCategories();

        // Select first category by default if none selected
        if (!currentCategory && categories.length > 0) {
            selectCategory(categories[0]);
        }
    } catch (err) {
        console.error('Error loading categories:', err);
        if (!isAuto) container.innerHTML = '<p class="text-sm text-red-500">Failed to connect to server</p>';
    }
}

function renderCategories() {
    const container = document.getElementById('category-filter');
    container.innerHTML = '';

    categories.forEach(cat => {
        const name = typeof cat === 'string' ? cat : (cat.category || cat.name || cat.category_name || '');
        const icon = cat.icon || '<i class="ph-duotone ph-fork-knife"></i>';
        const isActive = name === currentCategory;

        const card = document.createElement('div');
        card.className = `category-card flex-shrink-0 ${isActive ? 'active' : ''}`;
        card.style.flex = '0 0 auto';
        card.onclick = () => selectCategory(cat);
        card.innerHTML = `
            <span class="text-3xl mb-1">${icon}</span>
            <span class="text-xs font-bold text-center leading-tight px-1">${name}</span>
        `;
        container.appendChild(card);
    });
}

function selectCategory(cat) {
    const name = typeof cat === 'string' ? cat : (cat.category || cat.name || cat.category_name || '');
    currentCategory = name;

    // Update active state in UI
    document.querySelectorAll('.category-card').forEach(card => {
        const cardName = card.querySelector('.text-xs').textContent;
        card.classList.toggle('active', cardName === currentCategory);
    });

    currentItemsCache = ""; // Force re-render on manual category switch
    loadItems(currentCategory);
}

// ===== ITEMS =====

async function loadItems(category, isAuto = false) {
    const container = document.getElementById('menu-items-container');
    if (!isAuto) container.innerHTML = '<div class="loading-spinner">Loading items...</div>';

    try {
        let url = `../server/api/get_cafeteria_items.php?category=${encodeURIComponent(category)}`;
        if (typeof LOCATION_ID !== 'undefined' && LOCATION_ID) {
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
            currentItemsCache = "empty";
            return;
        }

        // Only re-render if items have actually changed
        const newCache = JSON.stringify(items);
        if (isAuto && currentItemsCache === newCache) {
            return; // No changes, don't rebuild DOM
        }

        currentItemsCache = newCache;
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
    if (viewMode === 'grid') {
        grid.className = 'grid grid-cols-2 md:grid-cols-3 gap-4 w-full p-4';
    } else {
        grid.className = 'menu-item-list-view';
    }

    items.forEach(item => {
        const id = item.id || item.item_id || '';
        const name = item.name || item.item_name || 'Unknown Item';
        const price = parseFloat(item.price || item.item_price || 0);
        const available = item.availability !== false && item.availability !== 0 && item.status !== 'unavailable';

        const categoryName = item.category_name || currentCategory || 'Default';
        const activeCatObj = categories.find(c => c.name === categoryName || c.category === categoryName);
        const iconHtml = activeCatObj?.icon || '<i class="ph-duotone ph-fork-knife"></i>';

        const card = document.createElement('div');
        card.id = `item-card-${id}`;
        card.className = `menu-item-card p-4 rounded-xl shadow-sm flex flex-col items-center justify-between cursor-pointer h-40 relative overflow-hidden group ${!available ? 'opacity-60' : ''}`;

        if (available) {
            card.onclick = () => openItemModal({ id, name, price, icon: iconHtml });
        }

        card.innerHTML = `
            ${available
                ? '<span class="status-badge available">Available</span>'
                : '<span class="status-badge unavailable">Sold Out</span>'}
            <div class="text-5xl mb-2 text-[#800000] group-hover:scale-110 transition-transform duration-200">${iconHtml}</div>
            <div class="text-center w-full">
                <p class="text-sm font-bold text-gray-800 line-clamp-2 leading-tight h-10 flex items-center justify-center">${cleanName(name)}</p>
                <p class="text-lg font-black text-[#800000]">₱${price.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
            </div>
            ${!available ? '<div class="absolute inset-0 bg-gray-50 bg-opacity-40 flex items-center justify-center"></div>' : ''}
        `;

        grid.appendChild(card);
    });

    container.appendChild(grid);
}

// ===== CART =====

function openItemModal(item) {
    selectedItemForModal = item;

    // Highlight the selected card
    const card = document.getElementById(`item-card-${item.id}`);
    if (card) card.classList.add('selected');

    const modal = document.getElementById('modal-container');

    // Variant detection: split by "," or " And " ONLY if a comma exists (handles "Kopiko Black, Blanca And Brown" vs "Fit and Right")
    const hasComma = item.name.includes(',');
    const variants = hasComma ? item.name.split(/[,]|(?:\s+And\s+)/i).map(v => v.trim()).filter(v => v) : null;
    let variantsHTML = '';
    if (variants) {
        variantsHTML = `
            <div class="text-left bg-gray-50 p-4 rounded-lg mb-4 w-full">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Select Item</p>
                <div class="space-y-2">
                    ${variants.map((v, idx) => `
                        <label class="flex items-center cursor-pointer bg-white p-3 rounded-xl border border-gray-200 hover:border-[#800000] transition group">
                            <input type="radio" name="item-variant" value="${v}" ${idx === 0 ? 'checked' : ''} class="w-5 h-5 accent-[#800000] cursor-pointer">
                            <span class="text-gray-800 text-sm font-bold ml-3 group-hover:text-[#800000]">${v}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `;
    }

    modal.innerHTML = `
        <div class="bg-white p-8 rounded-[2rem] w-11/12 max-w-sm shadow-2xl text-center relative animate-fade-in-up flex flex-col items-center">
            <div class="text-7xl mb-4 p-4 bg-gray-50 rounded-full text-[#800000]">${item.icon || '<i class="ph-duotone ph-fork-knife"></i>'}</div>
            <h2 class="text-3xl font-black text-gray-800 mb-1 tracking-tight">${variants ? 'Choose Variant' : cleanName(item.name)}</h2>
            <p class="text-2xl font-black text-[#800000] mb-8">${formatCurrency(item.price)}</p>
            
            ${variantsHTML}

            <div class="flex gap-4 w-full">
                <button onclick="closeModal()" class="flex-1 py-4 bg-[#e5e7eb] text-gray-700 font-black rounded-xl hover:bg-gray-300 transition-all active:scale-95">Cancel</button>
                <button onclick="confirmAddToCart()" class="flex-1 py-4 bg-[#800000] text-white font-black rounded-xl hover:bg-red-900 shadow-xl transition-all active:scale-110">Add to Tray</button>
            </div>
        </div>
    `;
    modal.classList.remove('hidden');
}

function confirmAddToCart() {
    if (!selectedItemForModal) return;

    const selectedVariant = document.querySelector('input[name="item-variant"]:checked')?.value;
    const finalItem = {
        ...selectedItemForModal,
        name: selectedVariant || selectedItemForModal.name
    };

    addToCart(finalItem);
    closeModal();
    selectedItemForModal = null;
}

function addToCart(item) {
    const existing = cart.find(c => c.id === item.id && c.name === item.name);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ ...item, qty: 1 });
    }
    renderCart();
}

function updateCartQty(index, delta) {
    const item = cart[index];
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function handleQuantityInput(index, value) {
    const val = parseInt(value);
    if (isNaN(val) || val < 1) return;
    if (cart[index]) {
        cart[index].qty = val;
    }
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-list');
    const countEl = document.getElementById('cart-count');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total');
    const orderBtn = document.getElementById('place-order-btn');

    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);
    const itemCount = cart.reduce((sum, item) => sum + item.qty, 0);

    if (cart.length === 0) {
        if (countEl) countEl.classList.add('hidden');
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <span class="text-[#800000] opacity-30 mb-2"><i class="ph-duotone ph-shopping-cart" style="font-size:40px"></i></span>
                <p class="text-sm">Your tray is empty</p>
            </div>`;
        subtotalEl.textContent = formatCurrency(0);
        totalEl.textContent = formatCurrency(0);
        orderBtn.disabled = true;
        return;
    }

    if (countEl) {
        countEl.textContent = itemCount;
        countEl.classList.remove('hidden');
    }

    container.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div class="cart-item-info">
                <p class="cart-item-name">${cleanName(item.name)}</p>
                <p class="cart-item-price">${formatCurrency(item.price)}</p>
            </div>
            <div class="cart-item-controls">
                <div class="flex items-center bg-gray-50 rounded-lg p-1 mr-2 border border-gray-100">
                    <button onclick="updateCartQty(${index}, -1)" class="cart-qty-btn">-</button>
                    <input type="number" value="${item.qty}" min="1" oninput="handleQuantityInput(${index}, this.value)" onblur="renderCart()" class="quantity-input w-8 text-center font-black text-sm bg-transparent border-none focus:outline-none focus:ring-0">
                    <button onclick="updateCartQty(${index}, 1)" class="cart-qty-btn">+</button>
                </div>
                <button onclick="removeItem(${index})" class="cart-qty-btn remove">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
        `).join('');

    subtotalEl.textContent = formatCurrency(total);
    totalEl.textContent = formatCurrency(total);
    orderBtn.disabled = false;
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ===== ORDER =====

function promptConfirmOrder() {
    if (cart.length === 0) return;
    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);
    const modal = document.getElementById('modal-container');

    const cartItemsHTML = cart.map(item => `
        <li class="flex justify-between items-start text-sm py-2 border-b border-gray-100 last:border-0">
            <div class="flex flex-col">
                <div class="flex items-center">
                    <span class="font-bold bg-gray-100 text-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs mr-2">${item.qty}</span>
                    <span class="text-gray-700 font-medium">${cleanName(item.name)}</span>
                </div>
            </div>
            <span class="font-semibold text-gray-800">${formatCurrency(item.price * item.qty)}</span>
        </li>
        `).join('');

    modal.innerHTML = `
        <div class="bg-white p-0 rounded-2xl w-11/12 max-w-md shadow-2xl text-center relative overflow-hidden transform transition-all scale-100 flex flex-col max-h-[80vh]">
            <div class="bg-[#800000] p-4 text-white">
                <h2 class="text-2xl font-black mb-0">Confirm Your Order</h2>
                <p class="text-sm opacity-90">Please review your items below</p>
            </div>
            
            <div class="p-6 overflow-y-auto text-left">
                <ul class="mb-4 space-y-1">${cartItemsHTML}</ul>
                <div class="flex justify-between items-center border-t border-gray-200 pt-4 mt-2">
                    <span class="text-gray-500 font-medium">Total Amount</span>
                    <span class="font-black text-3xl text-[#800000]">${formatCurrency(total)}</span>
                </div>
            </div>
            
            <div class="p-6 pt-0 flex gap-3 bg-white border-t border-gray-50">
                <button onclick="closeModal()" class="flex-1 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">Go Back</button>
                <button onclick="placeOrder()" class="flex-1 py-4 bg-[#800000] text-white font-bold rounded-xl hover:bg-red-900 shadow-lg transition">Confirm Order</button>
            </div>
        </div>
        `;
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-container').classList.add('hidden');
    // Remove highlights from all cards
    document.querySelectorAll('.menu-item-card.selected').forEach(el => el.classList.remove('selected'));
}

async function placeOrder() {
    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);
    const orderBtn = document.getElementById('place-order-btn');

    // Show loading state in modal instead of alert
    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-10 rounded-2xl w-11/12 max-w-sm shadow-2xl text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-[#800000] border-t-transparent mb-4"></div>
            <h2 class="text-xl font-black text-gray-800">Processing Order...</h2>
            <p class="text-gray-500 mt-2">Connecting to MIS server</p>
        </div>
        `;

    try {
        const items = cart.map(item => ({
            menu_item_id: item.id,
            external_item_name: item.name,
            quantity: item.qty,
            price_at_sale: item.price,
            modifiers: []
        }));

        const response = await fetch('../server/api/place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                items: items,
                order_source: STORE_NAME
            })
        });

        const data = await response.json();

        if (data.success) {
            showOrderSuccess(data.pre_order_code, total);
        } else {
            alert('Error placing order: ' + data.message);
            promptConfirmOrder(); // Return to confirmation
        }
    } catch (err) {
        console.error('Order error:', err);
        alert('Failed to place order. Please try again.');
        promptConfirmOrder();
    }
}

function showOrderSuccess(code, total) {
    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-8 rounded-[2rem] w-11/12 max-w-sm shadow-2xl text-center relative overflow-hidden animate-fade-in-up">
            <div class="absolute top-0 left-0 w-full h-3 bg-[#800000]"></div>
            
            <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 mt-4">
                <i class="ph-duotone ph-check-circle text-green-500 text-5xl"></i>
            </div>

            <h2 class="text-4xl font-black text-gray-800 mb-2 tracking-tighter">ORDER SENT!</h2>
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">Please proceed to the counter to pay and finalize your order.</p>

            <div class="bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200 mb-8 relative">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Your Order Code</p>
                <p class="text-6xl font-black text-[#800000] tracking-tighter tabular-nums">${code}</p>
                <div class="absolute -top-3 -left-3 w-6 h-6 bg-white rounded-full"></div>
                <div class="absolute -top-3 -right-3 w-6 h-6 bg-white rounded-full"></div>
                <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-white rounded-full"></div>
                <div class="absolute -bottom-3 -right-3 w-6 h-6 bg-white rounded-full"></div>
            </div>

            <div class="flex justify-between items-center mb-8 px-2">
                <span class="text-gray-500 font-bold uppercase text-[10px] tracking-widest">Total Due:</span>
                <span class="font-black text-2xl text-[#800000]">${formatCurrency(total)}</span>
            </div>

            <button onclick="hideOrderConfirmation()" class="w-full py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-black transition-all shadow-xl active:scale-95 uppercase tracking-widest text-sm">
                Back to Menu
            </button>
        </div>
        `;
    modal.classList.remove('hidden');
}

function hideOrderConfirmation() {
    document.getElementById('modal-container').classList.add('hidden');
    cart = [];
    renderCart();
    loadCategories(); // Refresh availability
}

// ===== UTILITIES =====

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showAlert(message, type = 'info') {
    // We already use the toast system from the PHP script if needed,
    // but we can also keep this simple one for small updates.
    const container = document.getElementById('alert-container');
    if (!container) return;
    const colors = { info: 'bg-blue-500', success: 'bg-green-600', error: 'bg-red-600' };
    const alert = document.createElement('div');
    alert.className = `${colors[type] || colors.info} text - white px - 6 py - 3 rounded - lg shadow - lg mb - 2 animate - fade -in -up`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 2500);
}

function goBack() {
    if (cart.length === 0) {
        window.location.href = 'store_selection.php';
        return;
    }

    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-8 rounded-3xl w-11/12 max-w-sm shadow-2xl text-center relative border-t-8 border-[#800000]">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ph-duotone ph-house text-[#800000] text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2">Return to Dashboard?</h3>
            <p class="text-gray-500 mb-8 leading-relaxed">Your current tray items will be cleared if you leave this page.</p>
            <div class="flex flex-col gap-3">
                <button id="exit-confirm-btn" class="w-full py-4 bg-[#800000] text-white rounded-xl font-bold shadow-lg shadow-red-100 hover:bg-red-900 transition-all active:scale-95">Yes, Return to Dashboard</button>
                <button onclick="closeModal()" class="w-full py-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">Keep Ordering</button>
            </div>
        </div>
        `;
    modal.classList.remove('hidden');

    document.getElementById('exit-confirm-btn').onclick = () => {
        window.location.href = 'store_selection.php';
    };
}

// Removed openOrderHistory, closeOrderHistory, fetchOrderHistory

function switchStore(value) {
    if (value === CURRENT_STORE_ID) return;

    if (cart && cart.length > 0) {
        const modal = document.getElementById('modal-container');
        modal.innerHTML = `
            <div class="bg-white p-8 rounded-3xl w-11/12 max-w-sm shadow-2xl text-center relative animate-fade-in-up border-t-8 border-[#800000]">
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ph-duotone ph-warning-circle text-[#800000] text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-2">Switch Stores?</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">You have items in your tray. Switching stores will clear your tray. Do you want to proceed?</p>
                <div class="flex flex-col gap-3">
                    <button id="store-confirm-btn" class="w-full py-4 bg-[#800000] text-white rounded-xl font-bold shadow-lg shadow-red-100 hover:bg-red-900 transition-all active:scale-95">Yes, Switch Store</button>
                    <button onclick="cancelStoreSwitch()" class="w-full py-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">Cancel</button>
                </div>
            </div>
        `;
        modal.classList.remove('hidden');

        document.getElementById('store-confirm-btn').onclick = () => {
            executeStoreSwitch(value);
        };
        return;
    }

    executeStoreSwitch(value);
}

function cancelStoreSwitch() {
    closeModal();
    // Dropdown UI updates are automatically reverted by closing the dropdown itself
}

function executeStoreSwitch(value) {
    if (value === 'paws-place') {
        window.location.href = 'kiosk_ordering.php';
    } else {
        const loc = (window.allLocations || []).find(l => l.slug === value);
        if (loc) {
            window.location.href = `cafeteria_ordering.php?store=${encodeURIComponent(loc.name)}&location_id=${loc.location_id}`;
        }
    }
}
