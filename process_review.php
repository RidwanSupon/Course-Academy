<?php
session_start();
require_once __DIR__ . '/config.php'; 

// 1. ইউজার লগইন চেক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // অ্যাকশন চেক করার জন্য নতুন ভ্যারিয়েবল

// 2. প্রাথমিক ডেটা ভ্যালিডেশন
if (!$course_id) {
    header("Location: index.php?error=invalid_course");
    exit;
}

// =========================================================
// === ১. রিভিউ ডিলিট করার লজিক (NEW DELETE LOGIC) ===
// =========================================================
if ($action === 'delete') {
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);

    if (!$review_id) {
        header("Location: course.php?id=" . $course_id . "&error=invalid_review_data");
        exit;
    }

    try {
        // সিকিউরিটি: শুধুমাত্র ইউজার তার নিজের রিভিউ ডিলিট করতে পারবে
        $stmt = $pdo->prepare("
            DELETE FROM course_reviews 
            WHERE id = ? AND user_id = ? AND course_id = ?
        ");
        $stmt->execute([$review_id, $user_id, $course_id]);

        if ($stmt->rowCount() > 0) {
            // ডিলিট সফল
            header("Location: course.php?id=" . $course_id . "&review_deleted=true");
            exit;
        } else {
            // ডিলিট ব্যর্থ (রিভিউ হয়তো অন্য কারো অথবা আইডি ভুল)
            header("Location: course.php?id=" . $course_id . "&error=delete_failed");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Review Deletion PDO Error: " . $e->getMessage());
        header("Location: course.php?id=" . $course_id . "&error=db_error");
        exit;
    }
}

// =========================================================
// === ২. নতুন রিভিউ সাবমিট করার লজিক (EXISTING SUBMISSION LOGIC) ===
// =========================================================

// ইনপুট ডেটা সংগ্রহ ও স্যানিটাইজ করা
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

// শুধুমাত্র 'submit_review' POST করা হলে বাকিটা রান হবে
if (isset($_POST['submit_review'])) {

    // প্রাথমিক ডেটা ভ্যালিডেশন
    if ($rating === false || empty($review_text) || $rating < 1 || $rating > 5) {
        header("Location: course.php?id=" . $course_id . "&error=invalid_review_data");
        exit;
    }

    try {
        // এনরোলমেন্ট এবং ডুপ্লিকেট রিভিউ চেক
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

        // ডাটাবেসে রিভিউ ইনসার্ট (Status: 'Approved')
        $stmtInsert = $pdo->prepare("
            INSERT INTO course_reviews (course_id, user_id, rating, review_text, status)
            VALUES (?, ?, ?, ?, 'Approved') 
        ");
        
        $stmtInsert->execute([$course_id, $user_id, $rating, $review_text]);

        // সফল হলে course.php তে রিডাইরেক্ট করা
        header("Location: course.php?id=" . $course_id . "&review_submitted=true");
        exit;

    } catch (PDOException $e) {
        error_log("Review Submission PDO Error: " . $e->getMessage());
        header("Location: course.php?id=" . $course_id . "&error=db_error");
        exit;
    }
}

// যদি কোনো অ্যাকশনই না আসে (না ডিলিট, না সাবমিট)
header("Location: course.php?id=" . $course_id);
exit;
?>