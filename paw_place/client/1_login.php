<?php
session_start();
// If already logged in, redirect to corresponding dashboard
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'Admin') header('Location: 5_adminDashboard.php');
    else if (in_array($role, ['Cashier'])) header('Location: 3_index.php');
    else if ($role === 'KIOSK') header('Location: 2_kiosk_ordering.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Place System Access</title>
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
                        <p class="text-2xl font-light text-white/90 tracking-wide">Paws Place Cafe System</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="right-panel" class="lg:col-span-1 p-8 flex flex-col justify-center items-center bg-white shadow-2xl z-10">
            <header class="text-center mb-10 w-full max-w-xs">
                <div class="text-7xl mb-4 text-maroon">🐾</div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">SYSTEM ACCESS</h2>
                <div class="h-1 w-16 bg-maroon mx-auto mt-4 rounded-full bg-[#800000]"></div>
            </header>

            <div class="space-y-4 w-full max-w-xs">
                <button onclick="window.location.href='customer_login.php'" class="role-button bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 text-green-800 w-full rounded-lg border-2 border-green-300">
                    <span class="text-lg font-bold tracking-wide">CUSTOMER KIOSK</span>
                    <span class="text-xs text-green-700 font-medium uppercase tracking-widest mt-1">Order Here</span>
                </button>
                
                <button onclick="window.location.href='staff_login.php'" class="role-button color-maroon hover:bg-red-900 w-full rounded-lg border-transparent">
                    <span class="text-lg font-bold tracking-wide">STAFF / ADMIN</span>
                    <span class="text-xs text-red-100 font-medium uppercase tracking-widest mt-1">Management Access</span>
                </button>
            </div>
            
            <p class="text-center text-[10px] text-gray-300 mt-16 w-full max-w-xs uppercase tracking-widest">
                Authorized Personnel Only
            </p>

        </div>

    </div>

    <script src="js/login.js"></script>
</body>
</html>
