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

$favorites = $products->getUserFavorites($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - TradeHub</title>
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
                <a href="cart.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="favorites.php" class="nav-link active">
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

    <!-- Favorites Section -->
    <section class="favorites-section">
        <div class="container">
            <div class="page-header">
                <h1>My Favorites</h1>
                <p>Your saved items for easy access</p>
            </div>
            
            <?php if(empty($favorites)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <h2>No favorites yet</h2>
                    <p>Start exploring and add items you love to your favorites</p>
                    <a href="index.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="favorites-header">
                    <div class="favorites-count">
                        <h3><?php echo count($favorites); ?> Saved Items</h3>
                    </div>
                    <div class="favorites-actions">
                        <button class="btn btn-outline" onclick="clearAllFavorites()">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    </div>
                </div>
                
                <div class="favorites-grid">
                    <?php foreach($favorites as $product): ?>
                        <div class="favorite-card" data-product-id="<?php echo $product['id']; ?>">
                            <div class="favorite-image">
                                <img src="<?php echo $product['primary_image'] ?: 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                <div class="favorite-actions">
                                    <button class="action-btn" onclick="removeFromFavorites(<?php echo $product['id']; ?>, this)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                                <div class="product-badge">
                                    <span class="condition-badge condition-<?php echo $product['condition_type']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $product['condition_type'])); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="favorite-info">
                                <h3 class="favorite-title">
                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars($product['title']); ?>
                                    </a>
                                </h3>
                                <p class="favorite-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                
                                <div class="favorite-price">
                                    <span class="current-price">₹<?php echo number_format($product['price']); ?></span>
                                    <?php if($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">₹<?php echo number_format($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="favorite-meta">
                                    <span class="seller">by <?php echo htmlspecialchars($product['seller_username']); ?></span>
                                    <span class="location"><?php echo htmlspecialchars($product['location']); ?></span>
                                </div>
                                
                                <div class="favorite-stats">
                                    <span><i class="fas fa-eye"></i> <?php echo $product['views_count']; ?></span>
                                    <span><i class="fas fa-heart"></i> <?php echo $product['likes_count']; ?></span>
                                    <span class="favorited-date">Added <?php echo date('M j', strtotime($product['favorited_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="assets/js/main.js"></script>
    <script>
        function removeFromFavorites(productId, button) {
            fetch('api/favorites.php', {
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
                    showNotification(data.message, 'success');
                    // Remove the card from the page
                    const card = button.closest('.favorite-card');
                    card.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        card.remove();
                        updateFavoritesCount();
                    }, 300);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        function clearAllFavorites() {
            if(confirm('Are you sure you want to remove all items from your favorites?')) {
                // This would require a new API endpoint to clear all favorites
                showNotification('Clear all favorites feature coming soon', 'info');
            }
        }
        
        function updateFavoritesCount() {
            const count = document.querySelectorAll('.favorite-card').length;
            const countElement = document.querySelector('.favorites-count h3');
            if(countElement) {
                countElement.textContent = count + ' Saved Items';
            }
            
            if(count === 0) {
                location.reload();
            }
        }
    </script>
    
    <style>
        .favorites-section {
            padding: 3rem 0;
            background-color: #f9fafb;
            min-height: 100vh;
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
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #fbbf24;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .favorites-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .favorites-count h3 {
            color: #1f2937;
            margin: 0;
        }
        
        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .favorite-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .favorite-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .favorite-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .favorite-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .favorite-card:hover .favorite-image img {
            transform: scale(1.05);
        }
        
        .favorite-actions {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .favorite-card:hover .favorite-actions {
            opacity: 1;
        }
        
        .action-btn {
            width: 2.5rem;
            height: 2.5rem;
            border: none;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.9);
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }
        
        .action-btn:hover {
            background-color: #10b981;
            color: white;
            transform: scale(1.1);
        }
        
        .action-btn:first-child:hover {
            background-color: #ef4444;
        }
        
        .product-badge {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
        }
        
        .condition-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .condition-new { background-color: #10b981; color: white; }
        .condition-like_new { background-color: #3b82f6; color: white; }
        .condition-good { background-color: #f59e0b; color: white; }
        .condition-fair { background-color: #f59e0b; color: white; }
        .condition-poor { background-color: #ef4444; color: white; }
        
        .favorite-info {
            padding: 1.5rem;
        }
        
        .favorite-title {
            margin-bottom: 0.5rem;
        }
        
        .favorite-title a {
            color: #1f2937;
            text-decoration: none;
            font-weight: 600;
        }
        
        .favorite-title a:hover {
            color: #10b981;
        }
        
        .favorite-category {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .favorite-price {
            margin-bottom: 0.75rem;
        }
        
        .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .original-price {
            font-size: 1rem;
            color: #9ca3af;
            text-decoration: line-through;
            margin-left: 0.5rem;
        }
        
        .favorite-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .favorite-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }
        
        .favorite-stats span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .favorited-date {
            margin-left: auto;
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.8);
            }
        }
        
        @media (max-width: 768px) {
            .favorites-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .favorites-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
