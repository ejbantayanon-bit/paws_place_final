<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Landing::index');

$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('attempt-login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
});

// Placeholders for now
$routes->get('kitchen', 'Kitchen::index');
$routes->group('api', function($routes) {
    $routes->get('orders', 'Api\Orders::index');
    $routes->get('products', 'Api\Products::index'); // New
    $routes->get('categories', 'Api\Categories::index'); // New
});
$routes->get('cashier', 'Cashier::index');
$routes->get('store', 'Store::index'); // New
$routes->group('kiosk', function($routes) {
    $routes->get('login', 'Kiosk::login');
    $routes->post('lookup', 'Kiosk::lookup');
    $routes->post('set-session', 'Kiosk::setSession');
    $routes->get('menu', function() { return 'Kiosk Menu (Coming Soon)'; });
});
$routes->get('admin/dashboard', function() {
    return 'Admin Dashboard (Coming Soon)';
});
$routes->get('kiosk', function() {
    return 'Kiosk (Coming Soon)';
});
