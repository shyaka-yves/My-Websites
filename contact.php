<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Save feedback to database
        if (saveFeedback($conn, $name, $email, $subject, $message)) {
            $success = 'Thank you for contacting us! We have received your message and will get back to you soon.';
        } else {
            $error = 'Sorry, there was an error sending your message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="bg-light py-5 mb-4">
        <div class="container text-center">
            <h1 class="display-5 fw-bold">Contact Us</h1>
            <p class="lead">We'd love to hear from you! Reach out with your questions, feedback, or project ideas.</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="mb-4"><i class="fas fa-envelope me-2 text-success"></i>Send a Message</h3>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Your contact information has been auto-filled. You can modify it if needed.
                            </div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="" id="contactForm" novalidate>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="name" class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required 
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? (isset($_SESSION['user_id']) ? $_SESSION['user_name'] : '')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? (isset($_SESSION['user_id']) ? $_SESSION['user_email'] : '')); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Contact Info & Map -->
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3"><i class="fas fa-map-marker-alt text-success me-2"></i>Sulfo road,KN 82,Ndamage house</li>
                            <li class="mb-3"><i class="fas fa-phone text-success me-2"></i>+250 786 551 353</li>
                            <li class="mb-3"><i class="fas fa-envelope text-success me-2"></i>lionsdesign110@gmail.com</li>
                            <li class="mb-3"><i class="fab fa-instagram text-success me-2"></i><a href="https://instagram.com/lions_design_ltd" target="_blank" class="text-decoration-none text-dark">@lions_design_ltd</a></li>
                            <li><i class="fas fa-clock text-success me-2"></i>Mon-Sat: 8AM-8PM</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-map me-2"></i>Our Location</h6>
                    </div>
                    <div class="card-body p-0">
                        <!-- Google Maps Embed Placeholder -->
                        
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3994.439693210134!2d30.053804455430473!3d-1.9432220747128184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca50801ee184f%3A0x1d2b26aae9d05cb8!2sLions%20Design%20Ltd!5e0!3m2!1sen!2srw!4v1753691872876!5m2!1sen!2srw" width="510" height="310" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
    // Simple client-side validation
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        let valid = true;
        ['name','email','subject','message'].forEach(function(id) {
            const field = document.getElementById(id);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html> 