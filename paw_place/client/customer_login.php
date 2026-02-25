<?php
session_start();
// If already logged in as customer, redirect to ordering
if (isset($_SESSION['role']) && $_SESSION['role'] === 'KIOSK') {
    header('Location: store_selection.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrabHound - Customer Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css">
</head>
<body>

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>

    <div class="grid grid-cols-1 lg:grid-cols-5 min-h-screen">
        <div class="lg:col-span-3 cafe-context p-0">
            <div class="w-full h-full flex items-stretch">
                <div class="w-full h-full image-placeholder overflow-hidden relative">
                    <img src="../image/Paws place.jpeg" alt="Cafe" class="object-cover w-full h-full">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/40 pointer-events-none"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
                        <h1 class="text-5xl font-black text-white mb-2 drop-shadow-lg tracking-wider">FOUNDATION UNIVERSITY</h1>
                        <p class="text-2xl font-light text-white/90 tracking-wide">GrabHound - Self Order Kiosk</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="right-panel" class="lg:col-span-2 p-10 flex flex-col justify-center items-center bg-white shadow-2xl z-10">
            <header class="text-center mb-10 w-full max-w-sm">
                <div class="text-maroon mb-4"><i class="ph-duotone ph-shopping-cart" style="font-size:64px"></i></div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight uppercase">CUSTOMER ACCESS</h2>
                <div class="h-1 w-16 bg-maroon mx-auto mt-4 rounded-full bg-[#800000]"></div>
            </header>

            <div id="login-container" class="w-full max-w-sm">
                <form id="student-login-form" class="space-y-5">
                    <div>
                        <input type="text" id="student-id" name="student-id" placeholder="ID Number" 
                            class="input-field w-full placeholder-gray-400 text-gray-800" required>
                    </div>
                    <div class="relative">
                        <input type="password" id="student-password" name="student-password" placeholder="Password" 
                            class="input-field w-full placeholder-gray-400 text-gray-800 pr-12" required>
                        <button type="button" onclick="togglePassword()" id="toggle-pw-btn"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1"
                            tabindex="-1" title="Show password">
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-closed" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Verified User Info (hidden until verified) -->
                    <div id="id-info-group" class="hidden bg-gray-50 border-2 border-gray-300 rounded-lg p-4 text-center">
                        <p id="id-name-display" class="text-lg font-bold text-gray-700 mb-1"></p>
                        <p class="text-sm text-gray-600 font-medium">
                            <span class="text-xs text-gray-500 uppercase tracking-widest">Department</span><br>
                            <span id="id-department-display" class="text-gray-700 font-semibold"></span>
                        </p>
                        <p id="id-type-badge" class="mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block"></p>
                    </div>

                    <div class="pt-4 space-y-3">
                        <button type="submit" id="login-btn" 
                            class="login-button w-full py-4 color-maroon font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-red-900 transition-all uppercase">
                            AUTHENTICATE
                        </button>
                        
                        <button type="button" id="start-ordering-btn" onclick="proceedToKiosk()" 
                            class="hidden w-full py-4 bg-green-600 text-white font-black tracking-widest text-sm rounded-lg shadow-lg hover:bg-green-700 transition-all uppercase">
                            START ORDERING
                        </button>
                        
                        <div class="text-center">
                            <button type="button" onclick="toggleHelpdeskBox()" 
                                class="text-xs font-bold text-gray-400 hover:text-maroon transition uppercase tracking-widest">
                                Forgot Password?
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Inline Helpdesk Message Box (hidden by default) -->
                <div id="helpdesk-box" class="hidden mt-5 bg-red-50 border border-red-200 rounded-lg px-6 py-4 text-center">
                    <p class="text-sm text-red-500 leading-relaxed">
                        If you forgot your FU Email Address, email us at<br>
                        <strong class="text-red-700">helpdesk@foundationu.com.</strong>
                    </p>
                </div>

                <!-- Guest Login Divider -->
                <div class="flex items-center gap-3 mt-6 mb-4">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400 font-semibold uppercase tracking-widest">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <button type="button" onclick="proceedAsGuest()"
                    class="w-full py-4 bg-gray-100 text-gray-600 font-bold tracking-widest text-sm rounded-lg border-2 border-gray-200 hover:bg-gray-200 hover:border-gray-300 transition-all uppercase">
                    CONTINUE AS GUEST
                </button>
            </div>
            
            <p class="text-center text-[10px] text-gray-300 mt-12 w-full max-w-sm uppercase tracking-widest">
                Self-Service Ordering
            </p>

        </div>


    </div>

    <script src="js/customer_login.js?v=<?php echo time(); ?>"></script>
</body>
</html>
