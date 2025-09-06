<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/products.php';

$auth = new Auth();
$products = new Products();

if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$cart = $products->getUserCart($_SESSION['user_id']);
$total = 0;
foreach($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TradeHub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">
                    <i class="fas fa-recycle"></i>
                    <span>TradeHub</span>
                </a>
            </div>
            
            <div class="nav-actions">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="favorites.php" class="nav-link">
                    <i class="fas fa-heart"></i>
                </a>
                <div class="nav-dropdown">
                    <button class="nav-link dropdown-toggle">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="my-listings.php"><i class="fas fa-list"></i> My Listings</a>
                        <a href="purchases.php"><i class="fas fa-shopping-bag"></i> Purchases</a>
                        <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                        <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <div class="page-header">
                <h1>Shopping Cart</h1>
                <p>Review your selected items before checkout</p>
            </div>
            
            <?php if(empty($cart)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="index.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <?php foreach($cart as $item): ?>
                            <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo $item['primary_image'] ?: 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                
                                <div class="item-details">
                                    <h3 class="item-title">
                                        <a href="product.php?id=<?php echo $item['product_id']; ?>">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="item-seller">Sold by <?php echo htmlspecialchars($item['seller_username']); ?></p>
                                    <div class="item-price">
                                        <span class="current-price">₹<?php echo number_format($item['price']); ?></span>
                                        <?php if($item['negotiable']): ?>
                                            <span class="negotiable">Negotiable</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="item-quantity">
                                    <label>Quantity:</label>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                    </div>
                                </div>
                                
                                <div class="item-total">
                                    <span class="total-price">₹<?php echo number_format($item['price'] * $item['quantity']); ?></span>
                                </div>
                                
                                <div class="item-actions">
                                    <button class="action-btn" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="action-btn" onclick="addToFavorites(<?php echo $item['product_id']; ?>)">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Order Summary</h3>
                            
                            <div class="summary-row">
                                <span>Subtotal (<?php echo count($cart); ?> items)</span>
                                <span>₹<?php echo number_format($total); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Platform Fee (2%)</span>
                                <span>₹<?php echo number_format($total * 0.02); ?></span>
                            </div>
                            
                            <div class="summary-row total">
                                <span>Total</span>
                                <span>₹<?php echo number_format($total * 1.02); ?></span>
                            </div>
                            
                            <div class="checkout-actions">
                                <button class="btn btn-primary btn-full" onclick="proceedToCheckout()">
                                    <i class="fas fa-credit-card"></i>
                                    Proceed to Checkout
                                </button>
                                <button class="btn btn-outline btn-full" onclick="clearCart()">
                                    <i class="fas fa-trash"></i>
                                    Clear Cart
                                </button>
                            </div>
                            
                            <div class="payment-methods">
                                <h4>Accepted Payment Methods</h4>
                                <div class="payment-icons">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                    <i class="fab fa-cc-amex"></i>
                                    <i class="fas fa-university"></i>
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="security-info">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <h4>Secure Payment</h4>
                                    <p>Your payment information is encrypted and secure</p>
                                </div>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-undo"></i>
                                <div>
                                    <h4>Easy Returns</h4>
                                    <p>30-day return policy for most items</p>
                                </div>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-headset"></i>
                                <div>
                                    <h4>24/7 Support</h4>
                                    <p>Get help whenever you need it</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="assets/js/main.js"></script>
    <script>
        function updateQuantity(productId, change) {
            const quantityElement = document.querySelector(`[data-product-id="${productId}"] .quantity`);
            const currentQuantity = parseInt(quantityElement.textContent);
            const newQuantity = Math.max(1, currentQuantity + change);
            
            if(newQuantity !== currentQuantity) {
                fetch('api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_quantity',
                        product_id: productId,
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        quantityElement.textContent = newQuantity;
                        updateItemTotal(productId, newQuantity);
                        updateCartSummary();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
        }
        
        function updateItemTotal(productId, quantity) {
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            const price = parseFloat(item.querySelector('.current-price').textContent.replace(/[₹,]/g, ''));
            const totalElement = item.querySelector('.total-price');
            totalElement.textContent = '₹' + (price * quantity).toLocaleString('en-IN');
        }
        
        function removeFromCart(productId) {
            if(confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'remove',
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.querySelector(`[data-product-id="${productId}"]`).remove();
                        updateCartSummary();
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
        }
        
        function addToFavorites(productId) {
            fetch('api/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        function clearCart() {
            if(confirm('Are you sure you want to clear your entire cart?')) {
                fetch('api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'clear'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
        }
        
        function proceedToCheckout() {
            showNotification('Checkout feature coming soon!', 'info');
        }
        
        function updateCartSummary() {
            // This would update the cart summary in a real implementation
            // For now, we'll just reload the page
            location.reload();
        }
    </script>
    
    <style>
        .cart-section {
            padding: 3rem 0;
            min-height: 60vh;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .page-header p {
            color: #6b7280;
            font-size: 1.125rem;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .empty-cart h2 {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .empty-cart p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .cart-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }
        
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto auto auto;
            gap: 1.5rem;
            align-items: center;
            padding: 1.5rem;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-title {
            margin-bottom: 0.5rem;
        }
        
        .item-title a {
            color: #1f2937;
            text-decoration: none;
            font-weight: 600;
        }
        
        .item-title a:hover {
            color: #10b981;
        }
        
        .item-seller {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .item-price {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .negotiable {
            background-color: #f3f4f6;
            color: #6b7280;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .item-quantity {
            text-align: center;
        }
        
        .item-quantity label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #d1d5db;
            background-color: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .quantity-btn:hover {
            background-color: #f3f4f6;
            border-color: #10b981;
        }
        
        .quantity {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }
        
        .item-total {
            text-align: right;
        }
        
        .total-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .item-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #d1d5db;
            background-color: white;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            background-color: #f3f4f6;
            border-color: #10b981;
        }
        
        .cart-summary {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .summary-card {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .summary-card h3 {
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            font-weight: 700;
            font-size: 1.125rem;
            color: #1f2937;
            border-top: 2px solid #e5e7eb;
            margin-top: 0.5rem;
            padding-top: 1rem;
        }
        
        .checkout-actions {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .payment-methods {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .payment-methods h4 {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .payment-icons {
            display: flex;
            gap: 1rem;
            font-size: 1.5rem;
            color: #6b7280;
        }
        
        .security-info {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .security-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .security-item:last-child {
            margin-bottom: 0;
        }
        
        .security-item i {
            color: #10b981;
            font-size: 1.25rem;
            margin-top: 0.25rem;
        }
        
        .security-item h4 {
            margin-bottom: 0.25rem;
            color: #1f2937;
        }
        
        .security-item p {
            color: #6b7280;
            font-size: 0.875rem;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .cart-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .cart-item {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .item-image {
                width: 100%;
                height: 200px;
            }
            
            .item-quantity,
            .item-total,
            .item-actions {
                justify-self: center;
            }
            
            .item-actions {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</body>
</html>
