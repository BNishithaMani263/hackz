<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/products.php';

$auth = new Auth();
$products = new Products();

$product_id = $_GET['id'] ?? '';

if(empty($product_id)) {
    header('Location: index.php');
    exit;
}

$product = $products->getProduct($product_id);

if(!$product || isset($product['error'])) {
    header('Location: index.php');
    exit;
}

$is_owner = $auth->isLoggedIn() && $_SESSION['user_id'] == $product['seller_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - TradeHub</title>
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
                <?php if($auth->isLoggedIn()): ?>
                    <a href="cart.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge" id="cart-count">0</span>
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
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="index.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['title']); ?></span>
        </div>
    </div>

    <!-- Product Details -->
    <section class="product-detail-section">
        <div class="container">
            <div class="product-detail">
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="<?php echo $product['images'][0]['image_url'] ?? 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" id="main-image">
                    </div>
                    <?php if(count($product['images']) > 1): ?>
                        <div class="thumbnail-gallery">
                            <?php foreach($product['images'] as $index => $image): ?>
                                <img src="<?php echo $image['image_url']; ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeMainImage('<?php echo $image['image_url']; ?>', this)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-header">
                        <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                        <div class="product-meta">
                            <span class="condition-badge condition-<?php echo $product['condition_type']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $product['condition_type'])); ?>
                            </span>
                            <span class="views-count">
                                <i class="fas fa-eye"></i> <?php echo $product['views_count']; ?> views
                            </span>
                        </div>
                    </div>
                    
                    <div class="product-price">
                        <span class="current-price">₹<?php echo number_format($product['price']); ?></span>
                        <?php if($product['original_price'] && $product['original_price'] > $product['price']): ?>
                            <span class="original-price">₹<?php echo number_format($product['original_price']); ?></span>
                            <span class="discount"><?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-details">
                        <div class="detail-item">
                            <strong>Category:</strong>
                            <span><?php echo htmlspecialchars($product['category_name']); ?></span>
                        </div>
                        <?php if($product['brand']): ?>
                        <div class="detail-item">
                            <strong>Brand:</strong>
                            <span><?php echo htmlspecialchars($product['brand']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($product['model']): ?>
                        <div class="detail-item">
                            <strong>Model:</strong>
                            <span><?php echo htmlspecialchars($product['model']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($product['year_of_purchase']): ?>
                        <div class="detail-item">
                            <strong>Year of Purchase:</strong>
                            <span><?php echo $product['year_of_purchase']; ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <strong>Negotiable:</strong>
                            <span><?php echo $product['negotiable'] ? 'Yes' : 'No'; ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Location:</strong>
                            <span><?php echo htmlspecialchars($product['location']); ?></span>
                        </div>
                    </div>
                    
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <div class="seller-info">
                        <h3>Seller Information</h3>
                        <div class="seller-details">
                            <div class="seller-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="seller-info-text">
                                <h4><?php echo htmlspecialchars($product['seller_name']); ?></h4>
                                <p>@<?php echo htmlspecialchars($product['seller_username']); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($product['seller_city'] . ', ' . $product['seller_state']); ?></p>
                            </div>
                        </div>
                        <div class="seller-actions">
                            <button class="btn btn-outline" onclick="contactSeller(<?php echo $product['seller_id']; ?>)">
                                <i class="fas fa-comments"></i> Contact Seller
                            </button>
                            <button class="btn btn-outline" onclick="viewSellerProfile(<?php echo $product['seller_id']; ?>)">
                                <i class="fas fa-user"></i> View Profile
                            </button>
                        </div>
                    </div>
                    
                    <?php if(!$is_owner): ?>
                    <div class="product-actions">
                        <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn btn-outline btn-lg" onclick="toggleFavorite(<?php echo $product['id']; ?>, this)">
                            <i class="far fa-heart"></i> Add to Favorites
                        </button>
                        <button class="btn btn-secondary btn-lg" onclick="makeOffer(<?php echo $product['id']; ?>)">
                            <i class="fas fa-handshake"></i> Make Offer
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="owner-actions">
                        <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                        <button class="btn btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete Product
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products">
        <div class="container">
            <h2>Related Products</h2>
            <div class="products-grid" id="related-products">
                <!-- Related products will be loaded here -->
            </div>
        </div>
    </section>

    <script src="assets/js/main.js"></script>
    <script>
        function changeMainImage(imageUrl, thumbnail) {
            document.getElementById('main-image').src = imageUrl;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }
        
        function contactSeller(sellerId) {
            if(!isLoggedIn()) {
                showNotification('Please login to contact seller', 'warning');
                return;
            }
            // Implement contact seller functionality
            showNotification('Contact seller feature coming soon', 'info');
        }
        
        function viewSellerProfile(sellerId) {
            window.location.href = `seller.php?id=${sellerId}`;
        }
        
        function makeOffer(productId) {
            if(!isLoggedIn()) {
                showNotification('Please login to make an offer', 'warning');
                return;
            }
            // Implement make offer functionality
            showNotification('Make offer feature coming soon', 'info');
        }
        
        function deleteProduct(productId) {
            if(confirm('Are you sure you want to delete this product?')) {
                fetch(`api/products.php?id=${productId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'my-listings.php';
                        }, 2000);
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
        
        // Load related products
        function loadRelatedProducts() {
            fetch(`api/products.php?category_id=<?php echo $product['category_id']; ?>&limit=4`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const container = document.getElementById('related-products');
                    container.innerHTML = '';
                    
                    data.products.forEach(product => {
                        if(product.id != <?php echo $product['id']; ?>) {
                            const productCard = createProductCard(product);
                            container.appendChild(productCard);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading related products:', error);
            });
        }
        
        function createProductCard(product) {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
                <div class="product-image">
                    <img src="${product.primary_image || 'assets/images/placeholder.jpg'}" alt="${product.title}">
                    <div class="product-actions">
                        <button class="action-btn favorite-btn" data-product-id="${product.id}">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="product.php?id=${product.id}">${product.title}</a>
                    </h3>
                    <p class="product-category">${product.category_name}</p>
                    <div class="product-price">
                        <span class="current-price">₹${formatPrice(product.price)}</span>
                    </div>
                </div>
            `;
            
            // Add event listeners
            card.querySelector('.favorite-btn').addEventListener('click', function(e) {
                e.preventDefault();
                toggleFavorite(product.id, this);
            });
            
            return card;
        }
        
        // Load related products on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRelatedProducts();
        });
    </script>
    
    <style>
        .breadcrumb {
            background-color: #f9fafb;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            color: #10b981;
        }
        
        .breadcrumb span {
            color: #9ca3af;
            margin: 0 0.5rem;
        }
        
        .product-detail-section {
            padding: 3rem 0;
        }
        
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }
        
        .product-gallery {
            position: sticky;
            top: 2rem;
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .thumbnail-gallery {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s ease;
        }
        
        .thumbnail.active,
        .thumbnail:hover {
            border-color: #10b981;
        }
        
        .product-header {
            margin-bottom: 1.5rem;
        }
        
        .product-header h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .product-meta {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .views-count {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .product-price {
            margin-bottom: 2rem;
        }
        
        .current-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .original-price {
            font-size: 1.5rem;
            color: #9ca3af;
            text-decoration: line-through;
            margin-left: 1rem;
        }
        
        .discount {
            background-color: #ef4444;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-left: 1rem;
        }
        
        .product-details {
            background-color: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .product-description {
            margin-bottom: 2rem;
        }
        
        .product-description h3 {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .seller-info {
            background-color: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .seller-info h3 {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .seller-details {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .seller-avatar {
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
        
        .seller-info-text h4 {
            margin-bottom: 0.25rem;
            color: #1f2937;
        }
        
        .seller-info-text p {
            margin-bottom: 0.25rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .seller-actions {
            display: flex;
            gap: 1rem;
        }
        
        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .owner-actions {
            display: flex;
            gap: 1rem;
        }
        
        .related-products {
            background-color: #f9fafb;
            padding: 3rem 0;
        }
        
        .related-products h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1f2937;
        }
        
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .product-gallery {
                position: static;
            }
            
            .main-image {
                height: 300px;
            }
            
            .current-price {
                font-size: 2rem;
            }
            
            .seller-actions,
            .product-actions,
            .owner-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
