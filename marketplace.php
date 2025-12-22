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


$host = "localhost";
$username = "root";
$password = "";
$database = "used_books";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed");
}

$query = "SELECT * FROM books WHERE status = 'available' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$books = [];

if ($result && mysqli_num_rows($result) > 0) {
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    
    $books = getSampleBooks();
}


function getSampleBooks() {
    return [
        [
            'id' => 1,
            'title' => 'The Alchemist',
            'author' => 'Paulo Coelho',
            'book_condition' => 'like-new',
            'price' => 350.00,
            'location' => 'Kathmandu',
            'seller_name' => 'Test User',
            'seller_email' => 'test@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=400&h=250&fit=crop&crop=center'
        ],
        [
            'id' => 2,
            'title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'book_condition' => 'good',
            'price' => 500.00,
            'location' => 'Pokhara',
            'seller_name' => 'Test User',
            'seller_email' => 'test@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&h=250&fit=crop&crop=center'
        ],
        [
            'id' => 3,
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'book_condition' => 'like-new',
            'price' => 250.00,
            'location' => 'Kathmandu',
            'seller_name' => 'John Sharma',
            'seller_email' => 'john@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=250&fit=crop&crop=center'
        ],
        [
            'id' => 4,
            'title' => 'A Brief History of Time',
            'author' => 'Stephen Hawking',
            'book_condition' => 'new',
            'price' => 400.00,
            'location' => 'Pokhara',
            'seller_name' => 'Sita Maharjan',
            'seller_email' => 'sita@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&h=250&fit=crop&crop=center'
        ],
        [
            'id' => 5,
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'book_condition' => 'good',
            'price' => 300.00,
            'location' => 'Lalitpur',
            'seller_name' => 'Hari Bhandari',
            'seller_email' => 'hari@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&h=250&fit=crop&crop=center'
        ],
        [
            'id' => 6,
            'title' => 'Harry Potter and the Philosopher\'s Stone',
            'author' => 'J.K. Rowling',
            'book_condition' => 'good',
            'price' => 500.00,
            'location' => 'Bhaktapur',
            'seller_name' => 'Sujan Thapa',
            'seller_email' => 'sujan@example.com',
            'image_path' => 'https://images.unsplash.com/photo-1600189261867-30e5ffe7b8da?w=400&h=250&fit=crop&crop=center'
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Marketplace | BooksBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/marketplace.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for contact info */
        .contact-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .contact-info h4 {
            margin-top: 0;
            color: #1e40af;
        }
        .contact-note {
            background: #fef3c7;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .contact-note i {
            color: #d97706;
        }
        .order-success {
            text-align: center;
        }
        .success-icon {
            color: #10b981;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .success-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .seller-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9rem;
            color: #6b7280;
        }
        .no-books {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
   
    <header class="navbar">
        <a href="index.html" class="logo">📘 BooksBazaar</a>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="marketplace.php" class="active">Marketplace</a></li>
            <li><a href="my_orders.php">My Orders</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><span class="user-welcome"><i class="fas fa-user"></i> <?php echo htmlspecialchars($user_name); ?></span></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </header>

    <section class="marketplace-hero">
        <div class="hero-content">
            <h1>Browse & <span>Trade Books</span></h1>
            <p>Discover thousands of books from readers across Nepal. Buy, sell, or exchange your books.</p>
            <div class="hero-search">
                <input type="text" id="searchInput" placeholder="Search by title, author, or genre...">
                <button class="search-btn" onclick="loadBooks()"><i class="fas fa-search"></i></button>
                <button class="btn primary sell-btn" onclick="showSellForm()"><i class="fas fa-plus"></i> Sell a Book</button>
            </div>
        </div>
    </section>

   
    <main class="marketplace-container">
       
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3><i class="fas fa-filter"></i> Filters</h3>
                <button class="clear-filters" onclick="clearFilters()">Clear All</button>
            </div>

            
            <div class="filter-group">
                <h4>Category</h4>
                <div class="filter-options">
                    <label class="filter-checkbox">
                        <input type="checkbox" name="category" value="fiction">
                        <span>Fiction</span>
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="category" value="non-fiction">
                        <span>Non-Fiction</span>
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="category" value="academic">
                        <span>Academic</span>
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="category" value="biography">
                        <span>Biography</span>
                    </label>
                </div>
            </div>

            
            <div class="filter-group">
                <h4>Condition</h4>
                <div class="filter-options">
                    <label class="filter-checkbox">
                        <input type="checkbox" name="condition" value="new">
                        <span>New</span>
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="condition" value="like-new">
                        <span>Like New</span>
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="condition" value="good">
                        <span>Good</span>
                    </label>
                </div>
            </div>

            
            <div class="filter-group">
                <h4>Price Range (NPR)</h4>
                <div class="price-range">
                    <div class="range-values">
                        <span id="minPrice">0</span> - <span id="maxPrice">5000</span>
                    </div>
                    <input type="range" id="priceSlider" min="0" max="5000" value="5000">
                </div>
            </div>
        </aside>

     
        <section class="books-main">
            <div class="books-header">
                <h2>Available Books <span id="booksCount">(<?php echo count($books); ?>)</span></h2>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
                    <button class="view-btn" data-view="list"><i class="fas fa-list"></i></button>
                </div>
            </div>

            <div class="books-grid" id="booksGrid">
                <?php if (empty($books)): ?>
                    <div class="no-books">
                        <i class="fas fa-book-open fa-3x"></i>
                        <h3>No books available</h3>
                        <p>Be the first to sell a book!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <?php
                        $seller_id = md5($book['seller_email'] ?? 'default');
                        $image_url = !empty($book['image_path']) ? htmlspecialchars($book['image_path']) : 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=250&fit=crop&crop=center';
                        ?>
                        <div class="book-card">
                            <div class="book-image">
                                <img src="<?php echo $image_url; ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                     style="width:100%; height:100%; object-fit:cover;">
                                <span class="book-badge"><?php echo ucfirst($book['book_condition'] ?? 'good'); ?></span>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="book-meta">
                                    <div class="book-price">NPR <?php echo number_format($book['price'] ?? 0, 2); ?></div>
                                    <div class="book-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($book['location'] ?? 'Unknown'); ?>
                                    </div>
                                </div>
                                <div class="book-actions">
                                    
                                    <button class="action-btn buy-btn" 
                                            onclick="buyBook(
                                                <?php echo $book['id']; ?>,
                                                '<?php echo addslashes($book['title']); ?>',
                                                '<?php echo addslashes($book['author']); ?>',
                                                <?php echo $book['price']; ?>,
                                                '<?php echo addslashes($book['seller_name']); ?>',
                                                '<?php echo addslashes($book['seller_email']); ?>',
                                                '<?php echo $seller_id; ?>'
                                            )">
                                        <i class="fas fa-shopping-cart"></i> Buy Now
                                    </button>
                                </div>
                                <?php if (($book['seller_email'] ?? '') != $user_email): ?>
                                <div class="seller-info">
                                    <small>Seller: <?php echo htmlspecialchars($book['seller_name'] ?? 'Unknown'); ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            
            <div class="sell-book-form" id="sellForm" style="display: none;">
                <h2><i class="fas fa-book-medical"></i> Sell Your Book</h2>
                <form id="sellBookForm" action="php/add_book.php" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Book Title *</label>
                            <input type="text" name="title" id="bookTitle" required placeholder="Enter book title">
                        </div>
                        <div class="form-group">
                            <label>Author *</label>
                            <input type="text" name="author" id="bookAuthor" required placeholder="Author name">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" id="bookCategory" required>
                                <option value="">Select category</option>
                                <option value="fiction">Fiction</option>
                                <option value="non-fiction">Non-Fiction</option>
                                <option value="academic">Academic</option>
                                <option value="biography">Biography</option>
                                <option value="fantasy">Fantasy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Condition *</label>
                            <select name="condition" id="bookCondition" required>
                                <option value="">Select condition</option>
                                <option value="new">New</option>
                                <option value="like-new">Like New</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (NPR) *</label>
                            <input type="number" name="price" id="bookPrice" required placeholder="e.g., 500" min="50" max="10000">
                        </div>
                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" id="bookLocation" required placeholder="City/Area">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" id="bookDescription" rows="3" required placeholder="Describe your book's condition, edition, etc."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Book Cover Image</label>
                        <input type="file" name="image" id="bookImage" accept="image/*">
                        <small>Optional. Max size: 2MB</small>
                    </div>

                    <button type="submit" class="btn primary submit-btn">List Book for Sale</button>
                    <button type="button" class="btn secondary" onclick="hideSellForm()">Cancel</button>
                </form>
            </div>
        </section>
    </main>

    
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Complete Your Purchase</h2>
            
            <div class="book-summary" id="bookSummary">
               
            </div>
            
            <div class="payment-options">
                <h3>Choose Payment Method</h3>
                <div class="payment-methods-grid">
                    <button class="payment-method esewa" onclick="selectPayment('esewa')">
                        <i class="fas fa-wallet"></i> Pay with eSewa
                    </button>
                    <button class="payment-method khalti" onclick="selectPayment('khalti')">
                        <i class="fas fa-mobile-alt"></i> Pay with Khalti
                    </button>
                </div>
                
                <div class="payment-form" id="paymentForm" style="display: none;">
                    <h4 id="paymentMethodTitle">Enter Payment Details</h4>
                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <input type="text" id="mobileNumber" placeholder="98XXXXXXXX" pattern="98[0-9]{8}" required>
                        <small>10-digit Nepali number starting with 98</small>
                    </div>
                    <div class="form-group">
                        <label>Delivery Address *</label>
                        <textarea id="deliveryAddress" rows="3" placeholder="Enter complete delivery address" required></textarea>
                    </div>
                    <button type="button" class="btn primary" onclick="processPayment()">Confirm Order</button>
                    <button type="button" class="btn secondary" onclick="cancelPayment()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    
    <footer class="footer">
        <p>© 2025 Book Bazaar. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="contact.html">Contact Support</a>
        </div>
    </footer>

    
    <script>
        
        let currentBook = null;
        let selectedPaymentMethod = null;
        const deliveryCharge = 50;

       
        function showSellForm() {
            document.getElementById('sellForm').style.display = 'block';
            window.scrollTo({ top: document.getElementById('sellForm').offsetTop, behavior: 'smooth' });
        }

        function hideSellForm() {
            document.getElementById('sellForm').style.display = 'none';
            document.getElementById('sellBookForm').reset();
        }

        async function submitBookForm(event) {
            event.preventDefault();
            
            const form = document.getElementById('sellBookForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('php/add_book.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Book listed successfully!');
                    hideSellForm();
                    window.location.reload(); 
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Network error. Please try again.');
            }
        }

        function buyBook(bookId, title, author, price, sellerName, sellerEmail, sellerId) {
            currentBook = {
                id: bookId,
                title: title,
                author: author,
                price: parseInt(price),
                sellerName: sellerName,
                sellerEmail: sellerEmail,
                sellerId: sellerId,
                total: parseInt(price) + deliveryCharge
            };
            
            document.getElementById('bookSummary').innerHTML = `
                <h4>${currentBook.title}</h4>
                <p><strong>Author:</strong> ${currentBook.author}</p>
                <p><strong>Price:</strong> NPR ${currentBook.price}</p>
                <p><strong>Seller:</strong> ${currentBook.sellerName}</p>
                <p><strong>Seller Email:</strong> ${currentBook.sellerEmail}</p>
                <p><strong>Total (with delivery):</strong> NPR ${currentBook.total}</p>
            `;
            
 
            document.getElementById('paymentModal').style.display = 'block';
            document.getElementById('paymentForm').style.display = 'none';
            selectedPaymentMethod = null;
        }

        
        function selectPayment(method) {
            selectedPaymentMethod = method;
            document.getElementById('paymentForm').style.display = 'block';
            document.getElementById('paymentMethodTitle').textContent = `Pay with ${method.toUpperCase()}`;
        }

       
        async function processPayment() {
            const mobile = document.getElementById('mobileNumber').value;
            const address = document.getElementById('deliveryAddress').value;
            
           
            if (!mobile.match(/^98[0-9]{8}$/)) {
                alert('Please enter a valid 10-digit Nepali mobile number starting with 98');
                return;
            }
            
            if (!address.trim()) {
                alert('Please enter delivery address');
                return;
            }
            
            
            const submitBtn = document.querySelector('#paymentForm .btn.primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            try {
                
                const orderData = {
                    book_id: currentBook.id,
                    seller_email: currentBook.sellerEmail,
                    price: currentBook.price,
                    delivery_charge: deliveryCharge,
                    total_amount: currentBook.total,
                    delivery_address: address,
                    payment_method: selectedPaymentMethod || 'cash',
                    mobile_number: mobile,
                    transaction_id: 'TXN' + Date.now()
                };
                
               
                const response = await fetch('php/create_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                  
                    showOrderSuccess(result.order_id, result.transaction_id, currentBook);
                } else {
                    alert('Order failed: ' + result.message);
                }
            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Error:', error);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

       
        function showOrderSuccess(orderId, transactionId, book) {
            document.getElementById('bookSummary').innerHTML = `
                <div class="order-success">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Order Successful!</h3>
                    <div class="order-details">
                        <p><strong>Order ID:</strong> #${orderId}</p>
                        <p><strong>Transaction ID:</strong> ${transactionId}</p>
                        <p><strong>Amount:</strong> NPR ${book.total}</p>
                        <p><strong>Book:</strong> ${book.title}</p>
                    </div>
                    
                    <div class="contact-info">
                        <h4>Contact Information:</h4>
                        <p><strong>Seller Name:</strong> ${book.sellerName}</p>
                        <p><strong>Seller Email:</strong> ${book.sellerEmail}</p>
                        <p><strong>Your Mobile:</strong> ${document.getElementById('mobileNumber').value}</p>
                        <p><strong>Delivery Address:</strong> ${document.getElementById('deliveryAddress').value}</p>
                        
                        <div class="contact-note">
                            <p><i class="fas fa-info-circle"></i> The seller will contact you within 24 hours.</p>
                            <p><i class="fas fa-info-circle"></i> You can view this order in "My Orders" page.</p>
                        </div>
                    </div>
                    
                    <div class="success-actions">
                        <button class="btn primary" onclick="window.location.href='my_orders.php'">
                            <i class="fas fa-list"></i> View My Orders
                        </button>
                        <button class="btn secondary" onclick="closeModalAndRefresh()">
                            Continue Shopping
                        </button>
                    </div>
                </div>
            `;
            
            
            document.getElementById('paymentForm').style.display = 'none';
        }

       
        function cancelPayment() {
            document.getElementById('paymentForm').style.display = 'none';
            selectedPaymentMethod = null;
        }

        
        function closeModalAndRefresh() {
            closeModal();
            window.location.reload();
        }

       
        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentForm').style.display = 'none';
            document.getElementById('mobileNumber').value = '';
            document.getElementById('deliveryAddress').value = '';
            currentBook = null;
            selectedPaymentMethod = null;
        }

       
        function clearFilters() {
            document.querySelectorAll('input[name="category"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="condition"]').forEach(cb => cb.checked = false);
            document.getElementById('priceSlider').value = 5000;
            document.getElementById('maxPrice').textContent = '5000';
            document.getElementById('searchInput').value = '';
            filterBooks();
        }

       
        async function loadBooks() {
            const search = document.getElementById('searchInput').value;
            const maxPrice = document.getElementById('priceSlider').value;
            
            filterBooks();
        }

        function filterBooks() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const maxPrice = parseInt(document.getElementById('priceSlider').value);
            
            const books = document.querySelectorAll('.book-card');
            let visibleCount = 0;
            
            books.forEach(book => {
                const title = book.querySelector('.book-title').textContent.toLowerCase();
                const author = book.querySelector('.book-author').textContent.toLowerCase();
                const priceText = book.querySelector('.book-price').textContent;
                const price = parseFloat(priceText.replace('NPR ', '').replace(',', ''));
                
               
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    author.includes(searchTerm);
                
                
                const matchesPrice = price <= maxPrice;
                
              
                if (matchesSearch && matchesPrice) {
                    book.style.display = 'block';
                    visibleCount++;
                } else {
                    book.style.display = 'none';
                }
            });
            
            
            document.getElementById('booksCount').textContent = `(${visibleCount})`;
        }

        
        document.addEventListener('DOMContentLoaded', function() {
            
            const sellForm = document.getElementById('sellBookForm');
            if (sellForm) {
                sellForm.addEventListener('submit', submitBookForm);
            }
            
            
            const priceSlider = document.getElementById('priceSlider');
            const maxPriceDisplay = document.getElementById('maxPrice');
            
            if (priceSlider) {
                priceSlider.addEventListener('input', function() {
                    maxPriceDisplay.textContent = this.value;
                    filterBooks();
                });
            }
            
          
            const checkboxes = document.querySelectorAll('.filter-checkbox input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', filterBooks);
            });
            
          
            document.getElementById('searchInput').addEventListener('input', filterBooks);
            
            
            window.onclick = function(event) {
                const modal = document.getElementById('paymentModal');
                if (event.target === modal) {
                    closeModal();
                }
            };
        });
    </script>
</body>
</html>