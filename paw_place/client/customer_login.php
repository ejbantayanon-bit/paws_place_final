<?php
session_start();
// If already logged in as customer, redirect to ordering
if (isset($_SESSION['role']) && $_SESSION['role'] === 'KIOSK') {
    header('Location: 2_kiosk_ordering.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place - Customer Kiosk</title>
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
                        <p class="text-2xl font-light text-white/90 tracking-wide">Paws Place Cafe - Self Service</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="right-panel" class="lg:col-span-1 p-8 flex flex-col justify-center items-center bg-white shadow-2xl z-10">
            <header class="text-center mb-10 w-full max-w-xs">
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">ORDER NOW</h2>
                <div class="h-1 w-16 bg-green-600 mx-auto mt-4 rounded-full"></div>
            </header>



            <div id="mode-selection" class="space-y-4 w-full max-w-xs">
                <button onclick="handleModeSelect('ID_LOGIN')" class="role-button bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-800 w-full rounded-lg border-2 border-blue-300">
                    <span class="text-lg font-bold tracking-wide">ID NUMBER</span>
                    <span class="text-xs text-blue-700 font-medium uppercase tracking-widest mt-1">Student or Employee</span>
                </button>
                
                <button onclick="handleModeSelect('GUEST')" class="role-button bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 text-purple-800 w-full rounded-lg border-2 border-purple-300">
                    <span class="text-lg font-bold tracking-wide">GUEST</span>
                    <span class="text-xs text-purple-700 font-medium uppercase tracking-widest mt-1">Quick Order</span>
                </button>
            </div>



            <div id="id-login-container" class="mt-8 w-full max-w-xs hidden">
                <div class="text-center mb-6">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">ID Login</p>
                </div>
                
                <form id="id-login-form" class="space-y-5">
                    <div>
                        <input type="text" id="kiosk-id-number" name="kiosk-id-number" placeholder="Enter ID Number" class="input-field w-full placeholder-gray-400 text-gray-800">
                    </div>
                    
                    <!-- ID Info Display -->
                    <div id="id-info-group" class="hidden bg-gray-50 border-2 border-gray-300 rounded-lg p-4 text-center">
                        <p id="id-name-display" class="text-lg font-bold text-gray-700 mb-2"></p>
                        <p class="text-sm text-gray-600 font-medium">
                            <span class="text-xs text-gray-500 uppercase tracking-widest">Department</span><br>
                            <span id="id-department-display" class="text-gray-700 font-semibold"></span>
                        </p>
                        <p id="id-type-badge" class="mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block"></p>
                    </div>
                    
                    <div class="pt-4 space-y-3">
                        <button type="button" id="verify-id-btn" onclick="handleKioskIdLookup()" class="w-full py-4 bg-blue-600 text-white font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-blue-700 transition-all">
                            VERIFY ID
                        </button>
                        
                        <button type="submit" id="id-login-btn" class="login-button w-full py-4 bg-green-600 text-white font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-green-700 transition-all" style="display: none;">
                            START ORDERING
                        </button>
                    </div>
                </form>
                <button onclick="resetModeSelection()" class="w-full mt-6 py-2 text-xs font-bold text-gray-400 hover:text-blue-600 transition uppercase tracking-widest">← Change Mode</button>
            </div>

            <div id="guest-form-container" class="mt-8 w-full max-w-xs hidden">
                <div class="text-center mb-6">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Guest Checkout</p>
                </div>
                
                <form id="guest-form" class="space-y-4">
                    <div>
                        <input type="text" id="guest-name" name="guest-name" placeholder="Enter Your Name" class="input-field w-full placeholder-gray-400 text-gray-800" required>
                    </div>
                    <div class="pt-4">
                        <button type="submit" id="guest-login-btn" class="login-button w-full py-4 bg-purple-600 text-white font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-purple-700 transition-all">
                            START ORDERING
                        </button>
                    </div>
                </form>
                <button onclick="resetModeSelection()" class="w-full mt-6 py-2 text-xs font-bold text-gray-400 hover:text-purple-600 transition uppercase tracking-widest">← Change Mode</button>
            </div>
            
            <button onclick="window.location.href='1_login.php'" class="w-full mt-8 py-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-widest">← Back to Main Menu</button>
            
            <p class="text-center text-[10px] text-gray-300 mt-8 w-full max-w-xs uppercase tracking-widest">
                Self-Service Ordering
            </p>

        </div>

    </div>

    <script src="js/customer_login.js"></script>
</body>
</html>
