<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $data) {
    $book_id = intval($data['book_id']);
    $price = floatval($data['price']);
    $delivery_charge = 50;
    $total_amount = $price + $delivery_charge;
    
   
    $book_query = "SELECT * FROM books WHERE id = $book_id";
    $book_result = mysqli_query($conn, $book_query);
    $book = mysqli_fetch_assoc($book_result);
    
    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
        exit();
    }
    
    $query = "INSERT INTO orders (
        book_id, 
        buyer_email, 
        buyer_name, 
        seller_email, 
        seller_name, 
        book_title, 
        price, 
        delivery_charge, 
        total_amount, 
        delivery_address, 
        mobile_number, 
        payment_method, 
        transaction_id
    ) VALUES (
        $book_id,
        '" . mysqli_real_escape_string($conn, $_SESSION['user_email']) . "',
        '" . mysqli_real_escape_string($conn, $_SESSION['user_name']) . "',
        '" . mysqli_real_escape_string($conn, $book['seller_email']) . "',
        '" . mysqli_real_escape_string($conn, $book['seller_name']) . "',
        '" . mysqli_real_escape_string($conn, $book['title']) . "',
        $price,
        $delivery_charge,
        $total_amount,
        '" . mysqli_real_escape_string($conn, $data['delivery_address']) . "',
        '" . mysqli_real_escape_string($conn, $data['mobile_number']) . "',
        '" . mysqli_real_escape_string($conn, $data['payment_method']) . "',
        '" . mysqli_real_escape_string($conn, $data['transaction_id']) . "'
    )";
    
    if (mysqli_query($conn, $query)) {
        $order_id = mysqli_insert_id($conn);
        
        
        mysqli_query($conn, "UPDATE books SET status = 'sold' WHERE id = $book_id");
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'transaction_id' => $data['transaction_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating order: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>