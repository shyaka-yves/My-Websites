<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;
$category_id = null;

if ($category_filter) {
    // The font family is set in the CSS, not in this PHP logic.
    // No changes needed here for font family.
    $category = getCategoryBySlug($conn, $category_filter);
    if ($category) {
        $category_id = $category['id'];
    }
}

// Get products
$products = getProducts($conn, $category_id);
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="display-4">Shop Our Products</h1>
                    <p class="lead">Discover our premium collection of gifts, awards, and custom printing solutions.</p>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search products..." onkeyup="searchProducts()">
                        <button class="btn btn-success" type="button" onclick="searchProducts()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="shop.php" class="list-group-item list-group-item-action <?php echo !$category_filter ? 'active' : ''; ?>">
                                All Products
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="shop.php?category=<?php echo $category['slug']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo $category_filter === $category['slug'] ? 'active' : ''; ?>">
                                    <?php echo $category['name']; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Price Range</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="minPrice" class="form-label">Min Price</label>
                            <input type="number" class="form-control" id="minPrice" placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label for="maxPrice" class="form-label">Max Price</label>
                            <input type="number" class="form-control" id="maxPrice" placeholder="1000">
                        </div>
                        <button class="btn btn-success w-100 mb-2" onclick="filterByPrice()">Apply Filter</button>
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-md-9">
                <!-- Search Results Container -->
                <div id="searchResults"></div>

                <!-- Products Display -->
                <div id="productsDisplay">
                    <?php if (empty($products)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h3>No products found</h3>
                            <p class="text-muted">Try adjusting your search criteria or browse all products.</p>
                            <a href="shop.php" class="btn btn-success">View All Products</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card product-card h-100">
                                        <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['title']; ?>">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                            <p class="card-text"><?php echo substr($product['description'], 0, 100); ?>...</p>
                                            <div class="mt-auto">
                                                <p class="price"><?php echo formatPrice($product['price']); ?></p>
                                                <div class="d-grid">
                                                    <button class="btn btn-success add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        let searchTimeout;

        // Search products function
        function searchProducts() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = document.getElementById('searchInput').value;
                const minPrice = document.getElementById('minPrice').value;
                const maxPrice = document.getElementById('maxPrice').value;
                
                const formData = new FormData();
                formData.append('action', 'search_products');
                formData.append('search_term', searchTerm);
                formData.append('min_price', minPrice);
                formData.append('max_price', maxPrice);
                formData.append('category_id', '<?php echo $category_id; ?>');

                fetch('ajax/filter_products.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFilteredProducts(data.products, data.search_term);
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 300); // Debounce search for better performance
        }

        // Price filter function
        function filterByPrice() {
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const searchTerm = document.getElementById('searchInput').value;
            
            const formData = new FormData();
            formData.append('action', 'search_products');
            formData.append('search_term', searchTerm);
            formData.append('min_price', minPrice);
            formData.append('max_price', maxPrice);
            formData.append('category_id', '<?php echo $category_id; ?>');

            fetch('ajax/filter_products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayFilteredProducts(data.products, data.search_term);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Display filtered products
        function displayFilteredProducts(products, searchTerm = '') {
            const container = document.getElementById('productsDisplay');
            
            if (products.length === 0) {
                let message = 'No products found';
                if (searchTerm) {
                    message = `No products found for "${searchTerm}"`;
                }
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h3>${message}</h3>
                        <p class="text-muted">Try adjusting your search criteria or price range.</p>
                        <button class="btn btn-success" onclick="clearFilters()">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </button>
                    </div>
                `;
                return;
            }

            let html = '';
            if (searchTerm) {
                html += `<div class="mb-3">
                    <h5>Search Results for "${searchTerm}" (${products.length} products found)</h5>
                </div>`;
            }
            
            html += '<div class="row">';
            products.forEach(product => {
                html += `
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="${product.image}" class="card-img-top" alt="${product.title}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${product.title}</h5>
                                <p class="card-text">${product.description.substring(0, 100)}...</p>
                                <div class="mt-auto">
                                    <p class="price">${parseFloat(product.price).toFixed(2)} Rwf</p>
                                    <div class="d-grid">
                                        <button class="btn btn-success add-to-cart" data-product-id="${product.id}">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;

            // Reattach event listeners
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;
                    addToCart(productId, 1);
                });
            });
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            
            // Reload the page to show all products
            window.location.reload();
        }

        // Add event listeners for price filter inputs
        document.addEventListener('DOMContentLoaded', function() {
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            
            // Auto-filter when price inputs change
            minPriceInput.addEventListener('input', function() {
                if (this.value || maxPriceInput.value) {
                    filterByPrice();
                }
            });
            
            maxPriceInput.addEventListener('input', function() {
                if (this.value || minPriceInput.value) {
                    filterByPrice();
                }
            });
        });
    </script>
</body>
</html> 