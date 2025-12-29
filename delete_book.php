<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get book ID
    $book_id = intval($_POST['book_id']);
    $seller_email = $_SESSION['user_email'];
    
    // Verify the book belongs to the logged-in user
    $check_query = "SELECT id FROM books WHERE id = $book_id AND seller_email = '$seller_email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this book']);
        exit();
    }
    
    // Build DELETE query
    $query = "DELETE FROM books WHERE id = $book_id";
    
    // Execute query
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Book deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting book: ' . mysqli_error($conn)]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>