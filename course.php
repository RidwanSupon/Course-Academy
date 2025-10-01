<?php
require_once __DIR__ . '/config.php';
if(session_status() === PHP_SESSION_NONE) session_start();

// Get course ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch course
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id=? AND active=1");
$courseStmt->execute([$id]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);

if(!$course) {
    header("Location: index.php");
    exit;
}

// Check login
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? '') : '';
$userEmail = $isLoggedIn ? ($_SESSION['user_email'] ?? '') : '';
$userPhone = $isLoggedIn ? ($_SESSION['user_phone'] ?? '') : '';
$userLocation = $isLoggedIn ? ($_SESSION['user_location'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($course['title']) ?> - ILM PATH NETWORK</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root { --ilm-blue:#0b1d3d; --ilm-gold:#f2a900; }
.ilm-bg-blue { background-color: var(--ilm-blue); }
.ilm-text-gold { color: var(--ilm-gold); }
.ilm-bg-gold { background-color: var(--ilm-gold); }
</style>
</head>
<body class="bg-gray-50 font-sans">

<?php include 'includes/header.php'; ?>

<main class="pt-24 px-4 md:px-6 max-w-6xl mx-auto">

  <!-- Course Header -->
  <div class="text-center mb-12">
    <h1 class="text-4xl md:text-5xl ilm-text-gold font-bold mb-2"><?= htmlspecialchars($course['title']) ?></h1>
    <p class="text-gray-600 text-lg md:text-xl"><?= htmlspecialchars($course['short_desc']) ?></p>
  </div>

  <!-- Course Details Grid -->
  <div class="grid md:grid-cols-2 gap-8 mb-12">
    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col space-y-4">
      <h2 class="text-2xl ilm-text-gold font-bold mb-4">Course Information</h2>
      <p><span class="font-semibold">Teacher:</span> <?= htmlspecialchars($course['teacher']) ?></p>
      <p><span class="font-semibold">Gender:</span> <?= htmlspecialchars($course['gender']) ?></p>
      <p><span class="font-semibold">Duration:</span> <?= htmlspecialchars($course['duration']) ?></p>
      <p><span class="font-semibold">Price:</span> <?= $course['price'] > 0 ? '৳ '.number_format($course['price'],2) : 'Free' ?></p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <h2 class="text-2xl ilm-text-gold font-bold mb-4">Course Description</h2>
      <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($course['long_desc'] ?? $course['short_desc'])) ?></p>
    </div>
  </div>

  <!-- Enrollment Form -->
  <div class="bg-white rounded-xl shadow-lg p-6 mb-12">
    <h2 class="text-2xl ilm-text-gold font-bold mb-6 text-center">Enroll Now</h2>

    <form id="enrollForm" action="enroll.php" method="POST" class="grid md:grid-cols-2 gap-4">
      <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

      <!-- Name -->
      <input type="text" name="name" placeholder="Full Name" required 
             value="<?= htmlspecialchars($userName) ?>" class="w-full border rounded-lg p-2">

      <!-- Email -->
      <input type="email" name="email" placeholder="Email" required 
             value="<?= htmlspecialchars($userEmail) ?>" class="w-full border rounded-lg p-2">

      <!-- Phone -->
      <input type="text" name="phone" placeholder="Phone" required 
             value="<?= htmlspecialchars($userPhone) ?>" class="w-full border rounded-lg p-2">

      <!-- Location -->
      <input type="text" name="location" placeholder="Location" 
             value="<?= htmlspecialchars($userLocation) ?>" class="w-full border rounded-lg p-2">

      <!-- Password (only for guests) -->
      <?php if(!$isLoggedIn): ?>
        <input type="password" name="password" placeholder="Password (for account)" class="w-full border rounded-lg p-2 md:col-span-2">
      <?php endif; ?>

      <!-- Payment Method -->
      <select name="payment_method" id="payment_method" required class="w-full border rounded-lg p-2 md:col-span-2">
        <option value="Cash">Cash</option>
        <option value="bKash">bKash</option>
      </select>

      <!-- bKash Transaction ID -->
      <input type="text" name="transaction_id" id="transaction_id" placeholder="bKash Transaction ID" 
             class="w-full border rounded-lg p-2 md:col-span-2 hidden">

      <button type="submit" class="ilm-bg-gold text-ilm-blue w-full py-2 rounded-lg font-bold hover:opacity-90 transition md:col-span-2">
        Enroll Now
      </button>
    </form>
  </div>

</main>

<?php include 'includes/footer.php'; ?>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 max-w-sm text-center shadow-xl">
    <h2 class="text-2xl font-bold text-green-600 mb-4">Enrollment Successful!</h2>
    <p class="text-gray-700 mb-4">You have been successfully enrolled in the course.</p>
    <button onclick="closeModal()" 
            class="ilm-bg-gold text-ilm-blue px-4 py-2 rounded-lg font-semibold hover:opacity-90">
      Close
    </button>
  </div>
</div>

<script>
// Show/hide bKash transaction field
const paymentSelect = document.getElementById('payment_method');
const txnInput = document.getElementById('transaction_id');
if(paymentSelect){
  paymentSelect.addEventListener('change', () => {
    if(paymentSelect.value === 'bKash'){
      txnInput.classList.remove('hidden');
      txnInput.setAttribute('required','required');
    } else {
      txnInput.classList.add('hidden');
      txnInput.removeAttribute('required');
    }
  });
}

// Show success modal if enrolled
const urlParams = new URLSearchParams(window.location.search);
if(urlParams.get('enrolled') === 'true'){
  const modal = document.getElementById('successModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeModal(){
  document.getElementById('successModal').classList.add('hidden');
  window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
</body>
</html>
