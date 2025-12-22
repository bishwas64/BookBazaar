<?php
session_start();
require_once 'database.php';
require_once 'session_check.php';

$user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? null;
$order_id = $_GET['order_id'] ?? null;

if (!$other_user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit();
}

try {
    
    $user_query = "SELECT username, full_name, email, phone, location 
                   FROM users WHERE id = ?";
    $stmt = $pdo->prepare($user_query);
    $stmt->execute([$other_user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
    $order_info = [];
    if ($order_id) {
        $order_query = "SELECT delivery_address, mobile_number, payment_method 
                        FROM orders WHERE order_id = ? AND (buyer_id = ? OR seller_id = ?)";
        $stmt = $pdo->prepare($order_query);
        $stmt->execute([$order_id, $user_id, $user_id]);
        $order_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
   
    $contact_allowed = false;
    if ($order_id) {
        $check_query = "SELECT order_id FROM orders 
                       WHERE order_id = ? 
                       AND ((buyer_id = ? AND seller_id = ?) 
                       OR (buyer_id = ? AND seller_id = ?))
                       AND order_status NOT IN ('cancelled', 'delivered')";
        $stmt = $pdo->prepare($check_query);
        $stmt->execute([$order_id, $user_id, $other_user_id, $other_user_id, $user_id]);
        $contact_allowed = $stmt->rowCount() > 0;
    }
    
    if ($contact_allowed) {
        echo json_encode([
            'success' => true,
            'user_info' => $user_info,
            'order_info' => $order_info,
            'contact_allowed' => true
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Contact information available only for active orders',
            'contact_allowed' => false
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>