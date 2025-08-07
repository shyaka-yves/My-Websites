<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lions Design - Premium Products & Gifts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Success Messages -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'account_deleted'): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>Your account has been successfully deleted. Thank you for using our services.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Section with Carousel -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="Magic mug.jpg" class="d-block w-100" alt="Premium Gifts">
                <div class="carousel-caption">
                    <h1>Premium Gifts & Awards</h1>
                    <p>Discover our exclusive collection of crystal awards and premium gifts</p>
                    <a href="shop.php" class="btn btn-success btn-lg">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="Temperature Bottles.jpg" class="d-block w-100" alt="Custom Printing">
                <div class="carousel-caption">
                    <h1>Custom Printing Solutions</h1>
                    <p>Professional banners, t-shirts, and promotional materials</p>
                    <a href="shop.php?category=printing" class="btn btn-success btn-lg">View Printing</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="wooden.jpg" class="d-block w-100" alt="Gift Sets">
                <div class="carousel-caption">
                    <h1>Exclusive Gift Sets</h1>
                    <p>Perfect gifts for every occasion - mugs, umbrellas, and more</p>
                    <a href="shop.php?category=gifts" class="btn btn-success btn-lg">Explore Gifts</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Product Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Shop by Category</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="category-card text-center">
                        <i class="fas fa-trophy fa-3x mb-3 text-success"></i>
                        <h4>Crystal Awards</h4>
                        <p>Premium crystal awards and recognition items</p>
                        <a href="shop.php?category=Crystal awards" class="btn btn-outline-dark">View Awards</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="category-card text-center">
                        <i class="fas fa-print fa-3x mb-3 text-success"></i>
                        <h4>Printing Services</h4>
                        <p>Banners, t-shirts, and promotional materials</p>
                        <a href="shop.php?category=printing" class="btn btn-outline-dark">View Printing</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="category-card text-center">
                        <i class="fas fa-gift fa-3x mb-3 text-success"></i>
                        <h4>Gift Items</h4>
                        <p>Mugs, umbrellas, key holders, and gift sets</p>
                        <a href="shop.php?category=gifts" class="btn btn-outline-dark">View Gifts</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Products</h2>
            <div class="row">
                <?php
                $featured_products = getFeaturedProducts($conn, 6);
                foreach ($featured_products as $product) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card product-card h-100">';
                    echo '<img src="' . $product['image'] . '" class="card-img-top" alt="' . $product['title'] . '">';
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<h5 class="card-title">' . $product['title'] . '</h5>';
                    echo '<p class="card-text">' . substr($product['description'], 0, 100) . '...</p>';
                    echo '<div class="mt-auto">';
                    echo '<p class="price">' . formatPrice($product['price']) . '</p>';
                    echo '<button class="btn btn-success add-to-cart" data-product-id="' . $product['id'] . '">Add to Cart</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="shop.php" class="btn btn-success btn-lg">View All Products</a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>About Lions Design</h2>
                    <p class="lead">We are a premier provider of premium gifts, awards, and custom printing solutions.</p>
                    <p>At Lions Design, we specialize in creating high-quality crystal awards, custom printed materials, and exclusive gift sets. Our commitment to excellence and attention to detail has made us the trusted choice for businesses and individuals seeking premium products.</p>
                    <p>From corporate awards to promotional materials, we deliver exceptional quality and outstanding customer service. Our team of skilled artisans and printing experts ensures that every product meets our high standards.</p>
                    <a href="about.php" class="btn btn-outline-dark">Learn More</a>
                </div>
                <div class="col-md-6">
                    <img src="lions.jpg" class="img-fluid rounded" alt="About Lions Design">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html> 