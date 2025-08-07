<?php
session_start();
require_once 'config/database.php';

// Fetch all services
$stmt = $conn->prepare('SELECT * FROM services ORDER BY id DESC');
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Remove custom body font to use global style */
        .service-card { border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: box-shadow .2s; background: #fff; }
        .service-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
        .service-img { width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; }
        .service-title { font-weight: 700; font-size: 1.2rem; }
        .service-price { color: #009e3c; font-weight: 600; }
        .btn-request { background: #009e3c; color: #fff; border: none; font-weight: 600; }
        .btn-request:hover { background: #007a2c; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4 text-center" style="font-weight:700;">Custom Printing Services</h2>
    <div class="row g-4">
        <?php foreach ($services as $service): ?>
            <div class="col-md-4 col-lg-3">
                <div class="service-card h-100 d-flex flex-column">
                    <img src="<?= htmlspecialchars($service['image']) ?>" class="service-img" alt="<?= htmlspecialchars($service['title']) ?>">
                    <div class="p-3 flex-grow-1 d-flex flex-column">
                        <div class="service-title mb-2"><?= htmlspecialchars($service['title']) ?></div>
                        <div class="mb-2" style="font-size:0.95rem; color:#444;">
                            <?= nl2br(htmlspecialchars($service['description'])) ?>
                        </div>
                        <div class="service-price mb-3">Starting at <?= htmlspecialchars($service['price']) ?></div>
                        
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Request Service Modal (to be implemented in next step) -->
<div class="modal fade" id="requestServiceModal" tabindex="-1" aria-labelledby="requestServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="serviceRequestForm" method="post" enctype="multipart/form-data" action="ajax/request_service.php">
        <div class="modal-header">
          <h5 class="modal-title" id="requestServiceModalLabel">Request Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="service_id" id="modalServiceId">
          <div class="mb-3">
            <label for="modalServiceTitle" class="form-label">Service</label>
            <input type="text" class="form-control" id="modalServiceTitle" readonly>
          </div>
          <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" id="full_name" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" name="phone" id="phone" required>
          </div>
          <div class="mb-3">
            <label for="file" class="form-label">Upload Logo/Design (jpg, png, pdf, max 5MB)</label>
            <input type="file" class="form-control" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Additional Instructions</label>
            <textarea class="form-control" name="message" id="message" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-request">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
<script>
// Fill modal with service info
var requestServiceModal = document.getElementById('requestServiceModal');
requestServiceModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var serviceId = button.getAttribute('data-service-id');
  var serviceTitle = button.getAttribute('data-service-title');
  document.getElementById('modalServiceId').value = serviceId;
  document.getElementById('modalServiceTitle').value = serviceTitle;
});

// AJAX submit for service request
const form = document.getElementById('serviceRequestForm');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(form);
  
  // Show loading state
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = 'Submitting...';
  submitBtn.disabled = true;
  
  fetch('ajax/request_service.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.redirect) {
      window.location.href = data.redirect;
      return;
    }
    if (data.success) {
      form.reset();
      var modal = bootstrap.Modal.getInstance(requestServiceModal);
      modal.hide();
      alert('Service requested and added to cart!');
      if (typeof updateCartCount === 'function') updateCartCount();
    } else {
      alert(data.error || 'Request failed.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Request failed: ' + error.message);
  })
  .finally(() => {
    // Reset button state
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  });
});
</script>
</body>
</html>