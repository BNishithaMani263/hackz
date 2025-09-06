<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/products.php';

$auth = new Auth();
$products = new Products();

// Get categories for filter
$categories = $products->getCategories();

// Get filter parameters
$filters = [
    'search' => $_GET['search'] ?? '',
    'category_id' => $_GET['category'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'condition' => $_GET['condition'] ?? '',
    'location' => $_GET['location'] ?? '',
    'limit' => 20
];

// Get products
$products_list = $products->getProducts($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TradeHub - Sustainable Second-Hand Marketplace</title>
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
            
            <div class="nav-search">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover Amazing Second-Hand Finds</h1>
            <p>Join India's largest sustainable marketplace. Buy, sell, and trade pre-owned items while contributing to a greener future.</p>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Active Users</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50K+</span>
                    <span class="stat-label">Products Listed</span>
                </div>
                <div class="stat">
                    <span class="stat-number">₹2M+</span>
                    <span class="stat-label">Saved by Users</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $filters['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Price Range</label>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($filters['min_price']); ?>">
                        <span>to</span>
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($filters['max_price']); ?>">
                    </div>
                </div>
                
                <div class="filter-group">
                    <label>Condition</label>
                    <select name="condition">
                        <option value="">All Conditions</option>
                        <option value="new" <?php echo $filters['condition'] == 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="like_new" <?php echo $filters['condition'] == 'like_new' ? 'selected' : ''; ?>>Like New</option>
                        <option value="good" <?php echo $filters['condition'] == 'good' ? 'selected' : ''; ?>>Good</option>
                        <option value="fair" <?php echo $filters['condition'] == 'fair' ? 'selected' : ''; ?>>Fair</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="Enter city" value="<?php echo htmlspecialchars($filters['location']); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="index.php" class="btn btn-outline">Clear</a>
            </form>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Products</h2>
                <div class="view-options">
                    <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
                    <button class="view-btn" data-view="list"><i class="fas fa-list"></i></button>
                </div>
            </div>
            
            <div class="products-grid" id="products-container">
                <?php if(is_array($products_list) && !isset($products_list['error'])): ?>
                    <?php foreach($products_list as $product): ?>
                        <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                            <div class="product-image">
                                <img src="<?php echo $product['primary_image'] ?: 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="action-btn quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="product-badge">
                                    <span class="condition-badge condition-<?php echo $product['condition_type']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $product['condition_type'])); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars($product['title']); ?>
                                    </a>
                                </h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                <div class="product-price">
                                    <span class="current-price">₹<?php echo number_format($product['price']); ?></span>
                                    <?php if($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">₹<?php echo number_format($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-meta">
                                    <span class="seller">by <?php echo htmlspecialchars($product['seller_username']); ?></span>
                                    <span class="location"><?php echo htmlspecialchars($product['location']); ?></span>
                                </div>
                                <div class="product-stats">
                                    <span><i class="fas fa-eye"></i> <?php echo $product['views_count']; ?></span>
                                    <span><i class="fas fa-heart"></i> <?php echo $product['likes_count']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your search criteria or browse all categories</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="load-more">
                <button class="btn btn-outline" id="load-more-btn">Load More Products</button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2>Why Choose TradeHub?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Transactions</h3>
                    <p>Safe and secure payment processing with buyer protection</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Eco-Friendly</h3>
                    <p>Reduce waste and promote sustainable consumption</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Optimized</h3>
                    <p>Seamless experience across all devices</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Direct Messaging</h3>
                    <p>Chat directly with sellers for negotiations</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TradeHub</h3>
                    <p>India's leading second-hand marketplace for sustainable living</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="how-it-works.php">How It Works</a></li>
                        <li><a href="safety.php">Safety Tips</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-envelope"></i> support@tradehub.com</p>
                    <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                    <p><i class="fas fa-map-marker-alt"></i> Mumbai, India</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TradeHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
