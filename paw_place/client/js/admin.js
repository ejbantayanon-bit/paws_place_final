// --- API BASE URL ---
const API_BASE = '../server/api';

function exportSalesHistory() {
    // Get current filter values directly from the Sales History view
    const dateFrom = document.getElementById('history-date-from').value;
    const dateTo = document.getElementById('history-date-to').value;
    const status = document.getElementById('history-status').value;
    const search = document.getElementById('history-search').value;

    let url = '../server/api/export_sales.php';
    const params = [];

    if (dateFrom) params.push(`start_date=${dateFrom}`);
    if (dateTo) params.push(`end_date=${dateTo}`);
    if (status) params.push(`status=${status}`);
    if (search) params.push(`search=${encodeURIComponent(search)}`);

    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    // Trigger download
    window.location.href = url;
}

// --- STATE ---
let currentView = 'dashboard';

// --- UTILITY FUNCTIONS ---
function showAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `p-4 rounded-lg shadow-lg text-white ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} mb-2`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);
}

function switchView(viewName) {
    currentView = viewName;
    document.querySelectorAll('.view-section').forEach(v => v.classList.add('hidden'));
    document.getElementById(`view-${viewName}`).classList.remove('hidden');

    document.querySelectorAll('.sidebar-link').forEach(b => b.classList.remove('active'));
    document.getElementById(`nav-${viewName}`).classList.add('active');

    const titles = {
        dashboard: { title: 'Dashboard Overview', subtitle: 'Welcome back, Admin' },
        menu: { title: 'Menu Management', subtitle: 'Manage menu items and categories' },
        categories: { title: 'Category Management', subtitle: 'Create and manage item categories' },
        history: { title: 'Sales History', subtitle: 'View and export past transactions' },
        inventory: { title: 'Inventory Management', subtitle: 'Track and adjust stock levels' },
        employees: { title: 'Employee Management', subtitle: 'Manage user access and roles' },
        logs: { title: 'System Activity Logs', subtitle: 'View system audit trails' },
        settings: { title: 'System Settings', subtitle: 'Configure global system parameters' }
    };

    const currentTitle = titles[viewName];
    if (currentTitle) {
        document.getElementById('page-title').textContent = currentTitle.title;
        document.getElementById('page-subtitle').textContent = currentTitle.subtitle;
    }

    if (viewName === 'dashboard') loadDashboard();
    if (viewName === 'menu') loadMenuItems();
    if (viewName === 'categories') loadCategories();
    if (viewName === 'history') loadSalesHistory();
    if (viewName === 'inventory') loadInventory();
    if (viewName === 'employees') loadUsers();
    if (viewName === 'logs') loadLogs();
    if (viewName === 'settings') loadSettings();
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
            if (typeof action.onclick === 'function') {
                action.onclick();
            } else if (typeof action.onclick === 'string') {
                const funcName = action.onclick.replace('()', '');
                if (window[funcName]) {
                    window[funcName]();
                } else {
                    eval(action.onclick);
                }
            }
        });

        actionsContainer.appendChild(btn);
    });

    document.getElementById('modal-overlay').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.add('hidden');
}

function logout() {
    // Show confirmation modal instead of browser confirm
    showModal('Logout Confirmation', 'Are you sure you want to logout? You will return to the login screen.', [
        { text: 'Cancel', onclick: 'closeModal()' },
        { text: 'Logout', onclick: 'confirmLogout()', style: 'bg-red-600 text-white hover:bg-red-700' }
    ]);
}

function confirmLogout() {
    window.location.href = '../server/logout.php';
}

// --- DASHBOARD VIEW ---
async function loadDashboard() {
    loadAnalytics();
    try {
        const [ordersRes] = await Promise.all([
            fetch(`${API_BASE}/get_orders.php`)
        ]);

        if (!ordersRes.ok) throw new Error('Failed to load dashboard');

        const ordersData = await ordersRes.json();
        const orders = ordersData.orders || [];

        // Calculate stats
        const totalSales = orders.reduce((sum, o) => sum + parseFloat(o.total_amount), 0);

        document.getElementById('stat-sales').textContent = formatCurrency(totalSales);
        document.getElementById('stat-orders').textContent = orders.length;

        // Render recent orders
        const tbody = document.getElementById('dashboard-orders');
        tbody.innerHTML = orders.slice(0, 10).map(order => `
            <tr class="border-b hover:bg-white transition">
                <td class="p-4 font-bold text-maroon">#${order.pre_order_code}</td>
                <td class="p-4">${order.order_items.length} items</td>
                <td class="p-4 font-bold">${formatCurrency(order.total_amount)}</td>
                <td class="p-4"><span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-800">${order.status}</span></td>
                <td class="p-4 text-gray-500">${new Date(order.created_at || order.time_placed).toLocaleTimeString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading dashboard:', error);
        showAlert('Failed to load dashboard', 'error');
    }
}

// --- MENU MANAGEMENT ---
async function loadMenuItems() {
    try {
        const response = await fetch(`${API_BASE}/get_menu_items.php?include_hidden=1`);
        if (!response.ok) throw new Error('Failed to load menu items');

        const data = await response.json();
        const items = data.items || [];
        const grid = document.getElementById('menu-grid');

        grid.innerHTML = items.map(item => `
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 relative group">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-gray-800">${item.name}</h4>
                    <span class="text-xs font-bold px-2 py-1 rounded ${item.is_available == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${item.is_available == 1 ? 'ACTIVE' : 'INACTIVE'}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-1">Cat: ${item.category_id}</p>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${item.description || 'No description'}</p>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-lg font-black text-maroon">${formatCurrency(item.base_price)}</span>
                    <button onclick='openEditMenuModal(${JSON.stringify(item)})' class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">Edit</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading menu items:', error);
        showAlert('Failed to load menu items', 'error');
    }
}

function openAddMenuModal() {
    showMenuModal('Add New Item', null);
}

function openEditMenuModal(item) {
    showMenuModal('Edit Item', item);
}

async function showMenuModal(title, item = null) {
    const isEdit = !!item;

    let categories = [];
    try {
        const response = await fetch(`${API_BASE}/get_categories.php`);
        const data = await response.json();
        categories = data.categories || [];
    } catch (e) {
        console.error("Failed to load categories for modal", e);
    }

    const categoryOptions = categories.map(cat =>
        `<option value="${cat.category_id}" ${item && item.category_id == cat.category_id ? 'selected' : ''}>${cat.name}</option>`
    ).join('');

    // Create form HTML
    const formHtml = `
        <form id="menu-form" class="space-y-4">
            ${isEdit ? `<input type="hidden" name="item_id" value="${item.item_id}">` : ''}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Item Name</label>
                <input type="text" name="name" value="${item ? item.name : ''}" class="w-full p-2 border rounded" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Price</label>
                    <input type="number" step="0.01" name="price" value="${item ? item.base_price : ''}" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full p-2 border rounded" required>
                        <option value="" disabled ${!item ? 'selected' : ''}>Select Category</option>
                        ${categoryOptions}
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                <textarea name="description" class="w-full p-2 border rounded" rows="2">${item ? item.description : ''}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Image URL</label>
                <input type="text" name="image_url" value="${item ? item.image_url : ''}" class="w-full p-2 border rounded" placeholder="assets/images/...">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_available" name="is_available" value="1" ${!item || item.is_available == 1 ? 'checked' : ''}>
                <label for="is_available" class="text-sm font-bold text-gray-700">Available for Order</label>
            </div>
        </form>
    `;

    showModal(title, '', [
        { text: 'Cancel', onclick: 'closeModal()' },
        {
            text: isEdit ? 'Save Changes' : 'Add Item',
            style: 'bg-maroon text-white hover:bg-red-900',
            onclick: () => submitMenuForm(isEdit)
        }
    ]);

    // Inject form into modal message area (hacky but works with existing modal)
    document.getElementById('modal-message').innerHTML = formHtml;
}

async function submitMenuForm(isEdit) {
    const form = document.getElementById('menu-form');
    // Manual extraction because FormData checks validity
    const name = form.name.value;
    const price = form.price.value;
    const category_id = form.category_id.value;

    if (!name || !price || !category_id) {
        alert('Please fill in all required fields');
        return;
    }

    const data = {
        name: name,
        price: price,
        category_id: category_id,
        description: form.description.value,
        image_url: form.image_url.value,
        is_available: form.is_available.checked ? 1 : 0
    };

    if (isEdit) {
        data.item_id = form.item_id.value;
    }

    const endpoint = isEdit ? 'update_menu_item.php' : 'add_menu_item.php';

    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            loadMenuItems(); // Refresh grid
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving item:', error);
        alert('Failed to save item');
    }
}




// --- CATEGORY MANAGEMENT ---
async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE}/get_categories.php`);
        if (!response.ok) throw new Error('Failed to load categories');

        const data = await response.json();
        const categories = data.categories || [];
        const tbody = document.getElementById('categories-body');

        tbody.innerHTML = categories.map(cat => `
            <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                <td class="p-4 text-gray-800 font-bold">${cat.category_id}</td>
                <td class="p-4 text-gray-800">${cat.name}</td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded text-xs font-bold ${cat.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${cat.is_active == 1 ? 'ACTIVE' : 'INACTIVE'}
                    </span>
                </td>
                <td class="p-4 text-center flex justify-center gap-2">
                    <button onclick='openEditCategoryModal(${JSON.stringify(cat)})' class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">Edit</button>
                    <button onclick="deleteCategory(${cat.category_id})" class="px-3 py-1 bg-red-100 hover:bg-red-200 rounded text-xs font-bold text-red-700">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading categories:', error);
        showAlert('Failed to load categories', 'error');
    }
}

function openAddCategoryModal() {
    showCategoryModal('Add New Category', null);
}

function openEditCategoryModal(category) {
    showCategoryModal('Edit Category', category);
}

function showCategoryModal(title, category = null) {
    const isEdit = !!category;

    const formHtml = `
        <form id="category-form" class="space-y-4">
            ${isEdit ? `<input type="hidden" name="category_id" value="${category.category_id}">` : ''}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Category Name</label>
                <input type="text" name="name" value="${category ? category.name : ''}" class="w-full p-2 border rounded" required>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="cat_is_active" name="is_active" value="1" ${!category || category.is_active == 1 ? 'checked' : ''}>
                <label for="cat_is_active" class="text-sm font-bold text-gray-700">Active</label>
            </div>
        </form>
    `;

    showModal(title, '', [
        { text: 'Cancel', onclick: 'closeModal()' },
        {
            text: isEdit ? 'Save Changes' : 'Add Category',
            style: 'bg-maroon text-white hover:bg-red-900',
            onclick: () => submitCategoryForm(isEdit)
        }
    ]);

    document.getElementById('modal-message').innerHTML = formHtml;
}

async function submitCategoryForm(isEdit) {
    const form = document.getElementById('category-form');
    const name = form.name.value;

    if (!name) {
        alert('Please enter a category name');
        return;
    }

    const data = {
        name: name,
        is_active: form.is_active.checked ? 1 : 0
    };

    if (isEdit) {
        data.category_id = form.category_id.value;
    }

    const endpoint = isEdit ? 'update_category.php' : 'add_category.php';

    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            method: 'POST',
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            loadCategories();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving category:', error);
        alert('Failed to save category');
    }
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;

    try {
        const response = await fetch(`${API_BASE}/delete_category.php`, {
            method: 'POST',
            body: JSON.stringify({ category_id: id })
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            loadCategories();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        alert('Failed to delete category');
    }
}



// --- SALES HISTORY (Aligned with Staff POS) ---
let allHistoryOrders = [];
let filteredHistoryOrders = [];
let currentHistoryPage = 1;
const itemsPerPage = 10;

async function loadSalesHistory() {
    // Fetch a large number of records to allow client-side filtering
    // If performance becomes an issue, we'll switch to server-side filtering
    let url = `${API_BASE}/get_sales_history.php?limit=1000`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to load history');

        const data = await response.json();
        allHistoryOrders = data.orders || [];
        filteredHistoryOrders = allHistoryOrders;

        // Initial filter application if inputs have values
        filterHistory();
    } catch (error) {
        console.error('Error loading history:', error);
        showAlert('Failed to load sales history', 'error');
    }
}

function renderHistoryPage() {
    const startIndex = (currentHistoryPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageOrders = filteredHistoryOrders.slice(startIndex, endIndex);

    const tbody = document.getElementById('history-table-body');

    if (pageOrders.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-gray-500">No records found.</td></tr>`;
        updatePaginationControls();
        return;
    }

    tbody.innerHTML = pageOrders.map(order => {
        // Format items list similar to Staff POS
        // Note: get_sales_history.php currently returns item_count but maybe not full items details?
        // Checking get_sales_history.php: it returns item_count but NOT order_items array yet.
        // Needs update in get_sales_history.php to return items if we want to show them!
        // For now, consistent with previous Admin view, we show item count. 
        // BUT Staff POS shows item names. I should probably update get_sales_history.php to return items.
        // I will assume for now I will update PHP next.

        const itemsList = order.order_items
            ? order.order_items.map(i => `${i.name}×${i.quantity}`).join(', ')
            : `${order.item_count} items`;

        return `
            <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                <td class="p-4 text-gray-800 font-bold text-xs">
                    #${order.pre_order_code || order.order_id} 
                    ${order.customer_name ? `<br><span class='text-xs text-gray-500 font-normal'>${order.customer_name}</span>` : ''}
                </td>
                <td class="p-4 text-gray-600 text-xs">${new Date(order.time_placed).toLocaleString()}</td>
                 <td class="p-4 text-gray-800 text-xs font-bold uppercase">${order.order_source || 'POS'}</td>
                <td class="p-4 text-gray-600 text-sm">${itemsList}</td>
                <td class="p-4 text-right text-gray-800 font-bold">${formatCurrency(order.total_amount)}</td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded text-xs font-bold ${getStatusColor(order.status)}">
                        ${order.status}
                    </span>
                </td>
            </tr>
        `;
    }).join('');

    updatePaginationControls();
}

function updatePaginationControls() {
    const totalPages = Math.ceil(filteredHistoryOrders.length / itemsPerPage);
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const numbersContainer = document.getElementById('pagination-numbers');

    if (prevBtn) prevBtn.disabled = currentHistoryPage === 1 || totalPages === 0;
    if (nextBtn) nextBtn.disabled = currentHistoryPage === totalPages || totalPages === 0;

    if (numbersContainer) {
        numbersContainer.innerHTML = '';
        // Simple pagination: show current, prev, next, first, last logic can be complex.
        // Showing max 5 buttons logic:
        let startPage = Math.max(1, currentHistoryPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);

        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-2 rounded-lg font-bold ${i === currentHistoryPage ? 'bg-maroon text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'}`;
            btn.onclick = () => goToPage(i);
            numbersContainer.appendChild(btn);
        }
    }
}

function previousPage() {
    if (currentHistoryPage > 1) {
        currentHistoryPage--;
        renderHistoryPage();
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredHistoryOrders.length / itemsPerPage);
    if (currentHistoryPage < totalPages) {
        currentHistoryPage++;
        renderHistoryPage();
    }
}

function goToPage(pageNum) {
    currentHistoryPage = pageNum;
    renderHistoryPage();
}

function filterHistory() {
    const dateFrom = document.getElementById('history-date-from').value;
    const dateTo = document.getElementById('history-date-to').value;
    const status = document.getElementById('history-status').value;
    const searchProduct = document.getElementById('history-search').value.toLowerCase();

    filteredHistoryOrders = allHistoryOrders.filter(order => {
        // Date filtering
        if (dateFrom) {
            // Convert formatted string to compare
            const orderDate = new Date(order.time_placed).toISOString().split('T')[0];
            if (orderDate < dateFrom) return false;
        }

        if (dateTo) {
            const orderDate = new Date(order.time_placed).toISOString().split('T')[0];
            if (orderDate > dateTo) return false;
        }

        // Status filtering
        if (status && order.status !== status) return false;

        // Product search (requires order_items) or Customer Name search
        if (searchProduct) {
            const inCustomer = (order.customer_name || '').toLowerCase().includes(searchProduct);
            const inId = (String(order.pre_order_code || '')).toLowerCase().includes(searchProduct);
            // Search in items if available
            const inItems = order.order_items && order.order_items.some(item => item.name.toLowerCase().includes(searchProduct));

            if (!inCustomer && !inId && !inItems) return false;
        }

        return true;
    });

    currentHistoryPage = 1;
    renderHistoryPage();
}

function resetFilter() {
    document.getElementById('history-date-from').value = '';
    document.getElementById('history-date-to').value = '';
    document.getElementById('history-status').value = '';
    document.getElementById('history-search').value = '';
    filterHistory();
}

function getStatusColor(status) {
    switch (status) {
        case 'COMPLETED':
        case 'SERVED': return 'bg-green-100 text-green-800';
        case 'CANCELLED': return 'bg-red-100 text-red-800';
        case 'PENDING': return 'bg-yellow-100 text-yellow-800';
        case 'PREPARING': return 'bg-yellow-50 text-yellow-600';
        case 'READY': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// --- EMPLOYEE MANAGEMENT ---
async function loadUsers() {
    try {
        const response = await fetch(`${API_BASE}/get_users.php`);
        if (!response.ok) throw new Error('Failed to load users');

        const data = await response.json();
        const users = data.users || [];
        const tbody = document.getElementById('employees-body');

        tbody.innerHTML = users.map(user => `
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 font-bold text-gray-800">${user.full_name}</td>
                <td class="p-4 text-gray-600">${user.username}</td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-800">
                        ${user.role}
                    </span>
                </td>
                <td class="p-4 text-center text-gray-500 text-xs">${new Date(user.created_at).toLocaleDateString()}</td>
                <td class="p-4 text-center flex justify-center gap-2">
                    <button onclick='openEditUserModal(${JSON.stringify(user)})' class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">Edit</button>
                    ${user.role !== 'Admin' ? `<button onclick="deleteUser(${user.user_id})" class="px-3 py-1 bg-red-100 hover:bg-red-200 rounded text-xs font-bold text-red-700">Delete</button>` : ''}
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading users:', error);
        showAlert('Failed to load users', 'error');
    }
}

function openAddUserModal() {
    showUserModal('Add New Employee', null);
}

function openEditUserModal(user) {
    showUserModal('Edit Employee', user);
}

function showUserModal(title, user = null) {
    const isEdit = !!user;

    const formHtml = `
        <form id="user-form" class="space-y-4">
            ${isEdit ? `<input type="hidden" name="user_id" value="${user.user_id}">` : ''}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" value="${user ? user.full_name : ''}" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="${user ? user.username : ''}" ${isEdit ? 'disabled class="w-full p-2 border rounded bg-gray-100"' : 'class="w-full p-2 border rounded"'} required>
            </div>
            ${!isEdit ? `
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>` : `
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="w-full p-2 border rounded" placeholder="********">
            </div>`}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full p-2 border rounded">
                    <option value="Cashier" ${user && user.role === 'Cashier' ? 'selected' : ''}>Cashier</option>
                    <option value="Barista" ${user && user.role === 'Barista' ? 'selected' : ''}>Barista</option>
                    <option value="Admin" ${user && user.role === 'Admin' ? 'selected' : ''}>Admin</option>
                </select>
            </div>
        </form>
    `;

    showModal(title, '', [
        { text: 'Cancel', onclick: 'closeModal()' },
        {
            text: isEdit ? 'Save Changes' : 'Create User',
            style: 'bg-maroon text-white hover:bg-red-900',
            onclick: () => submitUserForm(isEdit)
        }
    ]);

    document.getElementById('modal-message').innerHTML = formHtml;
}

async function submitUserForm(isEdit) {
    const form = document.getElementById('user-form');
    const full_name = form.full_name.value;
    const role = form.role.value;
    const password = form.password.value;

    if (!full_name || (!isEdit && !password)) {
        alert('Please fill in all required fields');
        return;
    }

    const data = {
        full_name: full_name,
        role: role,
        password: password
    };

    if (isEdit) {
        data.user_id = form.user_id.value;
    } else {
        data.username = form.username.value;
    }

    const endpoint = isEdit ? 'update_user.php' : 'add_user.php';

    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            method: 'POST',
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            loadUsers();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving user:', error);
        alert('Failed to save user');
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;

    try {
        const response = await fetch(`${API_BASE}/delete_user.php`, {
            method: 'POST',
            body: JSON.stringify({ user_id: userId })
        });

        const result = await response.json();
        if (result.success) {
            showAlert(result.message, 'success');
            loadUsers();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        alert('Failed to delete user');
    }
}


// --- ACTIVITY LOGS ---
async function loadLogs() {
    try {
        const response = await fetch(`${API_BASE}/get_activity_logs.php?limit=100`);
        if (!response.ok) throw new Error('Failed to load logs');

        const data = await response.json();
        const logs = data.logs || [];
        const tbody = document.getElementById('logs-body');

        tbody.innerHTML = logs.map(log => `
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-gray-400 font-mono text-xs">#${log.log_id}</td>
                <td class="p-4 font-bold text-gray-800">
                    ${log.user_display || 'System'}
                    <br><span class="text-xs font-normal text-gray-500">${log.user_role}</span>
                </td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                        ${log.activity_type}
                    </span>
                </td>
                <td class="p-4 text-gray-600">${log.description}</td>
                <td class="p-4 text-gray-500 text-xs">${new Date(log.created_at).toLocaleString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading logs:', error);
        showAlert('Failed to load logs', 'error');
    }
}






async function loadAnalytics() {
    try {
        const range = document.getElementById('sales-range').value || '7days';
        const response = await fetch(`${API_BASE}/get_analytics.php?range=${range}`);
        if (!response.ok) throw new Error('Failed to load analytics');

        const data = await response.json();

        // Destroy existing charts if they exist
        if (window.salesChartInstance) window.salesChartInstance.destroy();
        if (window.topItemsChartInstance) window.topItemsChartInstance.destroy();

        // 1. Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        window.salesChartInstance = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: data.sales_data.map(d => d.date),
                datasets: [{
                    label: 'Sales (₱)',
                    data: data.sales_data.map(d => d.total),
                    borderColor: '#800000', // Maroon
                    backgroundColor: 'rgba(128, 0, 0, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // 2. Top Items Chart
        const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
        window.topItemsChartInstance = new Chart(topItemsCtx, {
            type: 'bar',
            indexAxis: 'y',
            data: {
                labels: data.top_items.map(d => d.name),
                datasets: [{
                    label: 'Quantity Sold',
                    data: data.top_items.map(d => d.count),
                    backgroundColor: [
                        '#800000',
                        '#A04040',
                        '#C08080',
                        '#E0C0C0',
                        '#F0E0E0'
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

// --- INITIALIZATION ---
formatDate();
loadDashboard();
