<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $password = $_POST['password'];
    
    // Verify password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($password, $user['password'])) {
        try {
            // Start transaction
            $conn->beginTransaction();
            
            // Delete user's orders and order items
            $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)");
            $stmt->execute([$user_id]);
            
            $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete user's cart items
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete user's feedback
            $stmt = $conn->prepare("DELETE FROM feedback WHERE email = (SELECT email FROM users WHERE id = ?)");
            $stmt->execute([$user_id]);
            
            // Finally, delete the user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Commit transaction
            $conn->commit();
            
            // Destroy session and redirect
            session_destroy();
            header("Location: index.php?message=account_deleted");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            $error = "Failed to delete account. Please try again.";
        }
    } else {
        $error = "Incorrect password. Please try again.";
    }
}

// Get user information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .warning-card {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-2">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Account
                </h1>
                <p class="text-muted">This action cannot be undone</p>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Alert Messages -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Warning Card -->
                <div class="card warning-card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle text-danger fa-2x me-3 mt-1"></i>
                            <div>
                                <h5 class="card-title text-danger">Warning: Account Deletion</h5>
                                <p class="card-text">
                                    You are about to permanently delete your account. This action will:
                                </p>
                                <ul class="mb-0">
                                    <li>Delete all your personal information</li>
                                    <li>Remove all your order history</li>
                                    <li>Delete your shopping cart items</li>
                                    <li>Remove any feedback you've submitted</li>
                                    <li>This action cannot be undone</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Confirm Deletion
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="deleteForm">
                            <div class="mb-3">
                                <label for="password" class="form-label">Enter your password to confirm</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">This is required to confirm the deletion</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                                    <label class="form-check-label" for="confirmCheck">
                                        I understand that this action is permanent and cannot be undone
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="confirm_delete" class="btn btn-danger" id="deleteBtn" disabled>
                                    <i class="fas fa-trash me-2"></i>Delete Account
                                </button>
                                <a href="profile.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alternative Actions -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h6 class="card-title">Not sure about deleting?</h6>
                        <p class="card-text text-muted">Consider these alternatives instead:</p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="profile.php" class="btn btn-outline-primary">
                                <i class="fas fa-user me-2"></i>Update Profile
                            </a>
                            <a href="contact.php" class="btn btn-outline-info">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </a>
                            <a href="shop.php" class="btn btn-outline-success">
                                <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // Enable delete button only when checkbox is checked
        document.getElementById('confirmCheck').addEventListener('change', function() {
            const deleteBtn = document.getElementById('deleteBtn');
            const password = document.getElementById('password').value;
            
            deleteBtn.disabled = !this.checked || !password;
        });

        // Enable delete button when password is entered
        document.getElementById('password').addEventListener('input', function() {
            const deleteBtn = document.getElementById('deleteBtn');
            const confirmCheck = document.getElementById('confirmCheck').checked;
            
            deleteBtn.disabled = !confirmCheck || !this.value;
        });

        // Form submission confirmation
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            if (!confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html> 