<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=orders.php");
    exit();
}

$user_orders = getUserOrders($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Orders Section -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-shopping-bag me-2"></i>My Orders
                </h2>

                <?php if (empty($user_orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h3>No orders found</h3>
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="shop.php" class="btn btn-success">
                            <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($user_orders as $order): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Order #<?php echo $order['id']; ?></h6>
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Items:</strong> <?php echo $order['item_count']; ?> item(s)
                                        </div>
                                        <div class="mb-3">
                                            <strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                            <a href="track_order.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-truck me-2"></i>Track Order
                                            </a>
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

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>

<?php
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
?> 