<?php
session_start();
require_once 'database.php';


$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;


$category = $_GET['category'] ?? '';
$condition = $_GET['condition'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 10000;
$search = $_GET['search'] ?? '';
$limit = $_GET['limit'] ?? 20;
$offset = $_GET['offset'] ?? 0;

try {
    
    $query = "SELECT b.*, 
                     u.username as seller_name,
                     u.full_name as seller_full_name,
                     u.email as seller_email,
                     u.location as seller_location,
                     COUNT(DISTINCT o.order_id) as total_orders,
                     CASE 
                         WHEN b.seller_id = :user_id THEN 'your_book'
                         WHEN EXISTS (
                             SELECT 1 FROM orders o 
                             WHERE o.book_id = b.book_id 
                             AND o.buyer_id = :user_id
                         ) THEN 'purchased'
                         ELSE 'available'
                     END as book_status
              FROM books b
              JOIN users u ON b.seller_id = u.id
              LEFT JOIN orders o ON b.book_id = o.book_id
              WHERE b.status = 'available'";
    
    $params = [':user_id' => $user_id];
    
   
    if (!empty($category)) {
        $query .= " AND b.category = :category";
        $params[':category'] = $category;
    }
    
    if (!empty($condition)) {
        $query .= " AND b.condition = :condition";
        $params[':condition'] = $condition;
    }
    
    if (!empty($search)) {
        $query .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    
    $query .= " AND b.price BETWEEN :min_price AND :max_price";
    $params[':min_price'] = $min_price;
    $params[':max_price'] = $max_price;
    
    
    $query .= " GROUP BY b.book_id 
                ORDER BY b.created_at DESC 
                LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
   
    $count_query = "SELECT COUNT(*) as total FROM books b WHERE b.status = 'available'";
    
    if (!empty($category)) {
        $count_query .= " AND b.category = :category";
    }
    
    if (!empty($condition)) {
        $count_query .= " AND b.condition = :condition";
    }
    
    if (!empty($search)) {
        $count_query .= " AND (b.title LIKE :search OR b.author LIKE :search)";
    }
    
    $count_query .= " AND b.price BETWEEN :min_price AND :max_price";
    
    $count_stmt = $pdo->prepare($count_query);
    
   
    if (!empty($category)) {
        $count_stmt->bindValue(':category', $category);
    }
    
    if (!empty($condition)) {
        $count_stmt->bindValue(':condition', $condition);
    }
    
    if (!empty($search)) {
        $count_stmt->bindValue(':search', "%$search%");
    }
    
    $count_stmt->bindValue(':min_price', $min_price);
    $count_stmt->bindValue(':max_price', $max_price);
    
    $count_stmt->execute();
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    
    $response = [
        'success' => true,
        'books' => $books,
        'total' => $total,
        'filters' => [
            'category' => $category,
            'condition' => $condition,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'search' => $search
        ],
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'total_pages' => ceil($total / $limit)
        ]
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch books: ' . $e->getMessage(),
        'books' => []
    ]);
}
?>