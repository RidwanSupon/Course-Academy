<?php
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch course
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id=? AND active=1");
$courseStmt->execute([$id]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);

if(!$course) {
    header("Location: index.php");
    exit;
}
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
    
    <!-- Left Column: Info -->
    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col space-y-4">
      <h2 class="text-2xl ilm-text-gold font-bold mb-4">Course Information</h2>
      <p><span class="font-semibold">Teacher:</span> <?= htmlspecialchars($course['teacher']) ?></p>
      <p><span class="font-semibold">Gender:</span> <?= htmlspecialchars($course['gender']) ?></p>
      <p><span class="font-semibold">Duration:</span> <?= htmlspecialchars($course['duration']) ?></p>
      <p><span class="font-semibold">Price:</span> <?= $course['price'] > 0 ? '৳ '.number_format($course['price'],2) : 'Free' ?></p>
    </div>

    <!-- Right Column: Full Description -->
    <div class="bg-white rounded-xl shadow-lg p-6">
      <h2 class="text-2xl ilm-text-gold font-bold mb-4">Course Description</h2>
      <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($course['long_desc'] ?? $course['short_desc'])) ?></p>
    </div>

  </div>

  <!-- Enrollment Form -->
  <div class="bg-white rounded-xl shadow-lg p-6 mb-12">
    <h2 class="text-2xl ilm-text-gold font-bold mb-6 text-center">Enroll Now</h2>
    <form action="enroll.php" method="POST" class="grid md:grid-cols-2 gap-4">
      <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

      <input type="text" name="name" placeholder="Full Name" required class="w-full border rounded-lg p-2">
      <input type="email" name="email" placeholder="Email" class="w-full border rounded-lg p-2">
      <input type="password" name="password" placeholder="Password (for login)" class="w-full border rounded-lg p-2">
      <input type="text" name="phone" placeholder="Phone" required class="w-full border rounded-lg p-2">
      <input type="text" name="location" placeholder="Location" class="w-full border rounded-lg p-2">

      <!-- Payment Method -->
      <select name="payment_method" id="payment_method" required class="w-full border rounded-lg p-2 md:col-span-2">
        <option value="Cash">Cash</option>
        <option value="bKash">bKash</option>
      </select>

      <!-- bKash Transaction ID (hidden by default) -->
      <input type="text" name="transaction_id" id="transaction_id" placeholder="bKash Transaction ID" class="w-full border rounded-lg p-2 md:col-span-2 hidden">

      <button type="submit" class="ilm-bg-gold text-ilm-blue w-full py-2 rounded-lg font-bold hover:opacity-90 transition md:col-span-2">
        Submit Enrollment
      </button>
    </form>
  </div>

</main>

<?php include 'includes/footer.php'; ?>

<script>
// Show/hide bKash transaction ID input
const paymentSelect = document.getElementById('payment_method');
const txnInput = document.getElementById('transaction_id');

paymentSelect.addEventListener('change', () => {
  if (paymentSelect.value === 'bKash') {
    txnInput.classList.remove('hidden');
    txnInput.setAttribute('required', 'required');
  } else {
    txnInput.classList.add('hidden');
    txnInput.removeAttribute('required');
  }
});
</script>
</body>
</html>
