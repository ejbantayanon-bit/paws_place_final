<?php
session_start();
// If already logged in as staff/admin, redirect to dashboard
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Cashier'])) {
    header('Location: ' . ($_SESSION['role'] === 'Admin' ? '5_adminDashboard.php' : '3_index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place - Staff Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>

    <div class="grid grid-cols-1 lg:grid-cols-4 min-h-screen">
        <div class="lg:col-span-3 cafe-context p-0">
            <div class="w-full h-full flex items-stretch">
                <div class="w-full h-full image-placeholder overflow-hidden relative">
                    <img src="../image/Paws place.jpeg" alt="Cafe" class="object-cover w-full h-full">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/40 pointer-events-none"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
                        <h1 class="text-5xl font-black text-white mb-2 drop-shadow-lg tracking-wider">FOUNDATION UNIVERSITY</h1>
                        <p class="text-2xl font-light text-white/90 tracking-wide">Paws Place Management System</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="right-panel" class="lg:col-span-1 p-8 flex flex-col justify-center items-center bg-white shadow-2xl z-10">
            <header class="text-center mb-10 w-full max-w-xs">
                <div class="text-7xl mb-4 text-maroon">👔</div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">STAFF ACCESS</h2>
                <div class="h-1 w-16 bg-maroon mx-auto mt-4 rounded-full bg-[#800000]"></div>
            </header>

            <div id="role-selection" class="space-y-4 w-full max-w-xs">
                <button onclick="handleRoleSelect('CASHIER')" class="role-button color-maroon hover:bg-red-900 w-full rounded-lg border-transparent">
                    <span class="text-lg font-bold tracking-wide">CASHIER / POS</span>
                    <span class="text-xs text-red-100 font-medium uppercase tracking-widest mt-1">Point of Sale</span>
                </button>
                
                <button onclick="handleRoleSelect('ADMIN')" class="role-button color-gray-dark hover:bg-gray-700 w-full rounded-lg border-transparent">
                    <span class="text-lg font-bold tracking-wide">ADMIN DASHBOARD</span>
                    <span class="text-xs text-gray-300 font-medium uppercase tracking-widest mt-1">Management</span>
                </button>
            </div>

            <div id="login-form-container" class="mt-8 w-full max-w-xs hidden">
                <div class="text-center mb-6">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Login Role</p>
                    <p class="text-xl font-bold text-maroon" id="selected-role"></p>
                </div>
                
                <form id="login-form" class="space-y-5">
                    <div>
                        <input type="text" id="username" name="username" placeholder="Username" class="input-field w-full placeholder-gray-400 text-gray-800" required>
                    </div>
                    <div>
                        <input type="password" id="password" name="password" placeholder="Password" class="input-field w-full placeholder-gray-400 text-gray-800" required>
                    </div>
                    <div class="pt-4">
                        <button type="submit" id="login-btn" class="login-button w-full py-4 color-maroon font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-red-900 transition-all">
                            AUTHENTICATE
                        </button>
                    </div>
                </form>
                <button onclick="resetSelection()" class="w-full mt-6 py-2 text-xs font-bold text-gray-400 hover:text-maroon transition uppercase tracking-widest">← Return to Selection</button>
            </div>
            
            <button onclick="window.location.href='1_login.php'" class="w-full mt-8 py-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-widest">← Back to Main Menu</button>
            
            <p class="text-center text-[10px] text-gray-300 mt-8 w-full max-w-xs uppercase tracking-widest">
                Authorized Personnel Only
            </p>

        </div>

    </div>

    <script src="js/staff_login.js"></script>
</body>
</html>
