<?php
session_start();
// If already logged in as staff, redirect accordingly
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'Admin') { header('Location: 5_adminDashboard.php'); exit; }
    if ($role === 'Cashier') { header('Location: 3_index.php'); exit; }
}

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
    <title>GrabHound - Dashboard</title>
    <link rel="stylesheet" href="css/store_selection.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
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
                    <a href="store_selection.php" class="nav-item active">
                        <span class="nav-icon"><i class="ph-duotone ph-house" style="font-size:18px"></i></span>
                        <span>Dashboard</span>
                    </a>
                    <a href="order_history.php" class="nav-item">
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
            <div class="page-header flex justify-between items-center w-full">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-800 tracking-tight">Welcome, <?php echo htmlspecialchars($firstName); ?>!</h1>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium">Select a store to start ordering</p>
                </div>
                <div class="header-meta flex items-center gap-4">
                    <span class="current-date" id="current-date"></span>
                    <button class="menu-toggle-btn" id="menu-toggle">
                        <i class="ph-bold ph-list"></i>
                    </button>
                </div>
            </div>


            <!-- Location Section -->
            <div class="section-header">
                <h2 class="section-title">Locations</h2>
            </div>

            <div class="store-grid">

                <!-- Paws Place (Active) -->
                <div class="store-card active" onclick="window.location.href='2_kiosk_ordering.php'">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
                    </div>
                    <div class="card-name">Paws Place</div>
                    <div class="card-action">Order Now →</div>
                </div>

                <!-- Pup Stop (Cafeteria API) -->
                <div class="store-card active" onclick="window.location.href='cafeteria_ordering.php?store=Pup+Stop'">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                    </div>
                    <div class="card-name">Pup Stop</div>
                    <div class="card-action">Order Now →</div>
                </div>

                <!-- Kennel Main (Cafeteria API) -->
                <div class="store-card active" onclick="window.location.href='cafeteria_ordering.php?store=Kennel+Main'">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h.01"/><path d="M9 12h.01"/><path d="M9 15h.01"/><path d="M9 18h.01"/></svg>
                    </div>
                    <div class="card-name">Kennel Main</div>
                    <div class="card-action">Order Now →</div>
                </div>

                <!-- Kennel North (Cafeteria API) -->
                <div class="store-card active" onclick="window.location.href='cafeteria_ordering.php?store=Kennel+North'">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="card-name">Kennel North</div>
                    <div class="card-action">Order Now →</div>
                </div>

            </div>


        </main>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <!-- Hidden audio for notification -->
    <audio id="notif-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

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

        // Set current date
        const dateEl = document.getElementById('current-date');
        if (dateEl) {
            const now = new Date();
            dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        async function initCafeteriaLocations() {
            try {
                const res = await fetch('../server/api/get_cafeteria_locations.php');
                const data = await res.json();
                console.log('Cafeteria locations:', data);

                if (data.success && Array.isArray(data.items)) {
                    const locations = data.items;
                    
                    // Map display names to API locations using the exact names from data
                    const mapping = {
                        'Pup Stop': locations.find(l => l.id == "13" || l.name.toLowerCase().includes('pupstop')),
                        'Kennel Main': locations.find(l => l.id == "1" || l.name.toLowerCase().includes('main cafeteria')),
                        'Kennel North': locations.find(l => l.id == "2" || (l.name.toLowerCase().includes('north cafeteria') && !l.name.toLowerCase().includes('pupstop')))
                    };

                    // Update store cards
                    document.querySelectorAll('.store-card').forEach(card => {
                        const name = card.querySelector('.card-name').textContent.trim();
                        const location = mapping[name];
                        if (location) {
                            card.onclick = () => {
                                window.location.href = `cafeteria_ordering.php?store=${encodeURIComponent(name)}&location_id=${encodeURIComponent(location.id)}`;
                            };
                            card.classList.add('active'); // Ensure they are active
                            card.style.opacity = "1";
                            card.style.pointerEvents = "auto";
                        } else if (name !== 'Paws Place') {
                            // If location is missing for a cafeteria store, maybe show as coming soon
                            // card.style.opacity = "0.5";
                            // card.style.pointerEvents = "none";
                        }
                    });
                }
            } catch (err) {
                console.error('Error fetching locations:', err);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCafeteriaLocations();
            initDashboardData();
        });

        const userId = '<?php echo $userId; ?>';
        let lastOrderStatuses = {}; // Track [order_id]: status
        let isFirstLoad = true;
        let dismissedReadyOrders = JSON.parse(localStorage.getItem(`dismissed_notifs_${userId}`) || '[]');

        async function initDashboardData() {
            if (!userId) return;
            try {
                const res = await fetch(`../server/api/get_student_orders.php?student_id=${encodeURIComponent(userId)}`);
                const data = await res.json();
                if (data.success && Array.isArray(data.orders)) {
                    checkStatusChanges(data.orders);
                    renderDashboard(data.orders);
                    isFirstLoad = false;
                }
            } catch (err) {
                console.error('Error fetching orders:', err);
                const rList = document.getElementById('recent-orders-list');
                if (rList) rList.innerHTML = '<div class="text-xs text-gray-400 p-4">History unavailable</div>';
            }
        }

        function checkStatusChanges(orders) {
            let currentReadyOrders = [];
            
            orders.forEach(order => {
                const id = order.order_id;
                const status = (order.status || '').toUpperCase();
                const oldStatus = lastOrderStatuses[id];

                if (status === 'READY') {
                    currentReadyOrders.push(order);
                    // Trigger alert ONLY if it's a new transition to Ready
                    if (!isFirstLoad && oldStatus && oldStatus !== 'READY') {
                        console.log(`Notification Triggered: Order #${id} is now READY`);
                        showNotificationToast(`Order #${order.final_code || order.pre_order_code} is READY!`, order.order_source);
                        playNotifSound();
                    }
                }
                
                lastOrderStatuses[id] = status;
            });

            const currentReadyIds = currentReadyOrders.map(o => o.order_id.toString());
            const hasUndismissedReady = currentReadyIds.some(id => !dismissedReadyOrders.includes(id));
            const badge = document.getElementById('notif-badge');
            
            if (hasUndismissedReady) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            // Update Panel List
            updateNotifPanel(currentReadyOrders);

            // Cleanup dismissed list
            dismissedReadyOrders = dismissedReadyOrders.filter(id => currentReadyIds.includes(id));
            localStorage.setItem(`dismissed_notifs_${userId}`, JSON.stringify(dismissedReadyOrders));
        }

        function updateNotifPanel(readyOrders) {
            const list = document.getElementById('notif-panel-list');
            const undismissed = readyOrders.filter(o => !dismissedReadyOrders.includes(o.order_id.toString()));

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
            Object.keys(lastOrderStatuses).forEach(id => {
                if (lastOrderStatuses[id] === 'READY' && !dismissedReadyOrders.includes(id.toString())) {
                    dismissedReadyOrders.push(id.toString());
                }
            });
            localStorage.setItem(`dismissed_notifs_${userId}`, JSON.stringify(dismissedReadyOrders));
            document.getElementById('notif-badge').classList.add('hidden');
            document.getElementById('notif-panel').classList.remove('show');
            updateNotifPanel([]);
        }

        // Notification Bell Click -> Toggle Panel
        document.getElementById('nav-notifications').onclick = function(e) {
            e.stopPropagation();
            const panel = document.getElementById('notif-panel');
            panel.classList.toggle('show');
        };

        // Close panel when clicking outside
        document.addEventListener('click', () => {
            document.getElementById('notif-panel').classList.remove('show');
        });
        document.getElementById('notif-panel').onclick = (e) => e.stopPropagation();

        function showNotificationToast(message, store) {
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
                sound.play().catch(e => console.log('Sound blocked by browser'));
            }
        }

        
        // Start Polling (every 10 seconds)
        setInterval(initDashboardData, 10000);

        function renderDashboard(orders) {
            // Stats and active orders sections removed per user request
        }

        function showComingSoon() {
            const toast = document.getElementById('toast');
            toast.textContent = 'This store is coming soon!';
            toast.classList.add('show');
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }
    </script>
    <script>
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const layout = document.querySelector('.layout');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', (e) => {
                if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && e.target !== menuToggle) {
                    sidebar.classList.remove('active');
                }
            });

            // Prevent clicks inside sidebar from closing it
            sidebar.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    </script>
</body>
</html>
