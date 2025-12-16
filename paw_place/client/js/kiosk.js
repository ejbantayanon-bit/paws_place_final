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
    'Milktea': '🧋',
    'Coffee': '☕',
    'Fruity Soda': '🍹',
    'Specialty': '⭐',
    'Milk': '🥛',
    'Ice Cream': '🍦'
};

let cart = [];      
let activeCategory = 'Milktea'; 
let selectedItemForModal = null; 

document.addEventListener('DOMContentLoaded', () => { fetchMenuData(); });

function exitKiosk() {
    const password = prompt("Enter Staff Password to Exit Kiosk Mode:");
    if (password === 'password') { 
        alert("Closing Kiosk...");
        window.location.href = '1_login.html';
    } else if (password !== null) {
        alert("Incorrect Password.");
    }
}

// --- Data Helpers ---
async function fetchMenuData() {
    try {
        // Load categories first so we can map category_id -> name
        const catRes = await fetch('../server/api/get_categories.php');
        const catData = await catRes.json();
        const categoriesMap = {};
        if (catData.success && Array.isArray(catData.categories)) {
            catData.categories.forEach(c => { categoriesMap[c.category_id] = c.name; });
        }

        const response = await fetch('../server/api/get_menu_items.php');
        const data = await response.json();
        
        if (data.success && data.items) {
            // Map database items to include icons and category names
            MENU = data.items.map(item => ({
                // normalize types from API (strings -> numbers/booleans)
                item_id: Number(item.item_id),
                name: item.name,
                category_id: Number(item.category_id),
                category: categoriesMap[item.category_id] || 'Uncategorized',
                base_price: parseFloat(item.base_price) || 0,
                is_available: (item.is_available === 1 || item.is_available === '1' || item.is_available === true),
                image_url: item.image_url || null,
                type: item.type || null,
                icon: getIconForCategoryName(categoriesMap[item.category_id] || ''),
                add_ons: getAddOnsForCategory(item.category_id)
            }));

            // Set default active category to first available
            if (MENU.length) activeCategory = MENU[0].category;
            renderMenu(MENU);
        } else {
            console.error('Failed to load menu:', data.message);
            alert('Error loading menu. Please refresh.');
        }
    } catch (error) {
        console.error('Error fetching menu:', error);
        alert('Unable to connect to server. Please check your connection.');
    }
}

function getIconForCategoryName(name) {
    const n = (name || '').toLowerCase();
    if (n.includes('coffee')) return '☕';
    if (n.includes('milk') || n.includes('tea')) return '🧋';
    if (n.includes('soda')) return '🍹';
    if (n.includes('ice cream') || n.includes('ice')) return '🍦';
    return '🍽️';
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

// --- UI Functions ---
function getCategories(menu) {
    const categories = new Set(menu.map(item => item.category));
    return Array.from(categories);
}

function renderMenu(menu, filter = activeCategory) {
    activeCategory = filter;
    const menuContainer = document.getElementById('menu-items-container'); 
    const categoryFilter = document.getElementById('category-filter');
    
    if(!menuContainer) return; // Guard clause

    menuContainer.innerHTML = '';
    categoryFilter.innerHTML = '';

    const categories = getCategories(menu);
    
    categories.forEach(cat => {
        const isActive = cat === activeCategory;
        const icon = CATEGORY_ICONS[cat] || '🍽️';
        
        const card = document.createElement('div');
        card.className = `category-card flex-shrink-0 ${isActive ? 'active' : ''}`;
        card.onclick = () => renderMenu(MENU, cat);
        
        card.innerHTML = `
            <span class="text-3xl mb-1">${icon}</span>
            <span class="text-xs font-bold text-center leading-tight px-1">${cat}</span>
        `;
        categoryFilter.appendChild(card);
    });

    // SPECIAL LOGIC FOR COFFEE
    if (filter === 'Coffee') {
        const hotItems = menu.filter(item => item.category === 'Coffee' && item.type === 'Hot Brew');
        const coldItems = menu.filter(item => item.category === 'Coffee' && item.type === 'Cold Brew');

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
        
        const filteredMenu = menu.filter(item => item.category === filter);
        filteredMenu.forEach(item => {
            grid.appendChild(createItemCard(item));
        });
    }
}

function createItemCard(item) {
    const itemCard = document.createElement('div');
    itemCard.className = `menu-item-card p-4 rounded-xl shadow-sm flex flex-col items-center justify-between cursor-pointer h-40 relative overflow-hidden group ${!item.is_available ? 'opacity-50 pointer-events-none' : ''}`;
    itemCard.onclick = item.is_available ? () => openItemModal(item) : null;
    
    const typeBadge = item.category === 'Coffee' 
        ? `<span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full ${item.type === 'Cold Brew' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'}">${item.type}</span>`
        : '';

    itemCard.innerHTML = `
        ${typeBadge}
        <div class="text-5xl mb-2 group-hover:scale-110 transition-transform duration-200">${item.icon}</div>
        <div class="text-center w-full">
            <p class="text-sm font-bold text-gray-800 truncate">${item.name}</p>
            <p class="text-lg font-black text-[#800000]">₱${item.base_price.toFixed(2)}</p>
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
    if (cart[index].quantity <= 0) cart.splice(index, 1);
    renderCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function confirmClearCart() {
    if (cart.length > 0 && confirm("Are you sure you want to remove all items from your tray?")) {
        clearCart();
    }
}

function clearCart() {
    cart = [];
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
        cartContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <span class="text-4xl mb-2">🛒</span>
                <p class="text-sm">Your tray is empty</p>
            </div>`;
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
                <button onclick="finalizeOrder()" class="flex-1 py-4 bg-[#800000] text-white font-bold rounded-xl hover:bg-red-900 shadow-lg transition">Yes, Pay Now</button>
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
                order_source: 'Kiosk'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showOrderSuccess(data.pre_order_code, total);
            clearCart();
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
    fetchMenuData(); 
}
