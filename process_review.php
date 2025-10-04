<?php
session_start();
require_once __DIR__ . '/config.php'; 

// 1. ইউজার লগইন চেক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ইনপুট ডেটা সংগ্রহ ও স্যানিটাইজ করা
$user_id = $_SESSION['user_id'];
$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

// 2. প্রাথমিক ডেটা ভ্যালিডেশন
if (!isset($_POST['submit_review']) || !$course_id || $rating === false || empty($review_text) || $rating < 1 || $rating > 5) {
    header("Location: course.php?id=" . ($course_id ?: 0) . "&error=invalid_review_data");
    exit;
}

try {
    // 3. এনরোলমেন্ট এবং ডুপ্লিকেট রিভিউ চেক
    $stmtCheck = $pdo->prepare("
        SELECT 
            (SELECT status FROM enrollments WHERE user_id = ? AND course_id = ?) AS enrollment_status,
            (SELECT id FROM course_reviews WHERE user_id = ? AND course_id = ?) AS review_exists
    ");
    $stmtCheck->execute([$user_id, $course_id, $user_id, $course_id]);
    $checks = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $enrollment_status = $checks['enrollment_status'];
    $review_exists = $checks['review_exists'];

    if ($enrollment_status !== 'Approved') {
        header("Location: course.php?id=" . $course_id . "&error=not_enrolled");
        exit;
    }

    if ($review_exists) {
        header("Location: course.php?id=" . $course_id . "&error=already_reviewed");
        exit;
    }

    // 4. ডাটাবেসে রিভিউ ইনসার্ট (Status: 'Approved' - এডমিন কনফার্মেশন ছাড়াই)
    $stmtInsert = $pdo->prepare("
        INSERT INTO course_reviews (course_id, user_id, rating, review_text, status)
        VALUES (?, ?, ?, ?, 'Approved') 
    ");
    
    // এখানে 'Approved' স্ট্রিংটি প্যারামিটার হিসেবে পাস করা হচ্ছে
    $stmtInsert->execute([$course_id, $user_id, $rating, $review_text]);

    // 5. সফল হলে course.php তে রিডাইরেক্ট করা
    header("Location: course.php?id=" . $course_id . "&review_submitted=true");
    exit;

} catch (PDOException $e) {
    error_log("Review Submission PDO Error: " . $e->getMessage());
    header("Location: course.php?id=" . $course_id . "&error=db_error");
    exit;
}

exit;
?>