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

$user_products = $products->getUserProducts($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - TradeHub</title>
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
                        <a href="my-listings.php" class="active"><i class="fas fa-list"></i> My Listings</a>
                        <a href="purchases.php"><i class="fas fa-shopping-bag"></i> Purchases</a>
                        <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                        <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- My Listings Section -->
    <section class="my-listings-section">
        <div class="container">
            <div class="page-header">
                <h1>My Listings</h1>
                <p>Manage your product listings and track their performance</p>
                <a href="add-product.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            
            <?php if(empty($user_products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h2>No products listed yet</h2>
                    <p>Start selling by adding your first product listing</p>
                    <a href="add-product.php" class="btn btn-primary">Add Your First Product</a>
                </div>
            <?php else: ?>
                <div class="listings-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count($user_products); ?></h3>
                            <p>Total Listings</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo array_sum(array_column($user_products, 'views_count')); ?></h3>
                            <p>Total Views</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo array_sum(array_column($user_products, 'likes_count')); ?></h3>
                            <p>Total Likes</p>
                        </div>
                    </div>
                </div>
                
                <div class="listings-grid">
                    <?php foreach($user_products as $product): ?>
                        <div class="listing-card" data-product-id="<?php echo $product['id']; ?>">
                            <div class="listing-image">
                                <img src="<?php echo $product['primary_image'] ?: 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                <div class="listing-status status-<?php echo $product['status']; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </div>
                            </div>
                            
                            <div class="listing-info">
                                <h3 class="listing-title">
                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars($product['title']); ?>
                                    </a>
                                </h3>
                                <p class="listing-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                
                                <div class="listing-price">
                                    <span class="current-price">₹<?php echo number_format($product['price']); ?></span>
                                    <?php if($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">₹<?php echo number_format($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="listing-stats">
                                    <span><i class="fas fa-eye"></i> <?php echo $product['views_count']; ?></span>
                                    <span><i class="fas fa-heart"></i> <?php echo $product['likes_count']; ?></span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($product['created_at'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="listing-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="assets/js/main.js"></script>
    <script>
        function deleteProduct(productId) {
            if(confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                fetch(`api/products.php?id=${productId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showNotification(data.message, 'success');
                        // Remove the product card from the page
                        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                        if(productCard) {
                            productCard.remove();
                        }
                        // Update stats
                        updateStats();
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
        
        function updateStats() {
            // This would update the statistics in a real implementation
            location.reload();
        }
    </script>
    
    <style>
        .my-listings-section {
            padding: 3rem 0;
            background-color: #f9fafb;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .page-header p {
            color: #6b7280;
            margin: 0;
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
            color: #d1d5db;
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
        
        .listings-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: #10b981;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .stat-content p {
            color: #6b7280;
            margin: 0;
        }
        
        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .listing-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        
        .listing-card:hover {
            transform: translateY(-4px);
        }
        
        .listing-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .listing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .listing-status {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #10b981;
            color: white;
        }
        
        .status-sold {
            background-color: #6b7280;
            color: white;
        }
        
        .status-draft {
            background-color: #f59e0b;
            color: white;
        }
        
        .status-rejected {
            background-color: #ef4444;
            color: white;
        }
        
        .listing-info {
            padding: 1.5rem;
        }
        
        .listing-title {
            margin-bottom: 0.5rem;
        }
        
        .listing-title a {
            color: #1f2937;
            text-decoration: none;
            font-weight: 600;
        }
        
        .listing-title a:hover {
            color: #10b981;
        }
        
        .listing-category {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .listing-price {
            margin-bottom: 1rem;
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
        
        .listing-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }
        
        .listing-stats span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .listing-actions {
            padding: 1rem 1.5rem;
            background-color: #f9fafb;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .listing-actions .btn {
            flex: 1;
            min-width: 80px;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .listings-grid {
                grid-template-columns: 1fr;
            }
            
            .listing-actions {
                flex-direction: column;
            }
            
            .listing-actions .btn {
                flex: none;
            }
        }
    </style>
</body>
</html>
