
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


async function showContactModal(userId, orderId) {
    const modal = document.getElementById('contactModal');
    const detailsDiv = document.getElementById('contactDetails');
    
    modal.style.display = 'block';
    
    try {
        const response = await fetch(`php/get_contact_info.php?user_id=${userId}&order_id=${orderId}`);
        const data = await response.json();
        
        if (data.success && data.contact_allowed) {
            detailsDiv.innerHTML = `
                <div class="contact-card">
                    <h3>${data.user_info.full_name || data.user_info.username}</h3>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>${data.user_info.email}</span>
                    </div>
                    ${data.user_info.phone ? `
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>${data.user_info.phone}</span>
                    </div>
                    ` : ''}
                    ${data.user_info.location ? `
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${data.user_info.location}</span>
                    </div>
                    ` : ''}
                    ${data.order_info ? `
                    <div class="order-contact">
                        <h4>Order Contact Details</h4>
                        <p><strong>Delivery Address:</strong> ${data.order_info.delivery_address}</p>
                        <p><strong>Mobile:</strong> ${data.order_info.mobile_number}</p>
                    </div>
                    ` : ''}
                    <div class="contact-actions">
                        <button class="btn primary" onclick="sendMessage(${userId}, ${orderId})">
                            <i class="fas fa-message"></i> Send Message
                        </button>
                        ${data.user_info.phone ? `
                        <a href="tel:${data.user_info.phone}" class="btn secondary">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                        ` : ''}
                    </div>
                </div>
            `;
        } else {
            detailsDiv.innerHTML = `
                <div class="alert">
                    <i class="fas fa-info-circle"></i>
                    <p>Contact information is only available for active orders.</p>
                </div>
            `;
        }
    } catch (error) {
        detailsDiv.innerHTML = `
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <p>Failed to load contact information.</p>
            </div>
        `;
    }
}

function closeContactModal() {
    document.getElementById('contactModal').style.display = 'none';
}

function sendMessage(receiverId, orderId) {
    const message = prompt('Enter your message:');
    if (message && message.trim()) {
       
        fetch('php/send_message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                receiver_id: receiverId,
                order_id: orderId,
                message: message.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Message sent successfully!');
                closeContactModal();
            } else {
                alert('Failed to send message: ' + data.message);
            }
        });
    }
}


async function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to update this order status?')) {
        try {
            const response = await fetch('php/update_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ order_id: orderId, status: status })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Order status updated successfully!');
                window.location.reload();
            } else {
                alert('Failed to update: ' + data.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }
}