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
    'Default': '<i class="ph-duotone ph-fork-knife"></i>'
};

let categories = [];
let currentCategory = null;
let cart = [];
let initialStoreValue = "";
let activeSubFilter = 'Hot Brew'; // Default for Coffee/Specialty
let selectedItemForModal = null; // This was missing an assignment in the instruction, assuming null.
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

    renderMenu(MENU); // Re-render with new mode and current data
}

// Helper to clean item names (remove @ price and (Hot))
function cleanName(name) {
    if (!name) return "";
    return name.replace(/\s*@\s*[\d.]+/g, '').replace(/\s*\(Hot\)/gi, '').trim();
}
// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    const switcher = document.getElementById('store-switcher');
    if (switcher) initialStoreValue = switcher.value;

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

    // Load config and init
    fetchMenuData();
});
// Auto-poll menu every 2 seconds to sync availability changes immediately
setInterval(() => fetchMenuData(true), 2000);

function exitKiosk() {
    // If cart is empty, bypass confirmation and go back to dashboard
    if (cart.length === 0) {
        window.location.href = 'store_selection.php';
        return;
    }

    // Show confirmation modal to exit to main dashboard
    const modal = document.getElementById('modal-container');
    modal.innerHTML = `
        <div class="bg-white p-8 rounded-3xl w-11/12 max-w-sm shadow-2xl text-center relative animate-fade-in-up border-t-8 border-[#800000]">
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

    // Go back to store selection (keep session active)
    document.getElementById('exit-confirm-btn').onclick = () => {
        window.location.href = 'store_selection.php';
    };
}

function closeModal() {
    document.getElementById('modal-container').classList.add('hidden');
}

// --- CURRENCY FORMATTER ---
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// --- Data Helpers ---
async function fetchMenuData(isAuto = false) {
    try {
        // Load categories first so we can map category_id -> name
        const catRes = await fetch('../server/api/get_categories.php');
        const catData = await catRes.json();
        const categoriesMap = {};
        if (catData.success && Array.isArray(catData.categories)) {
            catData.categories.forEach(c => { categoriesMap[c.category_id] = c.name; });
        }

        // Load modifiers mapping (category -> modifiers)
        const modRes = await fetch('../server/api/get_modifiers.php');
        const modData = await modRes.json();
        const modifiersMap = {};
        if (modData.success && Array.isArray(modData.modifiers)) {
            modData.modifiers.forEach(m => {
                const catId = m.applicable_category_id ? Number(m.applicable_category_id) : null;
                if (catId) {
                    modifiersMap[catId] = modifiersMap[catId] || [];
                    modifiersMap[catId].push(m.name);
                }
            });
        }

        const response = await fetch('../server/api/get_menu_items.php?include_hidden=1');
        const data = await response.json();

        if (data.success && data.items) {
            // Map database items to include icons and normalized category names
            const newMenu = data.items.map(item => ({
                item_id: Number(item.item_id),
                name: item.name,
                category_id: Number(item.category_id),
                category: ([1, 2].includes(Number(item.category_id)) ? 'Coffee' : (categoriesMap[item.category_id] || 'Uncategorized')),
                base_price: parseFloat(item.base_price) || 0,
                is_available: (item.is_available === 1 || item.is_available === '1' || item.is_available === true),
                image_url: item.image_url || null,
                type: inferItemType(item, categoriesMap[item.category_id]),
                icon: getIconForCategoryName(categoriesMap[item.category_id] || ''),
                add_ons: modifiersMap[Number(item.category_id)] || getAddOnsForCategory(item.category_id)
            }));

            // SMART CHECK: If auto-polling, only re-render if availability or price actually changed
            if (isAuto && MENU.length > 0) {
                const currentKey = MENU.map(i => `${i.item_id}:${i.is_available}:${i.base_price}`).join('|');
                const newKey = newMenu.map(i => `${i.item_id}:${i.is_available}:${i.base_price}`).join('|');
                if (currentKey === newKey) {
                    return; // Nothing changed, skip render & preserve user's category
                }
                // Something changed — update MENU but DO NOT reset activeCategory
                MENU = newMenu;
                renderMenu(MENU); // uses current activeCategory automatically
                return;
            }

            // INITIAL LOAD: set default active category
            MENU = newMenu;
            if (MENU.length) {
                activeCategory = MENU[0].category;
                // Set default subfilter based on available items in first category
                const hasHot = MENU.some(i => i.category === activeCategory && i.type === 'Hot Brew');
                const hasCold = MENU.some(i => i.category === activeCategory && i.type === 'Cold Brew');
                activeSubFilter = hasHot ? 'Hot Brew' : (hasCold ? 'Cold Brew' : null);
            }
            renderMenu(MENU);
        } else {
            console.error('Failed to load menu:', data.message);
            if (!isAuto) alert('Error loading menu. Please refresh.');
        }
    } catch (error) {
        console.error('Error fetching menu:', error);
        if (!isAuto) alert('Unable to connect to server. Please check your connection.');
    }
}

function getIconForCategoryName(name) {
    const n = (name || '').toLowerCase();
    if (!n) return CATEGORY_ICONS['Default'];
    if (n.includes('coffee')) return CATEGORY_ICONS['Coffee'];
    if (n.includes('milk tea') || n.includes('milktea') || (n.includes('milk') && n.includes('tea'))) return CATEGORY_ICONS['Milk Tea'];
    if (n.includes('milk') && !n.includes('tea')) return CATEGORY_ICONS['Milk Drink'];
    if (n.includes('soda') || n.includes('fruity')) return CATEGORY_ICONS['Fruity Soda'];
    if (n.includes('specialty')) return CATEGORY_ICONS['Specialty'];
    if (n.includes('add') || n.includes('addon') || n.includes('add ons')) return CATEGORY_ICONS['Add Ons'];
    if (n.includes('ice cream bar') || n.includes('ice-cream bar')) return CATEGORY_ICONS['Ice Cream Bar'];
    if (n.includes('ice cream') || n.includes('ice')) return CATEGORY_ICONS['Ice Cream'];
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

    const categories = getCategories(menu);

    // Make category strip horizontally scrollable and visually spaced
    categoryFilter.style.display = 'flex';
    categoryFilter.style.overflowX = 'auto';
    categoryFilter.style.gap = '12px';
    categoryFilter.style.padding = '12px 0'; // Sync with cafeteria
    categoryFilter.style.whiteSpace = 'nowrap';

    categories.forEach(cat => {
        const isActive = cat === activeCategory;
        const icon = getIconForCategoryName(cat);

        const card = document.createElement('div');
        card.className = `category-card flex-shrink-0 ${isActive ? 'active' : ''}`;
        card.style.flex = '0 0 auto';
        card.onclick = () => {
            activeCategory = cat;
            const hasHot = menu.some(i => i.category === cat && i.type === 'Hot Brew');
            const hasCold = menu.some(i => i.category === cat && i.type === 'Cold Brew');
            if (hasHot) activeSubFilter = 'Hot Brew';
            else if (hasCold) activeSubFilter = 'Cold Brew';
            else activeSubFilter = null;
            renderMenu(menu, cat);
        };

        card.innerHTML = `
            <span class="text-3xl mb-1">${icon}</span>
            <span class="text-xs font-bold text-center leading-tight px-1">${cat}</span>
        `;
        categoryFilter.appendChild(card);
    });

    // SPECIAL LOGIC FOR COFFEE and SPECIALTY (support for Hot/Cold switch)
    const hasHotItems = menu.some(item => item.category === filter && item.type === 'Hot Brew');
    const hasColdItems = menu.some(item => item.category === filter && item.type === 'Cold Brew');

    if (hasHotItems && hasColdItems) {
        // Render Hot/Cold Switcher
        const switchBar = document.createElement('div');
        switchBar.className = 'sub-filter-bar flex gap-2 mb-6 p-1 bg-gray-100 rounded-xl w-max self-center';

        const types = ['Hot Brew', 'Cold Brew'];
        types.forEach(type => {
            const btn = document.createElement('button');
            const isActive = type === activeSubFilter;
            btn.className = `sub-filter-btn px-8 py-2.5 rounded-lg font-black text-sm transition-all ${isActive ? 'bg-[#800000] text-white shadow-md' : 'text-gray-500 hover:bg-gray-200'}`;
            btn.textContent = type === 'Hot Brew' ? 'HOT' : 'COLD';
            btn.onclick = () => {
                activeSubFilter = type;
                renderMenu(menu, filter);
            };
            switchBar.appendChild(btn);
        });
        menuContainer.appendChild(switchBar);

        // Filter items based on switch
        const grid = document.createElement('div');
        grid.className = viewMode === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 gap-4 w-full' : 'menu-item-list-view';
        menuContainer.appendChild(grid);

        const itemsToRender = menu.filter(item => item.category === filter && item.type === activeSubFilter);
        itemsToRender.forEach(item => grid.appendChild(createItemCard(item)));

    } else {
        // STANDARD RENDERING or single-type category
        const grid = document.createElement('div');
        grid.className = viewMode === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 gap-4 w-full' : 'menu-item-list-view';
        menuContainer.appendChild(grid);

        const filteredMenu = menu.filter(item => item.category === filter);
        filteredMenu.forEach(item => {
            grid.appendChild(createItemCard(item));
        });
    }
}

function createItemCard(item) {
    const itemCard = document.createElement('div');
    itemCard.id = `item-card-${item.item_id}`;
    itemCard.className = `menu-item-card p-4 rounded-xl shadow-sm flex flex-col items-center justify-between cursor-pointer h-40 relative overflow-hidden group ${!item.is_available ? 'opacity-60' : ''}`;
    itemCard.onclick = item.is_available ? () => openItemModal(item) : null;

    const typeBadge = item.category === 'Coffee'
        ? `<span class="absolute top-2 left-2 text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 uppercase tracking-tighter z-10">${item.type}</span>`
        : '';

    itemCard.innerHTML = `
        ${typeBadge}
        ${item.is_available
            ? '<span class="status-badge available">Available</span>'
            : '<span class="status-badge unavailable">Sold Out</span>'}
        <div class="text-5xl mb-2 text-[#800000] group-hover:scale-110 transition-transform duration-200">${item.icon}</div>
        <div class="text-center w-full">
            <p class="text-sm font-bold text-gray-800 line-clamp-2 leading-tight h-10 flex items-center justify-center">${cleanName(item.name)}</p>
            <p class="text-lg font-black text-[#800000]">${formatCurrency(item.base_price)}</p>
        </div>
        ${!item.is_available ? '<div class="absolute inset-0 bg-gray-50 bg-opacity-40 flex items-center justify-center"></div>' : ''}
    `;
    return itemCard;
}

// --- ITEM MODAL ---
function openItemModal(item) {
    selectedItemForModal = item;

    // Highlight the selected card
    const card = document.getElementById(`item-card-${item.item_id}`);
    if (card) card.classList.add('selected');

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
                            <span class="text-xs font-bold text-gray-500">+${formatCurrency(ADDON_PRICES[addon])}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        addonsHTML = '';
    }

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
            <p class="text-2xl font-black text-[#800000] mb-6">${formatCurrency(item.base_price)}</p>
            
            ${variantsHTML}
            ${addonsHTML}

            <div class="flex gap-4 w-full mt-4">
                <button onclick="closeModal()" class="flex-1 py-4 bg-[#e5e7eb] text-gray-700 font-black rounded-xl hover:bg-gray-300 transition-all active:scale-95">Cancel</button>
                <button onclick="confirmAddToCart()" class="flex-1 py-4 bg-[#800000] text-white font-black rounded-xl hover:bg-red-900 shadow-xl transition-all active:scale-95">Add to Tray</button>
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

    const selectedVariant = document.querySelector('input[name="item-variant"]:checked')?.value;

    const newItem = {
        ...selectedItemForModal,
        name: selectedVariant || selectedItemForModal.name,
        modifiers: selectedAddons,
        final_price: selectedItemForModal.base_price + addonsCost
    };

    const existingItem = cart.find(i =>
        i.item_id === newItem.item_id &&
        i.name === newItem.name &&
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
    // Remove highlights from all cards
    document.querySelectorAll('.menu-item-card.selected').forEach(el => el.classList.remove('selected'));
}

// --- Cart Actions ---
function updateCartItemQuantity(index, change) {
    cart[index].quantity += change;
    if (cart[index].quantity < 1) cart[index].quantity = 1;
    renderCart();
}

function handleQuantityInput(index, value) {
    let newQty = parseInt(value);
    if (isNaN(newQty) || newQty < 1) {
        newQty = 1;
    }
    cart[index].quantity = newQty;
    // We don't renderCart here to avoid losing focus/cursor position during typing
    // But we need to update totals
    updateTotalsOnly();
}

function updateTotalsOnly() {
    const total = calculateCartTotal();
    document.getElementById('cart-subtotal').textContent = formatCurrency(total);
    document.getElementById('cart-total').textContent = formatCurrency(total);
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
    const countEl = document.getElementById('cart-count');
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl = document.getElementById('cart-total');
    const btn = document.getElementById('place-order-btn');
    const emptyBtn = document.getElementById('empty-tray-btn');

    cartContainer.innerHTML = '';
    const total = calculateCartTotal();
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

    if (cart.length === 0) {
        if (countEl) countEl.classList.add('hidden');
        if (emptyBtn) emptyBtn.classList.add('hidden');
        cartContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <span class="text-[#800000] opacity-30 mb-2"><i class="ph-duotone ph-shopping-cart" style="font-size:40px"></i></span>
                <p class="text-sm">Your tray is empty</p>
            </div>
        `;
        btn.disabled = true;
    } else {
        if (countEl) {
            countEl.textContent = itemCount;
            countEl.classList.remove('hidden');
        }
        if (emptyBtn) emptyBtn.classList.remove('hidden');
        cart.forEach((item, index) => {
            const addonsText = item.modifiers && item.modifiers.length > 0
                ? `<p class="text-xs text-gray-500 mt-1">+ ${item.modifiers.join(', ')}</p>`
                : '';

            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item';
            itemEl.innerHTML = `
                <div class="cart-item-info">
                    <p class="cart-item-name">${cleanName(item.name)}</p>
                    ${addonsText}
                    <p class="cart-item-price">${formatCurrency(item.final_price)}</p>
                </div>
                <div class="cart-item-controls">
                    <div class="flex items-center bg-gray-50 rounded-lg p-1 mr-2 border border-gray-100">
                        <button onclick="updateCartItemQuantity(${index}, -1)" class="cart-qty-btn">-</button>
                        <input type="number" value="${item.quantity}" min="1" oninput="handleQuantityInput(${index}, this.value)" onblur="renderCart()" class="quantity-input w-8 text-center font-black text-sm bg-transparent border-none focus:outline-none focus:ring-0">
                        <button onclick="updateCartItemQuantity(${index}, 1)" class="cart-qty-btn">+</button>
                    </div>
                    <button onclick="removeItem(${index})" class="cart-qty-btn remove">
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

    subtotalEl.textContent = formatCurrency(total);
    totalEl.textContent = formatCurrency(total);
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
            <span class="font-semibold text-gray-800">${formatCurrency(item.final_price * item.quantity)}</span>
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
                order_source: 'Paws Place'
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
    fetchMenuData();
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
        window.location.href = '2_kiosk_ordering.php';
    } else if (value === 'pup-stop') {
        window.location.href = 'cafeteria_ordering.php?store=Pup+Stop&location_id=13';
    } else if (value === 'kennel-main') {
        window.location.href = 'cafeteria_ordering.php?store=Kennel+Main&location_id=1';
    } else if (value === 'kennel-north') {
        window.location.href = 'cafeteria_ordering.php?store=Kennel+North&location_id=2';
    }
}

