<?php
session_start();

// If not logged in at all, redirect to customer login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'KIOSK') {
    header('Location: customer_login.php');
    exit;
}

// Get user info from session
$userName = $_SESSION['full_name'] ?? 'Customer';
$userDept = $_SESSION['department'] ?? '';
$userId = $_SESSION['user_id'] ?? '';
$firstName = explode(' ', trim($userName))[0];
$initial = strtoupper(substr($firstName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>GrabHound - Order History</title>
    <link rel="stylesheet" href="css/store_selection.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
    <script>
        // Force disable zoom on iOS
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('gesturestart', function(event) {
            event.preventDefault();
        });
    </script>
</head>
<body>

    <div class="layout">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <div class="brand-icon"><i class="ph-duotone ph-paw-print" style="font-size:28px"></i></div>
                    <div>
                        <div class="brand-name">GrabHound</div>
                        <div class="brand-sub">Foundation University</div>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <a href="store_selection.php" class="nav-item">
                        <span class="nav-icon"><i class="ph-duotone ph-house" style="font-size:18px"></i></span>
                        <span>Dashboard</span>
                    </a>
                    <a href="order_history.php" class="nav-item active">
                        <span class="nav-icon"><i class="ph-duotone ph-clock-counter-clockwise" style="font-size:18px"></i></span>
                        <span>Order History</span>
                    </a>
                    <div class="nav-item cursor-default relative" id="nav-notifications">
                        <span class="nav-icon">
                            <i class="ph-duotone ph-bell" style="font-size:18px"></i>
                            <span id="notif-badge" class="notif-badge hidden"></span>
                        </span>
                        <span>Notifications</span>

                        <!-- Notification Panel -->
                        <div id="notif-panel" class="notif-panel">
                            <div class="notif-panel-header">
                                <span class="notif-panel-title">Notifications</span>
                                <span class="notif-panel-clear" id="notif-clear-all" onclick="event.stopPropagation(); dismissAllNotifs();">Dismiss All</span>
                            </div>
                            <div id="notif-panel-list" class="notif-panel-list">
                                <!-- Dynamic Items -->
                                <div class="notif-empty">
                                    <i class="ph-duotone ph-bell-slash"></i>
                                    <p>No active notifications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <div class="user-card">
                    <div class="user-avatar"><?php echo $initial; ?></div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($firstName); ?></div>
                        <?php if ($userDept): ?>
                            <div class="user-dept"><?php echo htmlspecialchars($userDept); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="../server/logout.php" class="logout-btn">
                    <i class="ph-duotone ph-sign-out" style="font-size:16px;vertical-align:middle"></i> Log Out
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Order History</h1>
                    <p class="page-subtitle">View your past orders and their status</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-bar" style="display: flex; gap: 12px; margin-bottom: 24px; background: #ffffff; padding: 16px; border-radius: 16px; border: 1px solid #e5e7eb; flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 200px; position: relative;">
                    <i class="ph-duotone ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                    <input type="text" id="search-code" placeholder="Search order code..." style="width: 100%; padding: 10px 10px 10px 38px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; outline: none; transition: border-color 0.2s;" oninput="applyFilters()">
                </div>
                
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px; background: #f9fafb; padding: 4px 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
                        <span style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase;">From:</span>
                        <input type="date" id="filter-date-from" style="border: none; background: transparent; font-size: 13px; color: #4b5563; outline: none; cursor: pointer;" onchange="applyFilters()">
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; background: #f9fafb; padding: 4px 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
                        <span style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase;">To:</span>
                        <input type="date" id="filter-date-to" style="border: none; background: transparent; font-size: 13px; color: #4b5563; outline: none; cursor: pointer;" onchange="applyFilters()">
                    </div>
                    <select id="filter-status" style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; color: #4b5563; background: #f9fafb; outline: none; cursor: pointer;" onchange="applyFilters()">
                        <option value="all">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Preparing">Preparing</option>
                        <option value="Ready">Ready</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>

                    <select id="filter-store" style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; color: #4b5563; background: #f9fafb; outline: none; cursor: pointer;" onchange="applyFilters()">
                        <option value="all">All Stores</option>
                        <option value="Paws Place">Paws Place</option>
                        <option value="Pup Stop">Pup Stop</option>
                        <option value="Kennel Main">Kennel Main</option>
                        <option value="Kennel North">Kennel North</option>
                    </select>
                </div>
            </div>

            <div id="full-history-list" class="recent-orders-list">
                <div class="loading-placeholder" style="padding: 60px; text-align: center; color: #9ca3af; background: #ffffff; border-radius: 20px; border: 1px dashed #e5e7eb;">
                    Fetching your complete order history...
                </div>
            </div>

        </main>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <!-- Hidden audio for notification -->
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        const userId = '<?php echo $userId; ?>';
        let allOrders = [];

        async function fetchFullHistory() {
            if (!userId) {
                console.warn('No userId found for history fetch');
                return;
            }
            try {
                const res = await fetch(`../server/api/get_student_orders.php?student_id=${encodeURIComponent(userId)}`);
                const data = await res.json();
                if (data.success && Array.isArray(data.orders)) {
                    checkNotifChanges(data.orders);
                    allOrders = data.orders;
                    applyFilters();
                }
            } catch (err) {
                console.error('Error fetching history:', err);
                const hList = document.getElementById('full-history-list');
                if (hList) hList.innerHTML = '<div class="p-8 text-center text-gray-400">Unable to load history at this time.</div>';
            }
        }

        let lastNotifStatuses = {};
        let isFirstLoadNotif = true;
        let dismissedHistoryNotifs = JSON.parse(localStorage.getItem(`dismissed_notifs_${userId}`) || '[]');

        function checkNotifChanges(orders) {
            let currentReadyOrders = [];
            orders.forEach(order => {
                const id = order.order_id;
                const status = (order.status || '').toUpperCase();
                const oldStatus = lastNotifStatuses[id];

                if (status === 'READY') {
                    currentReadyOrders.push(order);
                    if (!isFirstLoadNotif && oldStatus && oldStatus !== 'READY') {
                        console.log(`Notification Triggered (History): Order #${id} is now READY`);
                        showNotifToast(`Order #${order.final_code || order.pre_order_code} is READY!`, order.order_source);
                        playNotifSound();
                    }
                }
                lastNotifStatuses[id] = status;
            });

            const currentReadyIds = currentReadyOrders.map(o => o.order_id.toString());
            const badge = document.getElementById('notif-badge');
            const hasUndismissed = currentReadyIds.some(id => !dismissedHistoryNotifs.includes(id));
            
            if (badge) {
                if (hasUndismissed) badge.classList.remove('hidden');
                else badge.classList.add('hidden');
            }

            // Update Panel List
            updateNotifPanel(currentReadyOrders);

            dismissedHistoryNotifs = dismissedHistoryNotifs.filter(id => currentReadyIds.includes(id));
            localStorage.setItem(`dismissed_notifs_${userId}`, JSON.stringify(dismissedHistoryNotifs));
            isFirstLoadNotif = false;
        }

        function updateNotifPanel(readyOrders) {
            const list = document.getElementById('notif-panel-list');
            if (!list) return;
            const undismissed = readyOrders.filter(o => !dismissedHistoryNotifs.includes(o.order_id.toString()));

            if (undismissed.length === 0) {
                list.innerHTML = `
                    <div class="notif-empty">
                        <i class="ph-duotone ph-bell-slash"></i>
                        <p>No active notifications</p>
                    </div>
                `;
                return;
            }

            list.innerHTML = undismissed.map(o => `
                <div class="notif-item">
                    <div class="notif-item-icon"><i class="ph-duotone ph-bowl-food"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Order #${o.final_code || o.pre_order_code} is READY</div>
                        <div class="notif-item-desc">Pick up your order at <b>${o.order_source}</b></div>
                        <div class="notif-item-time">${new Date(o.time_placed).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
                    </div>
                </div>
            `).join('');
        }

        function dismissAllNotifs() {
            Object.keys(lastNotifStatuses).forEach(id => {
                if (lastNotifStatuses[id] === 'READY' && !dismissedHistoryNotifs.includes(id.toString())) {
                    dismissedHistoryNotifs.push(id.toString());
                }
            });
            localStorage.setItem(`dismissed_notifs_${userId}`, JSON.stringify(dismissedHistoryNotifs));
            const badge = document.getElementById('notif-badge');
            if (badge) badge.classList.add('hidden');
            document.getElementById('notif-panel').classList.remove('show');
            updateNotifPanel([]);
        }

        // Notification Bell Click -> Toggle Panel
        document.getElementById('nav-notifications').onclick = function(e) {
            e.stopPropagation();
            const panel = document.getElementById('notif-panel');
            if (panel) panel.classList.toggle('show');
        };

        // Close panel when clicking outside
        document.addEventListener('click', () => {
            const panel = document.getElementById('notif-panel');
            if (panel) panel.classList.remove('show');
        });
        const panelEl = document.getElementById('notif-panel');
        if (panelEl) panelEl.onclick = (e) => e.stopPropagation();

        function showNotifToast(message, store) {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="background:#800000; color:white; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="ph-duotone ph-bell-ringing"></i>
                    </div>
                    <div>
                        <div style="font-weight:900; font-size:11px; text-transform:uppercase; color:#9ca3af; letter-spacing:0.5px;">${store || 'System'}</div>
                        <div style="font-size:13px; font-weight:700;">${message}</div>
                    </div>
                </div>
            `;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 5000);
        }

        function playNotifSound() {
            const sound = document.getElementById('notif-sound');
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(e => {});
            }
        }

        // Refresh History & Check notifications every 10 seconds
        setInterval(fetchFullHistory, 10000);

        function applyFilters() {
            const status = document.getElementById('filter-status').value;
            const store = document.getElementById('filter-store').value;
            const search = document.getElementById('search-code').value.toLowerCase();
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const filtered = allOrders.filter(o => {
                const matchStatus = status === 'all' || (o.status || '').toUpperCase() === status.toUpperCase();
                const matchStore = store === 'all' || (o.order_source || 'Paws Place') === store;
                const matchSearch = String(o.final_code || o.pre_order_code).toLowerCase().includes(search);
                
                // Date filtering
                let matchDate = true;
                if (o.time_placed) {
                    const orderDate = new Date(o.time_placed).toISOString().split('T')[0];
                    if (dateFrom && orderDate < dateFrom) matchDate = false;
                    if (dateTo && orderDate > dateTo) matchDate = false;
                }

                return matchStatus && matchStore && matchSearch && matchDate;
            });

            renderFullHistory(filtered);
        }

        function renderFullHistory(orders) {
            const list = document.getElementById('full-history-list');
            if (orders.length === 0) {
                list.innerHTML = '<div class="p-12 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">No orders match your filters.</div>';
                return;
            }

            list.innerHTML = orders.map(o => {
                const statusClass = 'status-' + o.status.toLowerCase();
                const itemsList = o.items.map(i => `${i.quantity}x ${i.name}`).join(', ');
                
                return `
                    <div class="order-history-item" style="margin-bottom: 8px;">
                        <div class="order-main-info">
                            <div class="order-icon-circle"><i class="ph-duotone ph-receipt"></i></div>
                            <div class="order-details-text">
                                <div class="order-store" style="display: flex; align-items: center; gap: 8px;">
                                    ${o.order_source || 'Paws Place'}
                                    <span style="font-size: 10px; padding: 2px 6px; background: #f3f4f6; border-radius: 4px; color: #6b7280;">#${o.final_code || o.pre_order_code}</span>
                                </div>
                                <div class="order-meta">${new Date(o.time_placed).toLocaleString()}</div>
                                <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">${itemsList}</div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                            <span class="order-status-pill ${statusClass}">${o.status}</span>
                            <div class="order-amount">₱${parseFloat(o.total_amount).toFixed(2)}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Set default dates: 1st of month to Today
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            
            const firstOfMonth = `${year}-${month}-01`;
            const today = `${year}-${month}-${day}`;
            
            document.getElementById('filter-date-from').value = firstOfMonth;
            document.getElementById('filter-date-to').value = today;
            
            fetchFullHistory();
        });
    </script>
</body>
</html>
