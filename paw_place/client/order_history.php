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
                    <div class="brand-icon">
                        <img src="img/lgoo.png" alt="Logo" class="object-contain">
                    </div>
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
                    <input type="text" id="search-code" placeholder="Search order code or items..." style="width: 100%; padding: 10px 10px 10px 38px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; outline: none; transition: border-color 0.2s;" oninput="applyFilters()">
                </div>
                
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
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
                        <option value="SERVED">Completed</option>
                        <option value="READY">Ready</option>
                        <option value="PREPARING">Preparing</option>
                        <option value="PENDING PAYMENT">Pending</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>

                    <select id="filter-store" style="padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; color: #4b5563; background: #f9fafb; outline: none; cursor: pointer;" onchange="applyFilters()">
                        <option value="all">All Stores</option>
                        <option value="Paws Place">Paws Place</option>
                        <option value="Pup Stop">Pup Stop</option>
                        <option value="Kennel Main">Kennel Main</option>
                        <option value="Kennel North">Kennel North</option>
                    </select>

                    <button onclick="resetFilters()" style="padding: 10px 16px; background: #f3f4f6; color: #4b5563; border-radius: 10px; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; text-transform: uppercase;">Reset</button>
                </div>
            </div>

            <div class="history-container" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f9fafb; border-bottom: 2px solid #f3f4f6;">
                        <tr>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Order ID</th>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date & Time</th>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Store</th>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Items</th>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Total</th>
                            <th style="padding: 16px 20px; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body">
                        <tr>
                            <td colspan="6" style="padding: 60px; text-align: center; color: #9ca3af;">
                                Fetching your complete order history...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 24px;">
                <!-- Populated by JS -->
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
        let filteredOrders = [];
        let currentPage = 1;
        const itemsPerPage = 10;

        function cleanName(name) {
            if (!name) return "";
            return name.replace(/\s*@\s*[\d.]+/g, '').replace(/\s*\(Hot\)/gi, '').trim();
        }

        async function fetchFullHistory() {
            if (!userId) return;
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
                document.getElementById('history-table-body').innerHTML = '<tr><td colspan="6" style="padding: 40px; text-align: center; color: #ef4444;">Unable to load history.</td></tr>';
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
                        showNotifToast(`Order #${order.final_code || order.pre_order_code} is READY!`, order.order_source);
                        playNotifSound();
                    }
                }
                lastNotifStatuses[id] = status;
            });

            const badge = document.getElementById('notif-badge');
            const currentReadyIds = currentReadyOrders.map(o => o.order_id.toString());
            const hasUndismissed = currentReadyIds.some(id => !dismissedHistoryNotifs.includes(id));
            if (badge) badge.classList.toggle('hidden', !hasUndismissed);

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
                list.innerHTML = `<div class="notif-empty"><i class="ph-duotone ph-bell-slash"></i><p>No active notifications</p></div>`;
                return;
            }

            list.innerHTML = undismissed.map(o => `
                <div class="notif-item">
                    <div class="notif-item-icon"><i class="ph-duotone ph-bowl-food"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">Order #${o.final_code || o.pre_order_code} is READY</div>
                        <div class="notif-item-desc">Pick up at <b>${o.order_source || 'Paws Place'}</b></div>
                    </div>
                </div>
            `).join('');
        }

        function dismissAllNotifs() {
            allOrders.forEach(o => {
                if ((o.status || '').toUpperCase() === 'READY') {
                    if (!dismissedHistoryNotifs.includes(o.order_id.toString())) {
                        dismissedHistoryNotifs.push(o.order_id.toString());
                    }
                }
            });
            localStorage.setItem(`dismissed_notifs_${userId}`, JSON.stringify(dismissedHistoryNotifs));
            document.getElementById('notif-badge')?.classList.add('hidden');
            updateNotifPanel([]);
        }

        document.getElementById('nav-notifications').onclick = (e) => {
            e.stopPropagation();
            document.getElementById('notif-panel').classList.toggle('show');
        };
        document.addEventListener('click', () => {
            document.getElementById('notif-panel')?.classList.remove('show');
        });

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
            if (sound) { sound.currentTime = 0; sound.play().catch(e => {}); }
        }

        function applyFilters() {
            const status = document.getElementById('filter-status').value;
            const store = document.getElementById('filter-store').value;
            const search = document.getElementById('search-code').value.toLowerCase();
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            filteredOrders = allOrders.filter(o => {
                const matchStatus = status === 'all' || (o.status || '').toUpperCase() === status.toUpperCase();
                const matchStore = store === 'all' || (o.order_source || 'Paws Place') === store;
                
                const itemsText = (o.items || []).map(i => cleanName(i.name)).join(' ').toLowerCase();
                const matchSearch = String(o.final_code || o.pre_order_code).toLowerCase().includes(search) || itemsText.includes(search);
                
                let matchDate = true;
                if (o.time_placed) {
                    const d = new Date(o.time_placed);
                    const orderDateStr = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    if (dateFrom && orderDateStr < dateFrom) matchDate = false;
                    if (dateTo && orderDateStr > dateTo) matchDate = false;
                }

                return matchStatus && matchStore && matchSearch && matchDate;
            });

            currentPage = 1;
            renderHistory();
        }

        function renderHistory() {
            const tbody = document.getElementById('history-table-body');
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageOrders = filteredOrders.slice(start, end);

            if (filteredOrders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="padding: 60px; text-align: center; color: #9ca3af;">No orders found match your filters.</td></tr>';
                renderPagination(0);
                return;
            }

            tbody.innerHTML = pageOrders.map(o => {
                const itemsList = (o.items || []).map(i => `<span style="font-weight:700;">${i.quantity}x</span> ${cleanName(i.name)}`).join(', ');
                const date = new Date(o.time_placed);
                const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                let statusColor = '#9ca3af'; let statusBg = '#f3f4f6';
                const s = (o.status || '').toUpperCase();
                if (s === 'SERVED') { statusColor = '#15803d'; statusBg = '#dcfce7'; }
                else if (s === 'READY') { statusColor = '#1d4ed8'; statusBg = '#dbeafe'; }
                else if (s === 'PREPARING') { statusColor = '#854d0e'; statusBg = '#fef9c3'; }
                else if (s === 'CANCELLED') { statusColor = '#b91c1c'; statusBg = '#fee2e2'; }

                return `
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 16px 20px;"><div style="font-weight: 800; color: #1f2937;">#${o.final_code || o.pre_order_code}</div></td>
                        <td style="padding: 16px 20px;"><div style="font-size: 13px; font-weight: 600; color: #374151;">${dateStr}</div><div style="font-size: 11px; color: #9ca3af;">${timeStr}</div></td>
                        <td style="padding: 16px 20px;"><div style="font-size: 12px; font-weight: 700; color: #800000; text-transform: uppercase;">${o.order_source || 'Paws Place'}</div></td>
                        <td style="padding: 16px 20px; max-width: 250px;"><div style="font-size: 12px; color: #4b5563; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${itemsList.replace(/<[^>]*>/g, '')}">${itemsList}</div></td>
                        <td style="padding: 16px 20px; text-align: right; font-weight: 800; color: #1f2937;">₱${parseFloat(o.total_amount).toFixed(2)}</td>
                        <td style="padding: 16px 20px; text-align: center;"><span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 9px; font-weight: 800; text-transform: uppercase; background: ${statusBg}; color: ${statusColor};">${o.status === 'PENDING PAYMENT' ? 'PENDING' : o.status}</span></td>
                    </tr>
                `;
            }).join('');

            renderPagination(Math.ceil(filteredOrders.length / itemsPerPage));
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-controls');
            if (totalPages <= 1) { container.innerHTML = ''; return; }
            let btns = `<button onclick="changePage(currentPage - 1)" ${currentPage === 1 ? 'disabled' : ''} style="padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; font-weight: 700; font-size: 11px; cursor: pointer; opacity: ${currentPage === 1 ? 0.4 : 1};">Prev</button>`;
            for (let i = 1; i <= totalPages; i++) {
                const active = i === currentPage;
                btns += `<button onclick="changePage(${i})" style="width: 32px; height: 32px; border: 1px solid ${active ? '#800000' : '#e5e7eb'}; border-radius: 8px; background: ${active ? '#800000' : 'white'}; color: ${active ? 'white' : '#4b5563'}; font-weight: 700; font-size: 11px; cursor: pointer;">${i}</button>`;
            }
            btns += `<button onclick="changePage(currentPage + 1)" ${currentPage === totalPages ? 'disabled' : ''} style="padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; font-weight: 700; font-size: 11px; cursor: pointer; opacity: ${currentPage === totalPages ? 0.4 : 1};">Next</button>`;
            container.innerHTML = btns;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredOrders.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page; renderHistory(); window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetFilters() {
            const now = new Date(); const year = now.getFullYear(); const month = String(now.getMonth() + 1).padStart(2, '0'); const day = String(now.getDate()).padStart(2, '0');
            document.getElementById('filter-date-from').value = `${year}-${month}-01`;
            document.getElementById('filter-date-to').value = `${year}-${month}-${day}`;
            document.getElementById('filter-status').value = 'all'; document.getElementById('filter-store').value = 'all'; document.getElementById('search-code').value = '';
            applyFilters();
        }

        document.addEventListener('DOMContentLoaded', () => { resetFilters(); setInterval(fetchFullHistory, 15000); });
    </script>
</body>
</html>
