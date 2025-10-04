<?php
// course.php - Display Course Details and handle enrollment/free class/review logic

require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Get course ID
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// === Fetch course details AND JOIN with mentors table ===
$stmt = $pdo->prepare("
    SELECT 
        c.*, 
        m.name AS mentor_name,
        m.photo AS mentor_photo,
        m.specialization AS mentor_specialization
    FROM courses c
    LEFT JOIN mentors m ON c.mentor_id = m.id
    WHERE c.id = ? AND c.active = 1
");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if course not found
if (!$course) {
    header("Location: index.php");
    exit;
}
// Check if a mentor is linked
$mentorLinked = !empty($course['mentor_id']) && !empty($course['mentor_name']);
// === END OF COURSE FETCH ===

// User session data
$isLoggedIn     = !empty($_SESSION['user_id']);
$userID         = $isLoggedIn ? $_SESSION['user_id'] : 0;
$userName       = $isLoggedIn ? ($_SESSION['user_name'] ?? '')     : '';
$userEmail      = $isLoggedIn ? ($_SESSION['user_email'] ?? '')    : '';
$userPhone      = $isLoggedIn ? ($_SESSION['user_phone'] ?? '')    : '';
$userLocation   = $isLoggedIn ? ($_SESSION['user_location'] ?? '') : '';


// =========================================================================
// === REVIEW LOGIC (NEW) ===
// =========================================================================

// 1. Fetch Average Rating and Total Reviews
$stmtAvg = $pdo->prepare("
    SELECT AVG(rating) AS avg_rating, COUNT(id) AS total_reviews 
    FROM course_reviews 
    WHERE course_id = ? AND status = 'Approved'
");
$stmtAvg->execute([$course_id]);
$review_stats = $stmtAvg->fetch(PDO::FETCH_ASSOC);

$avg_rating = round($review_stats['avg_rating'] ?? 0, 1);
$total_reviews = $review_stats['total_reviews'];


// 2. Check Enrollment and Review Status for the CURRENT user
$alreadyEnrolled = false;
$is_enrolled_and_approved = false;
$has_reviewed = false;

if ($isLoggedIn) {
    // Check if user is enrolled (any status)
    $checkEnroll = $pdo->prepare("SELECT status FROM enrollments WHERE user_id=? AND course_id=?");
    $checkEnroll->execute([$userID, $course_id]);
    $enrollment_record = $checkEnroll->fetch(PDO::FETCH_ASSOC);
    
    $alreadyEnrolled = $enrollment_record ? true : false;
    $is_enrolled_and_approved = ($enrollment_record && $enrollment_record['status'] === 'Approved');

    // Check if user has already submitted a review
    $checkReview = $pdo->prepare("SELECT id FROM course_reviews WHERE user_id=? AND course_id=?");
    $checkReview->execute([$userID, $course_id]);
    $has_reviewed = $checkReview->fetch() ? true : false;
}

// 3. Fetch list of APPROVED reviews for display
$stmtReviews = $pdo->prepare("
    SELECT r.*, u.name AS reviewer_name
    FROM course_reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.course_id = ? AND r.status = 'Approved'
    ORDER BY r.created_at DESC
");
$stmtReviews->execute([$course_id]);
$reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

// =========================================================================
// === END OF REVIEW LOGIC ===
// =========================================================================


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
} elseif (isset($_GET['review_submitted']) && $_GET['review_submitted'] === 'true') {
    $successMessage = '✨ Thank you! Your review has been submitted successfully and is awaiting admin approval.';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        // --- Review Errors (NEW) ---
        case 'already_reviewed':
            $errorMessage = '⚠️ You have already submitted a review for this course.';
            break;
        case 'not_enrolled':
            $errorMessage = '❌ You must be an approved, enrolled student to leave a review.';
            break;
        case 'invalid_review_data':
        case 'db_error':
            $errorMessage = 'An error occurred while processing your review. Please try again.';
            break;
        // --- Enrollment/Request Errors (Existing) ---
        case 'already_requested':
            $errorMessage = '⚠️ You already have an active Free Class request for this course.';
            break;
        case 'missing_fields':
            $errorMessage = '❌ Please fill in all required fields.';
            break;
        case 'request_failed':
            $errorMessage = 'An error occurred while submitting your request. Please try again.';
            break;
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
            
            <?php if ($mentorLinked): ?>
            <div class="mt-10 pt-6 border-t border-gray-200">
                <h3 class="text-2xl font-bold text-ilm-blue mb-4">Your Instructor</h3>
                <a href="mentor_details.php?id=<?= intval($course['mentor_id']) ?>" 
                   class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-200 group shadow-md">
                    
                    <img src="assets/uploads/mentors/<?= htmlspecialchars($course['mentor_photo'] ?? 'default.png') ?>" 
                        alt="<?= htmlspecialchars($course['mentor_name']) ?>" 
                        class="w-20 h-20 object-cover rounded-full ring-4 ring-ilm-gold/50 group-hover:ring-ilm-gold">
                            
                    <div>
                        <span class="text-xl font-extrabold text-ilm-blue group-hover:text-indigo-700 transition block">
                            <?= htmlspecialchars($course['mentor_name']) ?>
                        </span>
                        <span class="text-sm text-gray-600 block italic">
                            <?= htmlspecialchars($course['mentor_specialization'] ?? 'Certified Instructor') ?>
                        </span>
                        <span class="text-xs text-ilm-gold mt-1 font-semibold group-hover:underline">
                            View Full Profile →
                        </span>
                    </div>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-1 bg-gradient-to-br from-ilm-blue to-indigo-900 rounded-3xl shadow-2xl shadow-ilm-gold/50 border-t-8 border-ilm-gold p-8 h-fit order-1 lg:order-2">
            <h2 class="text-2xl font-extrabold text-white mb-6 pb-4 border-b border-white/20">
                Course Insights
            </h2>

            <div class="space-y-6 text-white text-lg">
                <div class="flex justify-between bg-white/10 p-3 rounded-xl">
                    <span>Mentor</span>
                    <span class="font-bold text-ilm-gold">
                        <?= $mentorLinked ? htmlspecialchars($course['mentor_name']) : htmlspecialchars($course['teacher']) ?>
                    </span>
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
    
    <div class="bg-white rounded-3xl shadow-xl p-10 mb-12 border border-gray-100">
        <h2 class="text-4xl font-extrabold text-blue-900 mb-8 pb-3 border-b-4 border-ilm-gold inline-block">
            Student Reviews (<?= $total_reviews ?>)
        </h2>

        <?php if ($total_reviews > 0): ?>
            <div class="flex items-center space-x-4 mb-8">
                <span class="text-5xl font-black text-ilm-gold"><?= $avg_rating ?></span>
                <div class="flex flex-col">
                    <div class="text-xl text-yellow-500">
                        <?php 
                        // Simple star display based on avg_rating
                        $fullStars = floor($avg_rating);
                        $hasHalf = ($avg_rating - $fullStars) >= 0.5;
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $fullStars) {
                                echo '★';
                            } elseif ($i == $fullStars && $hasHalf) {
                                echo '½'; // Placeholder for half-star
                            } else {
                                echo '☆'; // Empty star
                            }
                        }
                        ?>
                    </div>
                    <span class="text-gray-600 text-sm italic">Based on <?= $total_reviews ?> approved reviews</span>
                </div>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-lg mb-8">No reviews yet. Be the first to share your experience!</p>
        <?php endif; ?>

        <?php if ($isLoggedIn && $is_enrolled_and_approved && !$has_reviewed): ?>
            <div class="review-form-section mt-10 p-6 border-2 border-green-500 rounded-xl bg-green-50 shadow-md">
                <h3 class="text-2xl font-semibold mb-4 text-green-700">✍️ Submit Your Review</h3>
                <form action="process_review.php" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course_id ?>">
                    <input type="hidden" name="user_id" value="<?= $userID ?>">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Rating (1-5)</label>
                        <select name="rating" required class="p-2 border-2 border-gray-300 rounded-lg w-full md:w-1/4 text-gray-800">
                            <option value="5">5 Stars (Excellent) ⭐⭐⭐⭐⭐</option>
                            <option value="4">4 Stars (Very Good) ⭐⭐⭐⭐</option>
                            <option value="3">3 Stars (Good) ⭐⭐⭐</option>
                            <option value="2">2 Stars (Fair) ⭐⭐</option>
                            <option value="1">1 Star (Poor) ⭐</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="review_text" class="block text-gray-700 font-medium mb-2">Your Feedback</label>
                        <textarea id="review_text" name="review_text" rows="4" required 
                                  class="p-3 border-2 border-gray-300 rounded-lg w-full focus:border-green-500 focus:ring-2 focus:ring-green-200"></textarea>
                    </div>

                    <button type="submit" name="submit_review" 
                            class="bg-green-600 text-white py-2 px-6 rounded-full font-bold shadow-lg hover:bg-green-700 transition duration-300">
                        Post Review
                    </button>
                </form>
            </div>
        <?php elseif ($isLoggedIn && $has_reviewed): ?>
            <p class="mt-8 p-4 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded-xl">
                📝 You have already submitted a review for this course. It is visible below once approved by the admin.
            </p>
        <?php elseif (!$isLoggedIn): ?>
            <p class="mt-8 p-4 bg-blue-100 text-blue-700 border border-blue-300 rounded-xl">
                Login to submit your review after enrolling in this course.
            </p>
        <?php endif; ?>

        <div class="mt-12 space-y-8">
            <h3 class="text-2xl font-extrabold text-ilm-blue border-b pb-2">What Students Say</h3>
            <?php if (empty($reviews)): ?>
                <p class="text-gray-500">No approved reviews to display yet.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="border-b border-gray-100 pb-6">
                        <div class="flex items-center mb-2">
                            <span class="font-bold text-gray-800 mr-3"><?= htmlspecialchars($review['reviewer_name']) ?></span>
                            <span class="text-yellow-500 text-lg font-bold">
                                <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                            </span>
                        </div>
                        <p class="text-gray-700 italic leading-relaxed">"<?= nl2br(htmlspecialchars($review['review_text'])) ?>"</p>
                        <span class="text-xs text-gray-400 block mt-2">Reviewed on <?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
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
if (urlParams.has('enrolled') || urlParams.has('free_requested') || urlParams.has('error') || urlParams.has('review_submitted')) {
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