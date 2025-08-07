<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$session_id = generateSessionId();
$cart_items = getCart($conn, $session_id);
$cart_total = getCartTotal($conn, $session_id);

// Check if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

$user = getUserById($conn, $_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    $phone = sanitizeInput($_POST['phone']);
    $payment_method = sanitizeInput($_POST['payment_method']);
    
    if (empty($shipping_address) || empty($phone)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $conn->beginTransaction();
            
            // Create order
            $order_id = createOrder($conn, $_SESSION['user_id'], $cart_total, $shipping_address, $phone, $payment_method);
            
            // Add order items
            addOrderItems($conn, $order_id, $cart_items);
            
            // Process payment (placeholder for MTN Mobile Money)
            if ($payment_method === 'mtn_mobile_money') {
                $payment_result = processMTNPayment($cart_total, $phone);
                if ($payment_result['success']) {
                    // Update order status to paid
                    updateOrderStatus($conn, $order_id, 'paid');
                }
            }
            
            // Clear cart
            clearCart($conn, $session_id);
            
            $conn->commit();
            
            $success = 'Order placed successfully! Your order number is #' . $order_id;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error processing order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Checkout Section -->
    <div class="container py-5">
        <?php if ($success): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h3>Order Placed Successfully!</h3>
                            <p class="lead"><?php echo $success; ?></p>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-4">
                                <a href="orders.php" class="btn btn-success me-2">
                                    <i class="fas fa-list me-2"></i>View Orders
                                </a>
                                <a href="shop.php" class="btn btn-outline-dark">
                                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Checkout
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" id="checkoutForm">
                                <!-- Shipping Information -->
                                <h5 class="mb-3">
                                    <i class="fas fa-shipping-fast me-2"></i>Shipping Information
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" value="<?php echo $user['first_name']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" value="<?php echo $user['last_name']; ?>" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label">Shipping Address *</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo $user['address']; ?></textarea>
                                </div>

                                <!-- Payment Method -->
                                <h5 class="mb-3 mt-4">
                                    <i class="fas fa-credit-card me-2"></i>Payment Method
                                </h5>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="mtn_mobile_money" value="mtn_mobile_money" checked>
                                        <label class="form-check-label" for="mtn_mobile_money">
                                            <i class="fas fa-mobile-alt text-success me-2"></i>MTN Mobile Money
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university text-primary me-2"></i>Bank Transfer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery">
                                        <label class="form-check-label" for="cash_on_delivery">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>Cash on Delivery
                                        </label>
                                    </div>
                                </div>

                                <!-- MTN Mobile Money Instructions -->
                                <div id="mtnInstructions" class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>MTN Mobile Money Instructions</h6>
                                    <ol class="mb-0">
                                        <li>Ensure you have sufficient balance in your MTN Mobile Money account</li>
                                        <li>You will receive a payment prompt on your phone</li>
                                        <li>Enter your MTN Mobile Money PIN to complete the transaction</li>
                                        <li>Wait for confirmation SMS before closing this page</li>
                                    </ol>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-lock me-2"></i>Place Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card checkout-summary">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                                <?php if ($item['product_id'] < 0): // Service Request ?>
                                    <?php $service = getServiceRequestByCartProductId($conn, $item['product_id']); ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><span class="badge bg-success">Service</span> <?php echo htmlspecialchars($service['title']); ?> (x1)</span>
                                        <span><?php echo htmlspecialchars($service['price']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo $item['title']; ?> (x<?php echo $item['quantity']; ?>)</span>
                                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo formatPrice($cart_total); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-success"><?php echo formatPrice($cart_total); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><i class="fas fa-shield-alt text-success me-2"></i>Secure Checkout</h6>
                            <p class="small text-muted mb-0">
                                Your payment information is encrypted and secure. We never store your payment details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // Show/hide MTN instructions based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const mtnInstructions = document.getElementById('mtnInstructions');
                if (this.value === 'mtn_mobile_money') {
                    mtnInstructions.style.display = 'block';
                } else {
                    mtnInstructions.style.display = 'none';
                }
            });
        });

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('shipping_address').value.trim();
            
            if (!phone || !address) {
                e.preventDefault();
                showAlert('Please fill in all required fields', 'danger');
                return false;
            }
            
            // Phone number validation
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                showAlert('Please enter a valid phone number', 'danger');
                return false;
            }
        });

        // Confirm order placement
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'mtn_mobile_money') {
                if (!confirm('You will be redirected to MTN Mobile Money. Continue?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</body>
</html> 