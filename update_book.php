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
        echo json_encode(['success' => false, 'message' => 'You are not authorized to update this book']);
        exit();
    }
    
    // Get and sanitize form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $price = floatval($_POST['price']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle image upload if provided
    $image_update = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'book_' . time() . '_' . uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/' . $filename;
                $image_update = ", image_path = '$image_path'";
            }
        }
    }
    
    // Build UPDATE query
    $query = "UPDATE books SET 
              title = '$title',
              author = '$author',
              category = '$category',
              book_condition = '$condition',
              price = $price,
              location = '$location',
              description = '$description'
              $image_update
              WHERE id = $book_id";
    
    // Execute query
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Book updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating book: ' . mysqli_error($conn)]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>