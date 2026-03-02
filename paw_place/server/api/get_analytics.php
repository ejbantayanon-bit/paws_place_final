<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Security check: Only Admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');

try {
    // 1. Sales Trend
    // Default to last 7 days
    $range = isset($_GET['range']) ? $_GET['range'] : '7days';
    $salesData = [];

    if ($range === '1year') {
        // Last 12 Months
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime($monthStart));

            $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(time_placed) >= ? AND DATE(time_placed) <= ? AND status IN ('PREPARING', 'READY', 'SERVED')");
            $stmt->bind_param('ss', $monthStart, $monthEnd);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $salesData[] = [
                'date' => $monthLabel,
                'total' => $row['total'] ? floatval($row['total']) : 0
            ];
            $stmt->close();
        }
    } elseif ($range === '30days') {
        // Last 30 Days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(time_placed) = ? AND status IN ('PREPARING', 'READY', 'SERVED')");
            $stmt->bind_param('s', $date);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $salesData[] = [
                'date' => date('M d', strtotime($date)),
                'total' => $row['total'] ? floatval($row['total']) : 0
            ];
            $stmt->close();
        }
    } else {
        // Default: Last 7 Days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE DATE(time_placed) = ? AND status IN ('PREPARING', 'READY', 'SERVED')");
            $stmt->bind_param('s', $date);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $salesData[] = [
                'date' => date('M d', strtotime($date)),
                'total' => $row['total'] ? floatval($row['total']) : 0
            ];
            $stmt->close();
        }
    }

    // 2. Top 5 Selling Items (All Time)
    // We need to join order_items with menu_items
    // status check on orders table is important to only count valid sales
    $topItemsQuery = "
        SELECT COALESCE(mi.name, oi.external_item_name) as item_display_name, SUM(oi.quantity) as total_sold
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id
        WHERE o.status IN ('PREPARING', 'READY', 'SERVED')
        GROUP BY item_display_name
        ORDER BY total_sold DESC
        LIMIT 5
    ";
    $result = $conn->query($topItemsQuery);
    $topItems = [];
    while ($row = $result->fetch_assoc()) {
        $topItems[] = [
            'name' => $row['item_display_name'],
            'count' => intval($row['total_sold'])
        ];
    }

    // 3. Sales by Category
    // Assuming categories table exists or we group by category_id directly if no table
    // Based on previous file reads, 'categories' table exists.
    $categorySalesQuery = "
        SELECT COALESCE(c.name, 'Cafeteria Store') as category_display_name, SUM(oi.price_at_sale * oi.quantity) as total_sales
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.item_id
        LEFT JOIN categories c ON mi.category_id = c.category_id
        WHERE o.status IN ('PREPARING', 'READY', 'SERVED')
        GROUP BY category_display_name
        ORDER BY total_sales DESC
    ";
    
    // Fallback if categories table join fails (e.g. if category_id usage is inconsistent)
    // checking if categories table exists first via list_tables output earlier... yes it does.
    
    $result = $conn->query($categorySalesQuery);
    $categorySales = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categorySales[] = [
                'category' => $row['category_name'],
                'sales' => floatval($row['total_sales'])
            ];
        }
    } else {
        // Fallback: Group by category_id if join fails
         $categorySalesQueryFallback = "
            SELECT mi.category_id, SUM(oi.price_at_sale * oi.quantity) as total_sales
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.item_id
            WHERE o.status IN ('PREPARING', 'READY', 'SERVED')
            GROUP BY mi.category_id
            ORDER BY total_sales DESC
        ";
        $result = $conn->query($categorySalesQueryFallback);
        while ($row = $result->fetch_assoc()) {
             $categorySales[] = [
                'category' => 'Category ' . $row['category_id'], // Placeholder name
                'sales' => floatval($row['total_sales'])
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'sales_data' => $salesData,
        'top_items' => $topItems,
        'category_sales' => $categorySales
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
