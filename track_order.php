<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=track_order.php");
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

// Get all user orders for the dropdown
$user_orders = getUserOrders($conn, $user_id);

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

function getStatusDescription($status) {
    switch ($status) {
        case 'pending': return 'Your order is being processed and payment is pending.';
        case 'paid': return 'Payment has been received and your order is being prepared.';
        case 'shipped': return 'Your order has been shipped and is on its way to you.';
        case 'delivered': return 'Your order has been successfully delivered.';
        case 'cancelled': return 'Your order has been cancelled.';
        default: return 'Order status is being updated.';
    }
}

function getEstimatedDeliveryDate($order_date, $status) {
    $order_timestamp = strtotime($order_date);
    
    switch ($status) {
        case 'pending':
        case 'paid':
            return date('M d, Y', strtotime('+3-5 business days', $order_timestamp));
        case 'shipped':
            return date('M d, Y', strtotime('+1-2 business days', $order_timestamp));
        case 'delivered':
            return 'Delivered';
        case 'cancelled':
            return 'N/A';
        default:
            return date('M d, Y', strtotime('+3-5 business days', $order_timestamp));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 50px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 80px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 41px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #e9ecef;
        }
        .timeline-item.active::before {
            border-color: #28a745;
            background: #28a745;
        }
        .timeline-item.completed::before {
            border-color: #28a745;
            background: #28a745;
        }
        .timeline-content {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-card {
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }
        .tracking-number {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Order Tracking Section -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-truck me-2"></i>Track Your Order
                    </h2>
                    <a href="orders.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>

                <!-- Order Selector -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>Select Order to Track
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-8">
                                <select name="id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Choose an order to track...</option>
                                    <?php foreach ($user_orders as $user_order): ?>
                                        <option value="<?php echo $user_order['id']; ?>" 
                                                <?php echo ($order_id == $user_order['id']) ? 'selected' : ''; ?>>
                                            Order #<?php echo $user_order['id']; ?> - 
                                            <?php echo date('M d, Y', strtotime($user_order['created_at'])); ?> - 
                                            <?php echo formatPrice($user_order['total_amount']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Track Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($order): ?>
                    <!-- Order Details -->
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Order Status Timeline -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-route me-2"></i>Order Status Timeline
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <?php
                                        $statuses = ['pending', 'paid', 'shipped', 'delivered'];
                                        $current_status = $order['status'];
                                        $current_index = array_search($current_status, $statuses);
                                        
                                        foreach ($statuses as $index => $status):
                                            $is_completed = $index <= $current_index;
                                            $is_active = $index == $current_index;
                                            $class = $is_active ? 'active' : ($is_completed ? 'completed' : '');
                                        ?>
                                            <div class="timeline-item <?php echo $class; ?>">
                                                <div class="timeline-content">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="<?php echo getStatusIcon($status); ?> me-3 text-<?php echo getStatusColor($status); ?>"></i>
                                                        <h6 class="mb-0"><?php echo ucfirst($status); ?></h6>
                                                        <?php if ($is_completed): ?>
                                                            <i class="fas fa-check-circle text-success ms-auto"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="text-muted mb-0"><?php echo getStatusDescription($status); ?></p>
                                                    <?php if ($is_active): ?>
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Current Status
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-box me-2"></i>Order Items
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $total = 0; foreach ($order_items as $item): 
                                                    $subtotal = $item['price'] * $item['quantity']; 
                                                    $total += $subtotal; 
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo $item['image']; ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                                 style="width:50px; height:50px; object-fit:cover;" 
                                                                 class="rounded me-3">
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td><?php echo formatPrice($item['price']); ?></td>
                                                    <td><?php echo formatPrice($subtotal); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <th colspan="3" class="text-end">Total</th>
                                                    <th><?php echo formatPrice($total); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
                                    <div class="mb-3">
                                        <strong>Order Number:</strong>
                                        <div class="tracking-number">#<?php echo $order['id']; ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Order Date:</strong>
                                        <div><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong>
                                        <div>
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?> status-badge">
                                                <i class="<?php echo getStatusIcon($order['status']); ?> me-1"></i>
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Payment Method:</strong>
                                        <div><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Total Amount:</strong>
                                        <div class="h5 text-success mb-0"><?php echo formatPrice($order['total_amount']); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Estimated Delivery:</strong>
                                        <div><?php echo getEstimatedDeliveryDate($order['created_at'], $order['status']); ?></div>
                                    </div>
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
                                    <div class="mb-3">
                                        <strong>Shipping Address:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Phone:</strong>
                                        <div><?php echo htmlspecialchars($order['phone']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Support -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-headset me-2"></i>Need Help?
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">If you have any questions about your order, please contact our support team.</p>
                                    <div class="d-grid gap-2">
                                        <a href="contact.php" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope me-2"></i>Contact Support
                                        </a>
                                        <a href="tel:+250780000000" class="btn btn-outline-success">
                                            <i class="fas fa-phone me-2"></i>Call Us
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($order_id > 0): ?>
                    <!-- Order not found or doesn't belong to user -->
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h3>Order Not Found</h3>
                        <p class="text-muted">The order you're looking for doesn't exist or doesn't belong to your account.</p>
                        <a href="orders.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
                <?php else: ?>
                    <!-- No order selected -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h3>Select an Order to Track</h3>
                        <p class="text-muted">Choose an order from the dropdown above to view its tracking information.</p>
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