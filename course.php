<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Get course ID
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id=? AND active=1");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if course not found
if (!$course) {
    header("Location: index.php");
    exit;
}

// User session data
$isLoggedIn    = !empty($_SESSION['user_id']);
$userID        = $isLoggedIn ? $_SESSION['user_id'] : 0;
$userName      = $isLoggedIn ? ($_SESSION['user_name'] ?? '')     : '';
$userEmail     = $isLoggedIn ? ($_SESSION['user_email'] ?? '')    : '';
$userPhone     = $isLoggedIn ? ($_SESSION['user_phone'] ?? '')    : '';
$userLocation  = $isLoggedIn ? ($_SESSION['user_location'] ?? '') : '';

// Check if user already enrolled
$alreadyEnrolled = false;
if ($isLoggedIn) {
    $check = $pdo->prepare("SELECT id FROM enrollments WHERE user_id=? AND course_id=?");
    $check->execute([$userID, $course_id]);
    $alreadyEnrolled = $check->fetch() ? true : false;
}

// Check for existing Free Class Request (optional, but good for UX)
$alreadyRequested = false;
if ($isLoggedIn) {
    $check_request = $pdo->prepare("
        SELECT id FROM free_class_requests 
        WHERE user_id=? AND course_id=? AND status IN ('New', 'Contacted', 'Scheduled')
        LIMIT 1
    ");
    $check_request->execute([$userID, $course_id]);
    $alreadyRequested = $check_request->fetch() ? true : false;
}


// Tailwind input style
$inputClasses = "w-full border-2 border-gray-600 bg-ilm-blue/50 text-white
focus:border-ilm-gold focus:ring-4 focus:ring-ilm-gold/25 rounded-xl p-4
transition duration-300 placeholder-gray-400 shadow-inner";

// Tailwind modal input style (slightly different for white modal background)
$modalInputClasses = "w-full border-2 border-gray-300 bg-gray-50 text-gray-800
focus:border-ilm-gold focus:ring-4 focus:ring-ilm-gold/25 rounded-xl p-3
transition duration-300 placeholder-gray-500 shadow-inner";

// Handle URL messages
$successMessage = '';
$errorMessage = '';
if (isset($_GET['free_requested']) && $_GET['free_requested'] === 'true') {
    $successMessage = '🎉 Your Free Class Request has been submitted! We will contact you soon.';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'already_requested':
            $errorMessage = '⚠️ You already have an active Free Class request for this course.';
            break;
        case 'missing_fields':
            $errorMessage = '❌ Please fill in all required fields for the Free Class Request.';
            break;
        case 'request_failed':
            $errorMessage = 'An error occurred while submitting your request. Please try again.';
            break;
        // The rest of the error cases from enroll.php would go here
        case 'already_enrolled':
            $errorMessage = '⚠️ You are already enrolled in this course.';
            break;
        case 'enrollment_failed':
        case 'transaction_error':
            $errorMessage = '❌ Enrollment failed. Please check your transaction details or try again.';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($course['title']) ?> - ILM PATH NETWORK</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          'ilm-blue': '#0A1C3C',
          'ilm-gold': '#D4AF37',
          'ilm-light-gold': '#fde68a'
        },
        animation: {
          'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
          'fade-in-scale': 'fadeInScale 0.5s ease-out forwards'
        },
        keyframes: {
          fadeInUp: {
            '0%': {opacity:0,transform:'translateY(35px)'},
            '100%': {opacity:1,transform:'translateY(0)'}
          },
          fadeInScale: {
            '0%': {opacity:0,transform:'scale(0.95)'},
            '100%': {opacity:1,transform:'scale(1)'}
          }
        }
      }
    }
  }
  </script>
</head>

<body class="bg-gray-50 font-sans">
<?php include 'includes/header.php'; ?>

<main class="pt-32 px-4 md:px-6 max-w-7xl mx-auto">

  <?php if (!empty($successMessage)): ?>
    <div class="mb-8 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg font-semibold animate-fade-in-up" role="alert">
      <?= $successMessage ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($errorMessage)): ?>
    <div class="mb-8 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg font-semibold animate-fade-in-up" role="alert">
      <?= $errorMessage ?>
    </div>
  <?php endif; ?>
  <div class="text-center mb-16 p-4 animate-fade-in-up">
    <span class="text-sm font-extrabold text-white bg-ilm-blue py-1 px-4 rounded-full mb-4 inline-block uppercase tracking-widest shadow-lg">
      PREMIUM MASTERCLASS
    </span>
    <h1 class="text-2xl md:text-4xl text-ilm-gold font-black mb-8 drop-shadow-lg leading-none animate-fade-in-scale">
      <?= htmlspecialchars($course['title']) ?>
    </h1>
    <p class="text-gray-600 text-xl md:text-2xl max-w-4xl mx-auto italic font-medium border-l-4 border-ilm-gold pl-5 leading-relaxed animate-fade-in-up">
      <?= htmlspecialchars($course['short_desc']) ?>
    </p>
  </div>

  <div class="grid lg:grid-cols-3 gap-12 mb-12">
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl p-10 border border-gray-100 order-2 lg:order-1">
      <h2 class="text-4xl font-extrabold text-ilm-blue mb-6 pb-3 border-b-4 border-ilm-gold inline-block">
        Course Overview
      </h2>
      <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-line">
        <?= nl2br(htmlspecialchars($course['long_desc'] ?? $course['short_desc'])) ?>
      </p>
    </div>

    <div class="lg:col-span-1 bg-gradient-to-br from-ilm-blue to-indigo-900 rounded-3xl shadow-2xl shadow-ilm-gold/50 border-t-8 border-ilm-gold p-8 h-fit order-1 lg:order-2">
      <h2 class="text-2xl font-extrabold text-white mb-6 pb-4 border-b border-white/20">
        Course Insights
      </h2>

      <div class="space-y-6 text-white text-lg">
        <div class="flex justify-between bg-white/10 p-3 rounded-xl">
          <span>Mentor</span>
          <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['teacher']) ?></span>
        </div>
        <div class="flex justify-between bg-white/10 p-3 rounded-xl">
          <span>Course For</span>
          <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['gender']) ?></span>
        </div>
        <div class="flex justify-between bg-white/10 p-3 rounded-xl">
          <span>Duration</span>
          <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['duration']) ?></span>
        </div>
        <div class="flex justify-between bg-white/20 p-4 rounded-xl">
          <span>Investment</span>
          <span class="text-3xl font-black text-ilm-gold">
            <?= $course['price']>0 ? '৳ '.number_format($course['price'],0) : 'Free' ?>
          </span>
        </div>
      </div>
    </div>
  </div>

<!--free class button and enrollment form-->
  
  <div class="text-center mb-20">
    <button onclick="openFreeClassModal()" class="bg-white border-4 border-ilm-blue text-ilm-blue px-10 py-3 rounded-full font-extrabold text-lg shadow-xl hover:bg-ilm-blue hover:text-white transition-all duration-300 <?= $alreadyEnrolled ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= $alreadyEnrolled ? 'disabled' : '' ?>>
        <?= $alreadyRequested ? 'Request Already Submitted' : 'Request Free Class Demo' ?>
    </button>
    <?php if ($alreadyEnrolled): ?>
        <p class="text-sm text-gray-500 mt-2 italic">You are already enrolled; a free class is not applicable.</p>
    <?php elseif ($alreadyRequested): ?>
        <p class="text-sm text-gray-500 mt-2 italic">Your request is being processed. We will contact you soon!</p>
    <?php endif; ?>
  </div>

  <!-- Enrollment Form -->
  <div class="bg-ilm-blue rounded-3xl shadow-2xl p-12 mb-10 border-t-8 border-ilm-gold animate-fade-in-up">
    <h2 class="text-4xl font-extrabold text-white mb-10 text-center border-b border-white/20 pb-4">
      Finalize Your Registration
    </h2>

    <?php if ($alreadyEnrolled): ?>
      <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg text-center font-semibold">
        ⚠️ You are already enrolled in this course.
      </div>
    <?php else: ?>
      <form id="enrollForm" action="enroll.php" method="POST" class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
        <input type="text" name="name" placeholder="Full Name" required value="<?= htmlspecialchars($userName) ?>" class="<?= $inputClasses ?>">
        <input type="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($userEmail) ?>" class="<?= $inputClasses ?>">
        <input type="text" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($userPhone) ?>" class="<?= $inputClasses ?>">
        <input type="text" name="location" placeholder="Location (City/Area)" value="<?= htmlspecialchars($userLocation) ?>" class="<?= $inputClasses ?>">

        <?php if (!$isLoggedIn): ?>
          <input type="password" name="password" placeholder="Password (required for new account)" class="<?= $inputClasses ?> md:col-span-2" required>
        <?php endif; ?>

        <select name="payment_method" id="payment_method" required class="<?= $inputClasses ?> text-gray-300 md:col-span-2">
          <option value="" disabled selected>Select Payment Method</option>
          <option value="Cash" class="text-gray-800">Cash Payment</option>
          <option value="bKash" class="text-gray-800">bKash (Mobile Banking)</option>
        </select>

        <input type="text" name="transaction_id" id="transaction_id" placeholder="Enter bKash Transaction ID (Txn ID)" class="<?= $inputClasses ?> md:col-span-2 hidden">

        <button type="submit" class="bg-gradient-to-r from-ilm-gold to-yellow-400 text-ilm-blue w-full py-4 rounded-xl font-black text-xl shadow-2xl md:col-span-2 mt-8 hover:scale-105 transition-all">
          Enroll Now
        </button>
      </form>
    <?php endif; ?>
  </div>
  
  </main>

<?php include 'includes/footer.php'; ?>

<div id="successModal" class="fixed inset-0 bg-black bg-opacity-80 hidden justify-center items-center z-50 p-4">
  <div class="bg-white rounded-3xl p-10 max-w-sm w-full text-center shadow-2xl border-t-8 border-green-500">
    <div class="text-7xl text-green-500 mb-5">🎉</div>
    <h2 class="text-3xl font-black text-ilm-blue mb-4">Enrollment Confirmed!</h2>
    <p class="text-gray-700 mb-8">
      Your registration for <strong><?= htmlspecialchars($course['title']) ?></strong> is complete. Check your email for next steps.
    </p>
    <button onclick="closeModal('successModal')" class="bg-ilm-blue text-white px-10 py-3 rounded-full font-bold text-lg hover:bg-[#08152e] transition">
      Acknowledge
    </button>
  </div>
</div>

<div id="freeClassModal" class="fixed inset-0 bg-black bg-opacity-80 hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-3xl p-8 max-w-xl w-full shadow-2xl border-t-8 border-ilm-blue">
        <h2 class="text-3xl font-black text-ilm-blue mb-6 text-center">
            Request a Free Class for <?= htmlspecialchars($course['title']) ?>
        </h2>
        <form id="freeClassForm" action="free_class.php" method="POST" class="grid grid-cols-2 gap-4">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <input type="hidden" name="user_id" value="<?= $userID ?>"> <input type="text" name="name" placeholder="Full Name" required value="<?= htmlspecialchars($userName) ?>" class="<?= $modalInputClasses ?> col-span-2">
            <input type="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($userEmail) ?>" class="<?= $modalInputClasses ?> col-span-2">
            <input type="text" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($userPhone) ?>" class="<?= $modalInputClasses ?> col-span-2">

            <label class="col-span-1">
                <span class="text-gray-700 text-sm font-semibold block mb-1">Preferred Date:</span>
                <input type="date" name="preferred_date" class="<?= $modalInputClasses ?>" required>
            </label>
            
            <label class="col-span-1">
                <span class="text-gray-700 text-sm font-semibold block mb-1">Preferred Time:</span>
                <input type="time" name="preferred_time" class="<?= $modalInputClasses ?>" required>
            </label>

            <textarea name="message" placeholder="Optional: Any specific questions or requests?" rows="3" class="<?= $modalInputClasses ?> col-span-2"></textarea>

            <div class="col-span-2 flex justify-end gap-4 mt-4">
                <button type="button" onclick="closeModal('freeClassModal')" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" class="bg-ilm-blue text-white px-8 py-3 rounded-xl font-black hover:bg-[#08152e] transition shadow-lg">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
<script>
const paymentSelect = document.getElementById('payment_method');
const txnInput = document.getElementById('transaction_id');

// Functionality for payment method selection
paymentSelect?.addEventListener('change', () => {
  if (paymentSelect.value === 'bKash') {
    txnInput.classList.remove('hidden');
    txnInput.setAttribute('required', 'required');
    txnInput.value = ''; // Clear for a new transaction
  } else {
    txnInput.classList.add('hidden');
    txnInput.removeAttribute('required');
    txnInput.value = '';
  }
});

// Functionality for Enrollment Success Modal
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('enrolled') === 'true') {
  const modal = document.getElementById('successModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

// Function to close any modal and clean the URL
function closeModal(modalId) {
  document.getElementById(modalId).classList.add('hidden');
  document.getElementById(modalId).classList.remove('flex');
  // Clean URL parameters for success/error messages, but keep 'id'
  const newParams = new URLSearchParams();
  if (urlParams.has('id')) {
    newParams.set('id', urlParams.get('id'));
  }
  window.history.replaceState({}, document.title, `${window.location.pathname}?${newParams.toString()}`);
}

// Function to open the Free Class Modal
function openFreeClassModal() {
  // Prevent opening if the user is already enrolled (button should be disabled but good to double-check)
  <?php if ($alreadyEnrolled): ?>
      return; 
  <?php endif; ?>

  const modal = document.getElementById('freeClassModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

// Clear enrollment message URL params on load if no success/error is active, 
// to prevent them from showing on refresh if not handled by a banner.
if (urlParams.has('enrolled') || urlParams.has('free_requested') || urlParams.has('error')) {
  // If a banner message is showing, don't clear the URL until the user acknowledges or navigates.
  // The closeModal function will handle cleaning the URL.
} else {
    // If no message to display, ensure only 'id' remains in the URL for clean look.
    const cleanParams = new URLSearchParams();
    if (urlParams.has('id')) {
        cleanParams.set('id', urlParams.get('id'));
    }
    window.history.replaceState({}, document.title, `${window.location.pathname}?${cleanParams.toString()}`);
}

</script>
</body>
</html>