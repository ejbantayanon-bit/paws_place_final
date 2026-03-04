<?php
session_start();
// If already logged in as staff/admin, redirect to dashboard
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Cashier', 'Kitchen', 'Barista'])) {
    $redirect = '3_index.php';
    if ($_SESSION['role'] === 'Admin') $redirect = '5_adminDashboard.php';
    if ($_SESSION['role'] === 'Kitchen') $redirect = 'kitchen_terminal.php';
    header('Location: ' . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>GrabHound - Staff Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/login.css">
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

    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>

    <div class="grid grid-cols-1 lg:grid-cols-4 min-h-screen">
        <div class="lg:col-span-3 cafe-context p-0">
            <div class="w-full h-full flex items-stretch">
                <div class="w-full h-full image-placeholder overflow-hidden relative">
                    <img src="../image/Paws place.jpeg" alt="Cafe" class="object-cover w-full h-full">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/40 pointer-events-none"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
                        <h1 class="text-5xl font-black text-white mb-2 drop-shadow-lg tracking-wider">FOUNDATION UNIVERSITY</h1>
                        <p class="text-2xl font-light text-white/90 tracking-wide">GrabHound Management System</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="right-panel" class="lg:col-span-1 p-8 flex flex-col justify-center items-center bg-white shadow-2xl z-10">
            <header class="text-center mb-10 w-full max-w-xs flex flex-col items-center">
                <div class="mb-4">
                    <img src="img/lgoo.png" alt="GrabHound Logo" class="h-16 sm:h-20 object-contain">
                </div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">STAFF ACCESS</h2>
                <div class="h-1 w-16 bg-maroon mx-auto mt-4 rounded-full bg-[#800000]"></div>
            </header>

            <div id="login-form-container" class="mt-8 w-full max-w-xs">
                <form id="login-form" class="space-y-5">
                    <div>
                        <input type="text" id="username" name="username" placeholder="Username" class="input-field w-full placeholder-gray-400 text-gray-800" required autofocus>
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
            </div>
            
            <!-- Back to Main Menu removed -->
            
            <p class="text-center text-[10px] text-gray-300 mt-8 w-full max-w-xs uppercase tracking-widest">
                Authorized Personnel Only
            </p>

        </div>

    </div>

    <script src="js/staff_login.js"></script>
</body>
</html>
