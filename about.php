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
    <title>About Us - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>

        body, h1, h2, h3, h4, h5, h6, p, .card, .lead, .display-4, .btn, .navbar, .nav-link, .form-label, .form-control {
            font-family: 'Geist', 'Inter', 'Segoe UI', Arial, sans-serif !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>


    <!-- About Hero Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold">About Lions Design</h1>
                    <p class="lead">Your trusted partner for premium gifts, awards, and custom printing solutions.</p>
                </div>
                <div class="col-md-6">
                    <img src="lions.jpg" class="img-fluid rounded" alt="About Lions Design">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <h2>Our Story</h2>
                <p class="lead">Founded with a passion for excellence and creativity, Lions Design has been at the forefront of premium gift and award solutions for businesses and individuals alike.</p>
                
                <p>At Lions Design, we understand that every gift, award, or promotional item represents more than just a product – it represents recognition, appreciation, and connection. That's why we've dedicated ourselves to creating exceptional quality items that make lasting impressions.</p>
                
                <p>Our journey began with a simple mission: to provide the highest quality crystal awards, custom printing services, and premium gift items that help businesses and individuals celebrate achievements, strengthen relationships, and promote their brands effectively.</p>

                <h3 class="mt-5">Our Expertise</h3>
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-trophy fa-3x text-success mb-3"></i>
                                <h5>Crystal Awards</h5>
                                <p>Premium crystal awards and recognition items that celebrate achievements and milestones.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-print fa-3x text-success mb-3"></i>
                                <h5>Custom Printing</h5>
                                <p>High-quality banners, t-shirts, and promotional materials with custom designs.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-gift fa-3x text-success mb-3"></i>
                                <h5>Gift Items</h5>
                                <p>Exclusive gift sets, mugs, umbrellas, and accessories for every occasion.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-success mb-3"></i>
                                <h5>Corporate Solutions</h5>
                                <p>Comprehensive corporate gifting and promotional solutions for businesses.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="mt-5">Our Commitment</h3>
                <p>We are committed to:</p>
                <ul>
                    <li><strong>Quality:</strong> Every product meets our high standards for excellence</li>
                    <li><strong>Innovation:</strong> Continuously improving our designs and processes</li>
                    <li><strong>Customer Service:</strong> Providing exceptional support throughout your journey</li>
                    <li><strong>Sustainability:</strong> Using eco-friendly materials and practices where possible</li>
                    <li><strong>Reliability:</strong> Delivering on time, every time</li>
                </ul>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Quick Facts
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6><i class="fas fa-calendar-alt text-success me-2"></i>Established</h6>
                            <p class="mb-0">2024</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-map-marker-alt text-success me-2"></i>Location</h6>
                            <p class="mb-0">Sulfo road,KN 82,Ndamage house</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-phone text-success me-2"></i>Contact</h6>
                            <p class="mb-0">+250 786 551 353</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-envelope text-success me-2"></i>Email</h6>
                            <p class="mb-0">lionsdesign110@gmail.com</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-clock text-success me-2"></i>Business Hours</h6>
                            <p class="mb-0">Mon-Sat: 8AM-8PM</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Why Choose Us
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium Quality Products</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom Design Services</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Fast Turnaround Time</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Competitive Pricing</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Excellent Customer Service</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Secure Online Shopping</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-success text-white py-5">
        <div class="container text-center">
            <h3>Ready to Get Started?</h3>
            <p class="lead">Explore our products and discover the perfect solution for your needs.</p>
            <a href="shop.php" class="btn btn-light btn-lg me-3">
                <i class="fas fa-shopping-cart me-2"></i>Shop Now
            </a>
            <a href="contact.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-envelope me-2"></i>Contact Us
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html> 