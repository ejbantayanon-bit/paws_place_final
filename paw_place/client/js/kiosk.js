// --- MENU DATA - LOADED FROM DATABASE ---
let MENU = [];  // Will be populated from API

const ADDON_PRICES = {
    'Pearls': 10.00,
    'Coffee': 10.00,
    'Milk': 10.00,
    'Caramel Syrup': 10.00,
    'Coffee Jelly': 10.00,
    'Fruit Jelly': 10.00
};

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

let cart = [];
let activeCategory = 'Milktea';
let selectedItemForModal = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchMenuData();
    // Start fast polling for "immediate" availability updates
    setInterval(fetchMenuData, 5000);
});

function exitKiosk() {
    // Show confirmation modal to exit to main login
    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-6 rounded-2xl w-11/12 max-w-sm shadow-2xl text-center relative animate-fade-in-up">
            <h3 class="text-lg font-bold mb-2">Exit Ordering?</h3>
            <p class="text-sm text-gray-500 mb-4">You will be returned to the login screen.</p>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 py-2 bg-gray-200 rounded-md font-semibold">Continue Ordering</button>
                <button id="exit-confirm-btn" class="flex-1 py-2 bg-[#800000] text-white rounded-md font-semibold">Exit</button>
            </div>
        </div>
    `;
    modal.classList.remove('hidden');

    // Exit directly to main login page
    document.getElementById('exit-confirm-btn').onclick = async () => {
        try {
            // Clear session and redirect to main login
            await fetch('../server/api/logout.php', { method: 'POST' });
            window.location.href = '1_login.php';
        } catch (e) {
            console.error('Logout error', e);
            window.location.href = '1_login.php';
        }
    };
}

// --- Data Helpers ---
let isInitialLoad = true;
let categoriesCacheMap = {};
let modifiersCacheMap = {};

async function fetchMenuData() {
    try {
        // Only load categories and modifiers on initial load to save bandwidth
        if (isInitialLoad) {
            const catRes = await fetch('../server/api/get_categories.php');
            const catData = await catRes.json();
            if (catData.success && Array.isArray(catData.categories)) {
                catData.categories.forEach(c => { categoriesCacheMap[c.category_id] = c.name; });
            }

            const modRes = await fetch('../server/api/get_modifiers.php');
            const modData = await modRes.json();
            if (modData.success && Array.isArray(modData.modifiers)) {
                modData.modifiers.forEach(m => {
                    const catId = m.applicable_category_id ? Number(m.applicable_category_id) : null;
                    if (catId) {
                        modifiersCacheMap[catId] = modifiersCacheMap[catId] || [];
                        modifiersCacheMap[catId].push(m.name);
                    }
                });
            }
        }

        const response = await fetch('../server/api/get_menu_items.php?include_hidden=0');
        const data = await response.json();

        if (data.success && data.items) {
            const newMenu = data.items.map(item => ({
                item_id: Number(item.item_id),
                name: item.name,
                category_id: Number(item.category_id),
                category: ([1, 2].includes(Number(item.category_id)) ? 'Coffee' : (categoriesCacheMap[item.category_id] || 'Uncategorized')),
                base_price: parseFloat(item.base_price) || 0,
                is_available: (item.is_available === 1 || item.is_available === '1' || item.is_available === true),
                image_url: item.image_url || null,
                type: inferItemType(item, categoriesCacheMap[item.category_id]),
                icon: getIconForCategoryName(categoriesCacheMap[item.category_id] || ''),
                add_ons: modifiersCacheMap[Number(item.category_id)] || getAddOnsForCategory(item.category_id)
            }));

            // Check if availability changed for active modal
            if (selectedItemForModal) {
                const currentItemStatus = newMenu.find(i => i.item_id === selectedItemForModal.item_id);
                if (currentItemStatus && !currentItemStatus.is_available) {
                    closeModal(); // Close modal if item became unavailable while viewing
                }
            }

            // Detect changes before re-rendering to avoid flickering
            const menuChanged = JSON.stringify(MENU) !== JSON.stringify(newMenu);
            if (menuChanged || isInitialLoad) {
                MENU = newMenu;
                if (isInitialLoad && MENU.length) activeCategory = MENU[0].category;

                // Preserve scroll position of category filter
                const filterScrollLeft = document.getElementById('category-filter')?.scrollLeft || 0;
                renderMenu(MENU);
                if (document.getElementById('category-filter')) {
                    document.getElementById('category-filter').scrollLeft = filterScrollLeft;
                }
            }

            isInitialLoad = false;
        } else if (isInitialLoad) {
            console.error('Failed to load menu:', data.message);
            alert('Error loading menu. Please refresh.');
        }
    } catch (error) {
        console.error('Error fetching menu:', error);
        if (isInitialLoad) alert('Unable to connect to server. Please check your connection.');
    }
}

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

function getAddOnsForCategory(categoryId) {
    const addOnsMap = {
        // These map to category_id values; adjust if your categories differ
        4: ['Pearls', 'Coffee Jelly'],  // Milk Tea (example)
        1: ['Milk', 'Caramel Syrup'],   // Hot Coffee (example)
        2: ['Milk', 'Coffee Jelly']     // Cold Coffee (example)
    };
    return addOnsMap[categoryId] || [];
}

function inferItemType(item, categoryName) {
    // Prefer explicit type from API if present
    if (item.type && typeof item.type === 'string' && item.type.trim() !== '') return item.type;

    const name = (item.name || '').toLowerCase();
    const cat = (categoryName || '').toLowerCase();

    // Only infer hot/cold for Coffee or Specialty categories
    if (cat.includes('coffee') || cat.includes('specialty')) {
        if (name.includes('iced') || name.includes('cold') || name.includes('ice') || name.includes('frappe') || name.includes('blended') || name.includes('frozen')) return 'Cold Brew';
        return 'Hot Brew';
    }

    return item.type || null;
}

// --- UI Functions ---
function getCategories(menu) {
    const categories = new Set(menu.map(item => item.category));
    return Array.from(categories);
}

function renderMenu(menu, filter = activeCategory) {
    activeCategory = filter;
    const menuContainer = document.getElementById('menu-items-container');
    const categoryFilter = document.getElementById('category-filter');

    if (!menuContainer) return; // Guard clause

    menuContainer.innerHTML = '';
    categoryFilter.innerHTML = '';

    // Only show available items in the menu
    const availableMenu = menu.filter(item => item.is_available);
    const categories = getCategories(availableMenu);

    // Make category strip horizontally scrollable and visually spaced
    categoryFilter.style.display = 'flex';
    categoryFilter.style.overflowX = 'auto';
    categoryFilter.style.gap = '12px';
    categoryFilter.style.padding = '8px 0';
    categoryFilter.style.whiteSpace = 'nowrap';

    categories.forEach(cat => {
        const isActive = cat === activeCategory;
        const icon = getIconForCategoryName(cat);

        const card = document.createElement('div');
        card.className = `category-card flex-shrink-0 ${isActive ? 'active' : ''}`;
        card.style.flex = '0 0 auto';
        card.onclick = () => renderMenu(MENU, cat);

        card.innerHTML = `
            <span class="text-3xl mb-1">${icon}</span>
            <span class="text-xs font-bold text-center leading-tight px-1">${cat}</span>
        `;
        categoryFilter.appendChild(card);
    });

    // SPECIAL LOGIC FOR COFFEE and SPECIALTY (split by Hot/Cold types)
    if (filter === 'Coffee' || filter.toLowerCase().includes('specialty')) {
        const hotItems = availableMenu.filter(item => item.category === filter && item.type === 'Hot Brew');
        const coldItems = availableMenu.filter(item => item.category === filter && item.type === 'Cold Brew');

        if (hotItems.length > 0) {
            const hotHeader = document.createElement('h3');
            hotHeader.className = 'col-span-full text-xl font-black text-gray-800 mt-4 mb-2 pb-1 border-b border-gray-200 flex items-center gap-2';
            hotHeader.innerHTML = '<span class="text-2xl">☕</span> Hot Brew';
            menuContainer.appendChild(hotHeader);

            const hotGrid = document.createElement('div');
            hotGrid.className = 'col-span-full grid grid-cols-2 md:grid-cols-3 gap-4';
            menuContainer.appendChild(hotGrid);

            hotItems.forEach(item => hotGrid.appendChild(createItemCard(item)));
        }

        if (coldItems.length > 0) {
            const coldHeader = document.createElement('h3');
            coldHeader.className = 'col-span-full text-xl font-black text-gray-800 mt-8 mb-2 pb-1 border-b border-gray-200 flex items-center gap-2';
            coldHeader.innerHTML = '<span class="text-2xl">🧊</span> Cold Brew';
            menuContainer.appendChild(coldHeader);

            const coldGrid = document.createElement('div');
            coldGrid.className = 'col-span-full grid grid-cols-2 md:grid-cols-3 gap-4';
            menuContainer.appendChild(coldGrid);

            coldItems.forEach(item => coldGrid.appendChild(createItemCard(item)));
        }

    } else {
        // STANDARD RENDERING
        // Create a grid wrapper for standard categories
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 md:grid-cols-3 gap-4 w-full';
        menuContainer.appendChild(grid);

        const filteredMenu = availableMenu.filter(item => item.category === filter);
        filteredMenu.forEach(item => {
            grid.appendChild(createItemCard(item));
        });
    }
}

function createItemCard(item) {
    const itemCard = document.createElement('div');
    // Drastic size reduction for mobile (min-h: 110px)
    itemCard.className = `menu-item-card p-2 sm:p-4 rounded-xl shadow-sm flex flex-col items-center justify-between cursor-pointer min-h-[110px] sm:h-40 relative overflow-hidden group ${!item.is_available ? 'opacity-50 pointer-events-none' : ''}`;
    itemCard.onclick = item.is_available ? () => openItemModal(item) : null;

    const typeBadge = item.category === 'Coffee'
        ? `<span class="absolute top-1 right-1 sm:top-2 sm:right-2 text-[7px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-full ${item.type === 'Cold Brew' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'}">${item.type}</span>`
        : '';

    itemCard.innerHTML = `
        ${typeBadge}
        <div class="text-3xl sm:text-5xl mb-0.5 sm:mb-2 group-hover:scale-110 transition-transform duration-200">${item.icon}</div>
        <div class="text-center w-full">
            <p class="text-[10px] sm:text-sm font-bold text-gray-800 leading-tight truncate px-1">${item.name}</p>
            <p class="text-sm sm:text-lg font-black text-[#800000]">₱${item.base_price.toFixed(2)}</p>
        </div>
        ${!item.is_available ? '<div class="absolute inset-0 bg-gray-100 bg-opacity-80 flex items-center justify-center text-red-600 font-bold transform rotate-[-15deg] border-2 border-red-600 rounded">SOLD OUT</div>' : ''}
    `;
    return itemCard;
}

// --- ITEM MODAL ---
function openItemModal(item) {
    selectedItemForModal = item;
    const modal = document.getElementById('modal-container');
    const hasAddons = item.add_ons && item.add_ons.length > 0;

    let addonsHTML = '';
    if (hasAddons) {
        addonsHTML = `
            <div class="text-left bg-gray-50 p-4 rounded-lg mb-4 w-full">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Customize Your Drink</p>
                <div class="space-y-3">
                    ${item.add_ons.map(addon => `
                        <label class="flex items-center justify-between cursor-pointer bg-white p-2 rounded border border-gray-200 hover:border-maroon transition">
                            <div class="flex items-center">
                                <input type="checkbox" value="${addon}" class="addon-checkbox form-checkbox h-5 w-5 text-maroon rounded border-gray-300 focus:ring-maroon">
                                <span class="text-gray-800 text-sm font-semibold ml-3">${addon}</span>
                            </div>
                            <span class="text-xs font-bold text-gray-500">+₱${ADDON_PRICES[addon].toFixed(2)}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        addonsHTML = `<div class="bg-gray-50 p-4 rounded-lg mb-4 w-full"><p class="text-sm text-gray-400 italic text-center">No add-ons available for this item.</p></div>`;
    }

    modal.innerHTML = `
        <div class="bg-white p-6 rounded-2xl w-11/12 max-w-sm shadow-2xl text-center relative animate-fade-in-up flex flex-col items-center">
            <div class="text-6xl mb-2">${item.icon}</div>
            <h2 class="text-2xl font-black text-gray-800 mb-1">${item.name}</h2>
            <p class="text-lg font-bold text-[#800000] mb-6">₱${item.base_price.toFixed(2)}</p>
            
            ${addonsHTML}

            <div class="flex gap-3 w-full">
                <button onclick="closeModal()" class="flex-1 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300">Cancel</button>
                <button onclick="confirmAddToCart()" class="flex-1 py-3 bg-[#800000] text-white font-bold rounded-xl hover:bg-red-900 shadow-lg">Add to Tray</button>
            </div>
        </div>
    `;
    modal.classList.remove('hidden');
}

function confirmAddToCart() {
    if (!selectedItemForModal) return;

    const selectedAddons = [];
    let addonsCost = 0;
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        selectedAddons.push(cb.value);
        addonsCost += ADDON_PRICES[cb.value];
    });

    const newItem = {
        ...selectedItemForModal,
        modifiers: selectedAddons,
        final_price: selectedItemForModal.base_price + addonsCost
    };

    const existingItem = cart.find(i =>
        i.item_id === newItem.item_id &&
        JSON.stringify(i.modifiers.sort()) === JSON.stringify(newItem.modifiers.sort())
    );

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...newItem, quantity: 1 });
    }

    renderCart();
    closeModal();
    selectedItemForModal = null;
}

function closeModal() {
    document.getElementById('modal-container').classList.add('hidden');
}

// --- Cart Actions ---
function updateCartItemQuantity(index, change) {
    cart[index].quantity += change;
    if (cart[index].quantity < 1) cart[index].quantity = 1;
    renderCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function calculateCartTotal() {
    return cart.reduce((total, item) => total + (item.final_price * item.quantity), 0);
}

function renderCart() {
    const cartContainer = document.getElementById('cart-list');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total');
    const btn = document.getElementById('place-order-btn');
    const emptyBtn = document.getElementById('empty-tray-btn');

    cartContainer.innerHTML = '';
    const total = calculateCartTotal();

    if (cart.length === 0) {
        if (emptyBtn) emptyBtn.classList.add('hidden');
        cartContainer.innerHTML = '';
        btn.disabled = true;
    } else {
        if (emptyBtn) emptyBtn.classList.remove('hidden');
        cart.forEach((item, index) => {
            const addonsText = item.modifiers && item.modifiers.length > 0
                ? `<p class="text-xs text-gray-500 mt-1">+ ${item.modifiers.join(', ')}</p>`
                : '';

            const itemEl = document.createElement('div');
            itemEl.className = 'flex items-center justify-between bg-white p-3 rounded-lg shadow-sm border border-gray-100';
            itemEl.innerHTML = `
                <div class="flex-grow">
                    <p class="font-bold text-gray-800 text-sm">${item.name}</p>
                    ${addonsText}
                    <p class="text-xs text-[#800000] font-bold mt-1">₱${item.final_price.toFixed(2)}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center bg-gray-100 rounded-lg p-1 mr-2">
                        <button onclick="updateCartItemQuantity(${index}, -1)" class="w-8 h-8 flex items-center justify-center text-gray-600 font-bold hover:bg-gray-200 rounded">-</button>
                        <span class="w-8 text-center font-bold text-sm">${item.quantity}</span>
                        <button onclick="updateCartItemQuantity(${index}, 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 font-bold hover:bg-gray-200 rounded">+</button>
                    </div>
                    <button onclick="removeItem(${index})" class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `;
            cartContainer.appendChild(itemEl);
        });
        btn.disabled = false;
    }

    subtotalEl.textContent = `₱${total.toFixed(2)}`;
    totalEl.textContent = `₱${total.toFixed(2)}`;
}

// --- Order Processing ---
function promptConfirmOrder() {
    if (cart.length === 0) return;
    const total = calculateCartTotal();
    const modal = document.getElementById('modal-container');

    const cartItemsHTML = cart.map(item => `
        <li class="flex justify-between items-start text-sm py-2 border-b border-gray-100 last:border-0">
            <div class="flex flex-col">
                <div class="flex items-center">
                    <span class="font-bold bg-gray-100 text-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs mr-2">${item.quantity}</span>
                    <span class="text-gray-700 font-medium">${item.name}</span>
                </div>
                ${item.modifiers.length ? `<span class="text-xs text-gray-400 ml-8">+ ${item.modifiers.join(', ')}</span>` : ''}
            </div>
            <span class="font-semibold text-gray-800">₱${(item.final_price * item.quantity).toFixed(2)}</span>
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
                    <span class="font-black text-3xl text-[#800000]">₱${total.toFixed(2)}</span>
                </div>
            </div>
            
            <div class="p-6 pt-0 flex gap-3 bg-white border-t border-gray-50">
                <button onclick="closeModal()" class="flex-1 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">Go Back</button>
                <button onclick="finalizeOrder()" class="flex-1 py-4 bg-[#800000] text-white font-bold rounded-xl hover:bg-red-900 shadow-lg transition">Pay Now</button>
            </div>
        </div>
    `;
    modal.classList.remove('hidden');
}

async function finalizeOrder() {
    const total = calculateCartTotal();

    // Format items for API
    const items = cart.map(item => ({
        menu_item_id: item.item_id,
        quantity: item.quantity,
        price_at_sale: item.final_price,
        modifiers: item.modifiers || []
    }));

    try {
        const response = await fetch('../server/api/place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                items: items,
                order_source: 'Kiosk',
                customer_name: CURRENT_USER_NAME
            })
        });

        const data = await response.json();

        if (data.success) {
            showOrderSuccess(data.pre_order_code, total);

        } else {
            alert('Error placing order: ' + data.message);
        }
    } catch (error) {
        console.error('Error placing order:', error);
        alert('Failed to place order. Please try again.');
    }
}

function showOrderSuccess(code, total) {
    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-8 rounded-2xl w-11/12 max-w-md shadow-2xl text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-[#800000]"></div>
            <h2 class="text-3xl font-black text-gray-800 mb-2">ORDER SENT!</h2>
            <p class="text-gray-500 text-sm mb-6">Please pay at the counter to finalize.</p>
            <div class="bg-gray-100 p-6 rounded-xl border-2 border-dashed border-gray-300 mb-6">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Your Order Code</p>
                <p class="text-6xl font-black text-[#800000] tracking-tighter">${code}</p>
            </div>
            <div class="text-center mb-6">
                <span class="font-bold text-gray-600">Total Due: </span>
                <span class="font-black text-xl text-[#800000]">₱${total.toFixed(2)}</span>
            </div>
            <button onclick="hideOrderConfirmation()" class="w-full py-4 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-900 transition shadow-lg">Start New Order</button>
        </div>
    `;
    modal.classList.remove('hidden');
}

function hideOrderConfirmation() {
    document.getElementById('modal-container').classList.add('hidden');
    cart = [];
    renderCart();
    fetchMenuData();
}

// --- ORDER HISTORY FUNCTIONS ---
async function openOrderHistory() {
    console.log('openOrderHistory called');
    const modal = document.getElementById('order-history-modal');
    if (!modal) {
        console.error('Modal not found!');
        alert('Order history modal not found in page');
        return;
    }
    modal.classList.remove('hidden');
    console.log('Modal opened, fetching orders...');

    try {
        const response = await fetch('../server/api/get_student_orders.php');
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Orders data:', data);

        if (data.success && data.orders && data.orders.length > 0) {
            let html = '';
            data.orders.forEach(order => {
                const date = new Date(order.time_placed);
                const dateStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                const statusColor = {
                    'PENDING PAYMENT': 'bg-yellow-100 text-yellow-800',
                    'PREPARING': 'bg-blue-100 text-blue-800',
                    'READY': 'bg-green-100 text-green-800',
                    'SERVED': 'bg-gray-100 text-gray-800',
                    'CANCELLED': 'bg-red-100 text-red-800'
                }[order.status] || 'bg-gray-100 text-gray-800';

                html += `
                    <div class="border border-gray-200 rounded-lg p-4 mb-3">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-gray-800 flex items-center gap-2 flex-wrap">
                                    <span>Order #${order.pre_order_code}</span>
                                    <span class="text-[10px] bg-red-50 text-[#800000] font-black border border-red-100 px-2 py-0.5 rounded-full uppercase tracking-tighter">${order.customer_name || 'Guest'}</span>
                                </p>
                                <p class="text-xs text-gray-500">${dateStr}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColor}">${order.status}</span>
                        </div>
                        <div class="bg-gray-50 rounded p-3 mb-2">
                `;

                order.items.forEach(item => {
                    html += `<p class="text-sm text-gray-700"><span class="font-semibold">${item.quantity}x</span> ${item.name} - ₱${parseFloat(item.price_at_sale).toFixed(2)}</p>`;
                });

                html += `
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">Total:</p>
                            <p class="font-bold text-lg text-[#800000]">₱${parseFloat(order.total_amount).toFixed(2)}</p>
                        </div>
                    </div>
                `;
            });
            document.getElementById('order-history-list').innerHTML = html;
        } else {
            document.getElementById('order-history-list').innerHTML = '<p class="text-center text-gray-500 py-8">No orders yet. Start ordering!</p>';
        }
    } catch (error) {
        console.error('Error loading order history:', error);
        document.getElementById('order-history-list').innerHTML = '<p class="text-center text-red-500 py-8">Failed to load order history: ' + error.message + '</p>';
    }
}

function closeOrderHistory() {
    document.getElementById('order-history-modal').classList.add('hidden');
}

