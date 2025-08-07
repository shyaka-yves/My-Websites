<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=order_details.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get order details
$order = null;
$order_items = [];

if ($order_id > 0) {
    $order = getOrderDetails($conn, $order_id);
    // Verify the order belongs to the current user
    if ($order && $order['user_id'] == $user_id) {
        $order_items = getOrderItems($conn, $order_id);
    } else {
        $order = null;
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'paid': return 'success';
        case 'shipped': return 'info';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusIcon($status) {
    switch ($status) {
        case 'pending': return 'fas fa-clock';
        case 'paid': return 'fas fa-credit-card';
        case 'shipped': return 'fas fa-shipping-fast';
        case 'delivered': return 'fas fa-check-circle';
        case 'cancelled': return 'fas fa-times-circle';
        default: return 'fas fa-question-circle';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .order-header {
            background: #ffffff;
            color: #333;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e9ecef;
        }
        .status-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .status-card:hover {
            transform: translateY(-2px);
        }
        .product-card {
            transition: transform 0.2s;
            border: 1px solid #e9ecef;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .invoice-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <?php if ($order): ?>
        <!-- Order Header -->
        <div class="order-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">
                            <i class="fas fa-receipt me-3"></i>Order #<?php echo $order['id']; ?>
                        </h1>
                        <p class="mb-0 opacity-75">
                            Placed on <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?> fs-6 px-3 py-2">
                            <i class="<?php echo getStatusIcon($order['status']); ?> me-2"></i>
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Section -->
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>Order Items
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($order_items as $item): ?>
                                <?php if ($item['product_id'] < 0): // Service Request ?>
                                    <?php $service = getServiceRequestByCartProductId($conn, $item['product_id']); ?>
                                    <div class="product-card p-3 mb-3 rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <span class="badge bg-success">Service</span>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($service['title']); ?></h6>
                                                <p class="text-muted mb-0">From: <?php echo htmlspecialchars($service['full_name']); ?></p>
                                                <p class="mb-0"><small>Phone: <?php echo htmlspecialchars($service['phone']); ?></small></p>
                                                <?php if ($service['file']): ?>
                                                    <a href="<?php echo $service['file']; ?>" target="_blank" class="btn btn-sm btn-outline-dark mt-1">View File</a>
                                                <?php endif; ?>
                                                <?php if ($service['message']): ?>
                                                    <div class="mt-1"><small><b>Instructions:</b> <?php echo htmlspecialchars($service['message']); ?></small></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <small class="text-muted">Price</small><br>
                                                <strong><?php echo htmlspecialchars($service['price']); ?></strong>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <small class="text-muted">Subtotal</small><br>
                                                <strong><?php echo htmlspecialchars($service['price']); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="product-card p-3 mb-3 rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <img src="<?php echo $item['image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                     class="img-fluid rounded" 
                                                     style="max-width: 80px;">
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <small class="text-muted">Price</small><br>
                                                <strong><?php echo formatPrice($item['price']); ?></strong>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <small class="text-muted">Subtotal</small><br>
                                                <strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-shipping-fast me-2"></i>Shipping Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Shipping Address</h6>
                                    <div class="p-3 bg-light rounded">
                                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Contact Information</h6>
                                    <div class="p-3 bg-light rounded">
                                        <p class="mb-2">
                                            <i class="fas fa-phone me-2"></i>
                                            <?php echo htmlspecialchars($order['phone']); ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?php echo htmlspecialchars($order['email']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="invoice-section">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span><?php echo formatPrice($order['total_amount']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>Free</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>Included</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong class="text-success"><?php echo formatPrice($order['total_amount']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-credit-card text-primary me-3"></i>
                                <div>
                                    <strong>Payment Method</strong><br>
                                    <small class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <div>
                                    <strong>Payment Status</strong><br>
                                    <small class="text-muted">
                                        <?php echo ($order['status'] == 'pending') ? 'Pending' : 'Paid'; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Order Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="track_order.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-truck me-2"></i>Track Order
                                </a>
                                <button onclick="window.print();" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-2"></i>Print Invoice
                                </button>
                                <a href="contact.php" class="btn btn-outline-info">
                                    <i class="fas fa-headset me-2"></i>Contact Support
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-route me-2"></i>Order Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="status-card p-3 mb-3 border-left-<?php echo getStatusColor($order['status']); ?>">
                                <div class="d-flex align-items-center">
                                    <i class="<?php echo getStatusIcon($order['status']); ?> text-<?php echo getStatusColor($order['status']); ?> me-3"></i>
                                    <div>
                                        <strong><?php echo ucfirst($order['status']); ?></strong><br>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y H:i', strtotime($order['updated_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <a href="track_order.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-2"></i>View Full Timeline
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Order not found -->
        <div class="container py-5">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h3>Order Not Found</h3>
                <p class="text-muted">The order you're looking for doesn't exist or doesn't belong to your account.</p>
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html> 