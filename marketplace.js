
function checkLogin() {
   
    const isLoggedIn = localStorage.getItem('user_logged_in') === 'true';
    
    if (!isLoggedIn) {
      
    
        window.location.href = 'login.html?redirect=marketplace';
        return false;
    }
    return true;
}


function updateNavbar() {
    const authLinks = document.getElementById('authLinks');
    const isLoggedIn = localStorage.getItem('user_logged_in') === 'true';
    const userName = localStorage.getItem('user_name') || 'User';
    
    if (authLinks) {
        if (isLoggedIn) {
            authLinks.innerHTML = `
                <li><span class="user-welcome"><i class="fas fa-user"></i> ${userName}</span></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            `;
        } else {
            authLinks.innerHTML = `
                <li><a href="login.html">Login</a></li>
                <li><a href="signup.html">Signup</a></li>
            `;
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
   
    if (window.location.pathname.includes('marketplace')) {
        if (!checkLogin()) return;
    }
    
    updateNavbar();
    loadBooksFromDatabase();
    setupEventListeners();
});


async function loadBooksFromDatabase() {
    try {
        const response = await fetch('php/get_books.php');
        const books = await response.json();
        
        if (Array.isArray(books)) {
            allBooks = books.map(book => ({
                id: book.id,
                title: book.title,
                author: book.author,
                category: book.category,
                condition: book.book_condition,
                price: parseFloat(book.price),
                location: book.location,
                description: book.description,
                image: book.image_path || null,
                seller_email: book.seller_email,
                seller_name: book.seller_name
            }));
            
            renderBooks(allBooks.slice(0, displayedBooks));
        }
    } catch (error) {
        console.error('Error loading books:', error);
       
        renderBooks(sampleBooks.slice(0, displayedBooks));
    }
}


let selectedBook = null;

function buyBook(bookId) {
    if (!checkLogin()) return;
    
    const book = allBooks.find(b => b.id === bookId);
    if (!book) return;
    
    selectedBook = book;
    
   
    const modal = document.getElementById('paymentModal');
    const bookSummary = document.getElementById('bookSummary');
    
    bookSummary.innerHTML = `
        <h4>${book.title}</h4>
        <p><strong>Author:</strong> ${book.author}</p>
        <p><strong>Condition:</strong> ${book.condition}</p>
        <p><strong>Price:</strong> NPR ${book.price}</p>
        <p><strong>Seller:</strong> ${book.seller_name}</p>
        <p><strong>Location:</strong> ${book.location}</p>
    `;
    
    modal.style.display = 'block';
}


function payWithEsewa() {
    document.getElementById('paymentForm').style.display = 'block';
    currentPaymentMethod = 'esewa';
}

function payWithKhalti() {
    document.getElementById('paymentForm').style.display = 'block';
    currentPaymentMethod = 'khalti';
}

function confirmPayment() {
    const mobile = document.getElementById('mobileNumber').value;
    const pin = document.getElementById('paymentPin').value;
    
    if (!mobile || !pin) {
        alert('Please enter mobile number and PIN');
        return;
    }
    
    alert(`Payment of NPR ${selectedBook.price} via ${currentPaymentMethod} is being processed!`);
    
    
    closeModal();
    
    
    alert('Payment successful! The seller will contact you shortly.');
    
    const bookIndex = allBooks.findIndex(b => b.id === selectedBook.id);
    if (bookIndex !== -1) {
        allBooks.splice(bookIndex, 1);
        renderBooks(allBooks.slice(0, displayedBooks));
    }
}


function closeModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.getElementById('paymentForm').style.display = 'none';
    document.getElementById('mobileNumber').value = '';
    document.getElementById('paymentPin').value = '';
    selectedBook = null;
}


function addToWishlist(bookId) {
    if (!checkLogin()) {
        alert('Please login to add books to wishlist');
        return;
    }
    
    const book = allBooks.find(b => b.id === bookId);
    if (book) {
       
        let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        if (!wishlist.find(b => b.id === bookId)) {
            wishlist.push(book);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            alert('Added to wishlist!');
        } else {
            alert('Book already in wishlist!');
        }
    }
}


async function submitBookForm(event) {
    event.preventDefault();
    
    if (!checkLogin()) return;
    
    const formData = new FormData(document.getElementById('sellBookForm'));
    
    try {
        const response = await fetch('php/add_book.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Book listed successfully!');
            document.getElementById('sellBookForm').reset();
            document.querySelector('.file-name').textContent = 'No file chosen';
            loadBooksFromDatabase(); 
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error submitting form. Please try again.');
    }
}


function setupEventListeners() {
    
    document.querySelector('.close-modal').addEventListener('click', closeModal);
    
    
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('paymentModal');
        if (event.target === modal) {
            closeModal();
        }
    });
    
   
    const imageInput = document.getElementById('bookImage');
    const fileName = document.querySelector('.file-name');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
            }
        });
    }
    
    
    const sellForm = document.getElementById('sellBookForm');
    if (sellForm) {
        sellForm.addEventListener('submit', submitBookForm);
    }
}
async function buyBook(bookId, title, author, price, sellerId, sellerName) {
    
    currentBook = {
        id: bookId,
        title: title,
        author: author,
        price: parseInt(price),
        sellerId: sellerId,
        sellerName: sellerName,
        total: parseInt(price) + 50 
    };
   
    document.getElementById('modalBookTitle').textContent = currentBook.title;
    document.getElementById('modalBookAuthor').textContent = currentBook.author;
    document.getElementById('modalBookPrice').textContent = currentBook.price;
    document.getElementById('modalBookSeller').textContent = currentBook.sellerName;
    document.getElementById('modalTotalPrice').textContent = currentBook.total;
    
   
    document.getElementById('paymentModal').style.display = 'block';
}

async function processPayment() {
    const mobile = document.getElementById('mobileNumber').value;
    const pin = document.getElementById('paymentPin').value;
    const address = document.getElementById('deliveryAddress').value;
    
    
    if (!mobile.match(/^98[0-9]{8}$/)) {
        alert('Please enter a valid 10-digit Nepali mobile number starting with 98');
        return;
    }
    
    if (!pin) {
        alert('Please enter PIN/OTP');
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
       
        const paymentSuccess = await simulatePaymentAPI(mobile, pin, currentBook.total);
        
        if (paymentSuccess) {
            
            const orderData = {
                book_id: currentBook.id,
                seller_id: currentBook.sellerId,
                price: currentBook.price,
                delivery_charge: 50,
                total_amount: currentBook.total,
                delivery_address: address,
                payment_method: selectedPaymentMethod,
                mobile_number: mobile,
                transaction_id: 'TXN' + Date.now() + Math.floor(Math.random() * 1000)
            };
            
            const orderResponse = await createOrder(orderData);
            
            if (orderResponse.success) {
               
                showOrderSuccess(orderResponse.order_id, orderData.transaction_id);
                
               
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                alert('Order failed: ' + orderResponse.message);
            }
        } else {
            alert('Payment failed. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    } finally {
      
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}


async function createOrder(orderData) {
    const response = await fetch('php/create_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    });
    
    return await response.json();
}

async function simulatePaymentAPI(mobile, pin, amount) {
   
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    
    return Math.random() > 0.1; 
}

function showOrderSuccess(orderId, transactionId) {
    const modalContent = document.querySelector('.modal-content');
    modalContent.innerHTML = `
        <div class="order-success">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Order Confirmed!</h2>
            <div class="order-details">
                <p><strong>Order ID:</strong> #${orderId}</p>
                <p><strong>Transaction ID:</strong> ${transactionId}</p>
                <p><strong>Amount Paid:</strong> NPR ${currentBook.total}</p>
                <p><strong>Delivery to:</strong> ${document.getElementById('deliveryAddress').value}</p>
            </div>
            <div class="seller-contact-info" id="sellerContactInfo">
                <h3>Seller Contact Information</h3>
                <p><i class="fas fa-spinner fa-spin"></i> Loading contact info...</p>
            </div>
            <div class="success-actions">
                <button class="btn primary" onclick="viewMyOrders()">
                    <i class="fas fa-list"></i> View My Orders
                </button>
                <button class="btn secondary" onclick="closeModalAndRefresh()">
                    Continue Shopping
                </button>
            </div>
        </div>
    `;
    
    
    loadSellerContact(currentBook.sellerId, orderId);
}

async function loadSellerContact(sellerId, orderId) {
    try {
        const response = await fetch(`php/get_contact_info.php?user_id=${sellerId}&order_id=${orderId}`);
        const data = await response.json();
        
        if (data.success && data.contact_allowed) {
            const contactDiv = document.getElementById('sellerContactInfo');
            contactDiv.innerHTML = `
                <h3>Seller Contact Information</h3>
                <div class="contact-details">
                    <p><strong>Name:</strong> ${data.user_info.full_name || data.user_info.username}</p>
                    <p><strong>Email:</strong> ${data.user_info.email}</p>
                    ${data.user_info.phone ? `<p><strong>Phone:</strong> ${data.user_info.phone}</p>` : ''}
                    ${data.user_info.location ? `<p><strong>Location:</strong> ${data.user_info.location}</p>` : ''}
                </div>
                <button class="btn small" onclick="sendMessageToSeller(${sellerId}, ${orderId})">
                    <i class="fas fa-envelope"></i> Send Message
                </button>
            `;
        }
    } catch (error) {
        console.error('Error loading contact info:', error);
    }
}

function viewMyOrders() {
    window.location.href = 'my_orders.php';
}

function closeModalAndRefresh() {
    closeModal();
    window.location.reload();
}