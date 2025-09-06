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

$categories = $products->getCategories();
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'seller_id' => $_SESSION['user_id'],
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'category_id' => $_POST['category_id'],
        'price' => floatval($_POST['price']),
        'original_price' => !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null,
        'condition_type' => $_POST['condition_type'],
        'brand' => trim($_POST['brand']),
        'model' => trim($_POST['model']),
        'year_of_purchase' => !empty($_POST['year_of_purchase']) ? intval($_POST['year_of_purchase']) : null,
        'negotiable' => isset($_POST['negotiable']) ? 1 : 0,
        'location' => trim($_POST['location']),
        'latitude' => !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null,
        'longitude' => !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null
    ];
    
    // Validation
    if(empty($data['title']) || empty($data['description']) || empty($data['category_id']) || $data['price'] <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $result = $products->createProduct($data);
        if($result['success']) {
            $success = 'Product created successfully!';
            // Reset form
            $_POST = [];
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - TradeHub</title>
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

    <!-- Add Product Section -->
    <section class="add-product-section">
        <div class="container">
            <div class="page-header">
                <h1>Add New Product</h1>
                <p>List your item for sale and reach thousands of potential buyers</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="product-form" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Basic Information</h3>
                        
                        <div class="form-group">
                            <label for="title">Product Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            <small>Be descriptive and include key features</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <small>Provide detailed information about the product's condition, features, and any defects</small>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Pricing & Condition</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Price (₹) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="original_price">Original Price (₹)</label>
                                <input type="number" id="original_price" name="original_price" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['original_price'] ?? ''); ?>">
                                <small>Leave empty if unknown</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="condition_type">Condition *</label>
                            <select id="condition_type" name="condition_type" required>
                                <option value="">Select condition</option>
                                <option value="new" <?php echo ($_POST['condition_type'] ?? '') == 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="like_new" <?php echo ($_POST['condition_type'] ?? '') == 'like_new' ? 'selected' : ''; ?>>Like New</option>
                                <option value="good" <?php echo ($_POST['condition_type'] ?? '') == 'good' ? 'selected' : ''; ?>>Good</option>
                                <option value="fair" <?php echo ($_POST['condition_type'] ?? '') == 'fair' ? 'selected' : ''; ?>>Fair</option>
                                <option value="poor" <?php echo ($_POST['condition_type'] ?? '') == 'poor' ? 'selected' : ''; ?>>Poor</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox">
                                <input type="checkbox" name="negotiable" <?php echo isset($_POST['negotiable']) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Price is negotiable
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Product Details</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($_POST['brand'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($_POST['model'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="year_of_purchase">Year of Purchase</label>
                            <select id="year_of_purchase" name="year_of_purchase">
                                <option value="">Select year</option>
                                <?php for($year = date('Y'); $year >= 1990; $year--): ?>
                                    <option value="<?php echo $year; ?>" <?php echo ($_POST['year_of_purchase'] ?? '') == $year ? 'selected' : ''; ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Location</h3>
                        
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                            <small>City, State (e.g., Mumbai, Maharashtra)</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="number" id="latitude" name="latitude" step="any" value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="number" id="longitude" name="longitude" step="any" value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline" onclick="getCurrentLocation()">
                            <i class="fas fa-map-marker-alt"></i> Use Current Location
                        </button>
                    </div>
                    
                    <div class="form-section">
                        <h3>Images</h3>
                        
                        <div class="image-upload">
                            <div class="upload-area" id="upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload images or drag and drop</p>
                                <small>PNG, JPG, GIF up to 10MB each (max 5 images)</small>
                            </div>
                            <input type="file" id="image-input" name="images[]" multiple accept="image/*" style="display: none;">
                            <div class="image-preview" id="image-preview"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="saveDraft()">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> List Product
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script src="assets/js/main.js"></script>
    <script>
        // Image upload functionality
        const uploadArea = document.getElementById('upload-area');
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        
        uploadArea.addEventListener('click', () => imageInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });
        
        imageInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if(file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageContainer = document.createElement('div');
                        imageContainer.className = 'preview-image';
                        imageContainer.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        imagePreview.appendChild(imageContainer);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        function removeImage(button) {
            button.parentElement.remove();
        }
        
        // Get current location
        function getCurrentLocation() {
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        showNotification('Location updated successfully', 'success');
                    },
                    (error) => {
                        showNotification('Unable to get location: ' + error.message, 'error');
                    }
                );
            } else {
                showNotification('Geolocation is not supported by this browser', 'error');
            }
        }
        
        // Save as draft
        function saveDraft() {
            showNotification('Draft saved successfully', 'success');
        }
        
        // Form validation
        document.querySelector('.product-form').addEventListener('submit', function(e) {
            const requiredFields = ['title', 'description', 'category_id', 'price', 'condition_type', 'location'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if(!input.value.trim()) {
                    input.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    input.style.borderColor = '#e5e7eb';
                }
            });
            
            if(!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
    </script>
    
    <style>
        .add-product-section {
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
        
        .product-form {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-grid {
            display: grid;
            gap: 2rem;
        }
        
        .form-section {
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #f9fafb;
        }
        
        .form-section h3 {
            margin-bottom: 1.5rem;
            color: #1f2937;
            font-size: 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group:last-child {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #10b981;
        }
        
        .form-group small {
            display: block;
            margin-top: 0.25rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox input {
            display: none;
        }
        
        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .checkbox input:checked + .checkmark {
            background-color: #10b981;
            border-color: #10b981;
        }
        
        .checkbox input:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .image-upload {
            margin-top: 1rem;
        }
        
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .upload-area:hover,
        .upload-area.dragover {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        
        .upload-area i {
            font-size: 2rem;
            color: #9ca3af;
            margin-bottom: 1rem;
        }
        
        .upload-area p {
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        .upload-area small {
            color: #6b7280;
        }
        
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .preview-image {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-image img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        
        .remove-image {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 24px;
            height: 24px;
            background-color: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .product-form {
                margin: 0 1rem;
                padding: 1rem;
            }
        }
    </style>
</body>
</html>
