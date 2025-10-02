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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://unpkg.com/heroicons@2.1.1/dist/umd/heroicons.min.js"></script>
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
            '0%': { opacity: '0', transform: 'translateY(35px)' },
            '100%': { opacity: '1', transform: 'translateY(0)' }
          },
          fadeInScale: {
            '0%': { opacity: '0', transform: 'scale(0.95)' },
            '100%': { opacity: '1', transform: 'scale(1)' }
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

    <!-- Hero Section -->
    <div class="text-center mb-16 p-4 animate-fade-in-up">
        <span class="text-sm font-extrabold text-white bg-ilm-blue py-1 px-4 rounded-full mb-4 inline-block uppercase tracking-widest shadow-lg">PREMIUM MASTERCLASS</span>
        <h1 class="text-2xl md:text-4xl text-ilm-gold font-black mb-8 drop-shadow-lg leading-none animate-fade-in-scale"><?= htmlspecialchars($course['title']) ?></h1>
        <p class="text-gray-600 text-xl md:text-2xl max-w-4xl mx-auto italic font-medium border-l-4 border-ilm-gold pl-5 leading-relaxed animate-fade-in-up"><?= htmlspecialchars($course['short_desc']) ?></p>
    </div>

<!-- Course Overview & Insights -->
<div class="grid lg:grid-cols-3 gap-12 mb-12">

    <!-- Course Overview Card -->
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl p-10 border border-gray-100 order-2 lg:order-1 transform transition duration-500 hover:-translate-y-3 hover:scale-[1.02]">
        <h2 class="text-4xl font-extrabold text-ilm-blue mb-6 pb-3 border-b-4 border-ilm-gold inline-block">Course Overview</h2>
        <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-line">
            <?= nl2br(htmlspecialchars($course['long_desc'] ?? $course['short_desc'])) ?>
        </p>
    </div>

    <!-- Course Insights Card -->
    <div class="lg:col-span-1 bg-gradient-to-br from-ilm-blue to-indigo-900 rounded-3xl shadow-2xl shadow-ilm-gold/50 border-t-8 border-ilm-gold p-8 h-fit order-1 lg:order-2 transform transition duration-500 hover:-translate-y-3 hover:scale-[1.02]">
        <h2 class="text-2xl font-extrabold text-white mb-6 pb-4 border-b border-white/20 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 mr-2 text-ilm-gold">
                <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 5.25a.75.75 0 0 1 .75-.75H9a.75.75 0 0 1 0 1.5H8.25a.75.75 0 0 1-.75-.75ZM4.846 10.334q.978-.85 2.112-1.258a21.353 21.353 0 0 1 10.155 0q1.134.408 2.112 1.258C18.91 10.662 19.5 11.5 19.5 12.5a7.5 7.5 0 0 1-15 0c0-1.01.589-1.848 1.346-2.166ZM19.5 12.5c0 .35-.024.69-.068 1.018a3.75 3.75 0 1 0 0-2.036c.044.328.068.667.068 1.018ZM3 12.5a7.5 7.5 0 0 1 1.706-4.706 8.75 8.75 0 0 0-1.5.07.75.75 0 0 0 .148 1.492 7.25 7.25 0 0 0 0 8.888A7.5 7.5 0 0 1 3 12.5Zm17.794-4.706A7.5 7.5 0 0 1 21 12.5a7.5 7.5 0 0 1-1.706 4.706 8.75 8.75 0 0 0 1.5-.07.75.75 0 0 0-.148-1.492 7.25 7.25 0 0 0 0-8.888Z" clip-rule="evenodd" />
            </svg>
            Course Insights
        </h2>

        <div class="space-y-6 text-white text-lg">
            <!-- Mentor -->
            <div class="flex items-center justify-between bg-white/10 p-3 rounded-xl shadow-md animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 flex items-center justify-center bg-ilm-gold/20 rounded-full">
                        <heroicon:user-circle-solid class="w-5 h-5 text-ilm-gold"/>
                    </div>
                    <span class="font-semibold">Mentor</span>
                </div>
                <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['teacher']) ?></span>
            </div>

            <!-- For -->
            <div class="flex items-center justify-between bg-white/10 p-3 rounded-xl shadow-md animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 flex items-center justify-center bg-ilm-gold/20 rounded-full">
                        <heroicon:users-solid class="w-5 h-5 text-ilm-gold"/>
                    </div>
                    <span class="font-semibold"> Course For</span>
                </div>
                <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['gender']) ?></span>
            </div>

            <!-- Duration -->
            <div class="flex items-center justify-between bg-white/10 p-3 rounded-xl shadow-md animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 flex items-center justify-center bg-ilm-gold/20 rounded-full">
                        <heroicon:clock-solid class="w-5 h-5 text-ilm-gold"/>
                    </div>
                    <span class="font-semibold">Duration</span>
                </div>
                <span class="font-bold text-ilm-gold"><?= htmlspecialchars($course['duration']) ?></span>
            </div>

            <!-- Investment -->
            <div class="flex items-center justify-between bg-white/20 p-4 rounded-2xl shadow-lg animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 flex items-center justify-center bg-ilm-gold/30 rounded-full">
                        <heroicon:currency-dollar-solid class="w-6 h-6 text-ilm-blue"/>
                    </div>
                    <span class="font-semibold">Investment</span>
                </div>
                <span class="text-3xl md:text-4xl font-black text-ilm-gold drop-shadow-md">
                    <?= $course['price'] > 0 ? '৳ '.number_format($course['price'],0) : 'Free' ?>
                </span>
            </div>
        </div>
    </div>
</div>


    <!-- Registration Form -->
    <div class="bg-ilm-blue rounded-3xl shadow-2xl shadow-ilm-blue/50 p-12 mb-20 border-t-8 border-ilm-gold animate-fade-in-up">
        <h2 class="text-4xl font-extrabold text-white mb-10 text-center border-b border-white/20 pb-4">Finalize Your Registration</h2>
        <form id="enrollForm" action="enroll.php" method="POST" class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <?php $input_classes = "w-full border-2 border-gray-600 bg-ilm-blue/50 text-white focus:border-ilm-gold focus:ring-4 focus:ring-ilm-gold/25 rounded-xl p-4 transition duration-300 placeholder-gray-400 shadow-inner"; ?>

            <input type="text" name="name" placeholder="Full Name" required value="<?= htmlspecialchars($userName) ?>" class="<?= $input_classes ?>">
            <input type="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($userEmail) ?>" class="<?= $input_classes ?>">
            <input type="text" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($userPhone) ?>" class="<?= $input_classes ?>">
            <input type="text" name="location" placeholder="Location (City/Area)" value="<?= htmlspecialchars($userLocation) ?>" class="<?= $input_classes ?>">
            <?php if(!$isLoggedIn): ?>
                <input type="password" name="password" placeholder="Password (required for new account)" class="<?= $input_classes ?> md:col-span-2" required>
            <?php endif; ?>

            <select name="payment_method" id="payment_method" required class="<?= $input_classes ?> text-gray-300 md:col-span-2">
                <option value="" disabled selected>Select Payment Method</option>
                <option value="Cash" class="text-gray-800">Cash Payment</option>
                <option value="bKash" class="text-gray-800">bKash (Mobile Banking)</option>
            </select>

            <input type="text" name="transaction_id" id="transaction_id" placeholder="Enter bKash Transaction ID (Txn ID)" class="<?= $input_classes ?> md:col-span-2 hidden">

            <button type="submit" class="bg-gradient-to-r from-ilm-gold to-yellow-400 text-ilm-blue w-full py-4 rounded-xl font-black text-xl shadow-2xl shadow-ilm-gold/70 md:col-span-2 mt-8 hover:scale-105 hover:shadow-xl transition-all duration-300">
                Enroll Now <heroicon:arrow-right-solid class="w-6 h-6 inline-block ml-2"/>
            </button>
        </form>
    </div>

</main>

<?php include 'includes/footer.php'; ?>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-80 hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-3xl p-10 max-w-sm w-full text-center shadow-2xl border-t-8 border-green-500 transform scale-90 animate-fade-in-scale">
        <div class="text-7xl text-green-500 mb-5 animate-fade-in-up">🎉</div>
        <h2 class="text-3xl font-black text-ilm-blue mb-4 animate-fade-in-up">Enrollment Confirmed!</h2>
        <p class="text-gray-700 mb-8 animate-fade-in-up">Your registration for the **<?= htmlspecialchars($course['title']) ?>** masterclass is complete. Check your email for next steps.</p>
        <button onclick="closeModal()" class="bg-ilm-blue text-white px-10 py-3 rounded-full font-bold text-lg hover:bg-[#08152e] transition shadow-lg shadow-ilm-blue/40 animate-fade-in-up">
            Acknowledge
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
            <?php if(!$isLoggedIn): ?>
            document.querySelector('input[name="password"]').setAttribute('required', 'required');
            <?php endif; ?>
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
