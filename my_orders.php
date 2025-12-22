<?php

session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<script>
        alert("Please login first!");
        window.location.href = "login.html";
    </script>';
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? '';


$buying_orders = [];
$selling_orders = [];


if (isset($_SESSION['user_email'])) {
    
    $conn = mysqli_connect("localhost", "root", "", "used_books");
    
    if ($conn) {
        
        $buying_query = "SELECT * FROM orders WHERE buyer_email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
        $buying_result = mysqli_query($conn, $buying_query);
        if ($buying_result) {
            $buying_orders = mysqli_fetch_all($buying_result, MYSQLI_ASSOC);
        }
        
        
        $selling_query = "SELECT * FROM orders WHERE seller_email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
        $selling_result = mysqli_query($conn, $selling_query);
        if ($selling_result) {
            $selling_orders = mysqli_fetch_all($selling_result, MYSQLI_ASSOC);
        }
        
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - BooksBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <header class="navbar">
        <a href="index.html" class="logo">📘 BooksBazaar</a>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="marketplace.php">Marketplace</a></li>
            <li><a href="my_orders.php" class="active">My Orders</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><span class="user-welcome"><i class="fas fa-user"></i> <?php echo htmlspecialchars($user_name); ?></span></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </header>

    <main class="orders-container">
        <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?>! Here are your orders.</p>
        
        <div class="orders-tabs">
            <button class="tab-btn active" onclick="showTab('buying')">
                <i class="fas fa-shopping-cart"></i> Buying (<?php echo count($buying_orders); ?>)
            </button>
            <button class="tab-btn" onclick="showTab('selling')">
                <i class="fas fa-store"></i> Selling (<?php echo count($selling_orders); ?>)
            </button>
        </div>
        
        
        <div class="tab-content active" id="buyingTab">
            <?php if (empty($buying_orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart fa-3x"></i>
                    <h3>No purchases yet</h3>
                    <p>Start buying books from the marketplace!</p>
                    <a href="marketplace.php" class="btn primary">Browse Books</a>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($buying_orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo $order['id'] ?? 'N/A'; ?></h3>
                            <span class="order-status <?php echo strtolower($order['order_status'] ?? 'pending'); ?>">
                                <?php echo ucfirst($order['order_status'] ?? 'Pending'); ?>
                            </span>
                        </div>
                        
                        <div class="order-body">
                            <div class="order-details">
                                <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id'] ?? 'N/A'; ?></p>
                                <p><strong>Amount:</strong> NPR <?php echo number_format($order['total_amount'] ?? 0, 2); ?></p>
                                <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'] ?? 'now')); ?></p>
                                <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method'] ?? 'Cash'); ?></p>
                            </div>
                            
                            <div class="seller-contact">
                                <h5>Seller Information</h5>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['seller_email'] ?? 'Not available'); ?></p>
                                <?php if ($order['order_status'] === 'pending' || $order['order_status'] === 'confirmed'): ?>
                                <div class="contact-note">
                                    <p><i class="fas fa-info-circle"></i> The seller will contact you for delivery.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        
        <div class="tab-content" id="sellingTab">
            <?php if (empty($selling_orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-store fa-3x"></i>
                    <h3>No sales yet</h3>
                    <p>Sell your books to see orders here!</p>
                    <a href="marketplace.php" class="btn primary">Sell a Book</a>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($selling_orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo $order['id'] ?? 'N/A'; ?></h3>
                            <div class="order-actions">
                                <span class="order-status <?php echo strtolower($order['order_status'] ?? 'pending'); ?>">
                                    <?php echo ucfirst($order['order_status'] ?? 'Pending'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-body">
                            <div class="order-details">
                                <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id'] ?? 'N/A'; ?></p>
                                <p><strong>Amount:</strong> NPR <?php echo number_format($order['total_amount'] ?? 0, 2); ?></p>
                                <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'] ?? 'now')); ?></p>
                            </div>
                            
                            <div class="buyer-contact">
                                <h5>Buyer Information</h5>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['buyer_email'] ?? 'Not available'); ?></p>
                                <?php if (!empty($order['mobile_number'])): ?>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['mobile_number']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['delivery_address'])): ?>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                                <?php endif; ?>
                                
                                <div class="contact-note">
                                    <p><i class="fas fa-info-circle"></i> Contact the buyer to arrange delivery.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        
        function showTab(tabName) {
         
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
           
            document.getElementById(tabName + 'Tab').classList.add('active');
            
            
            event.target.classList.add('active');
        }
    </script>
</body>
</html>