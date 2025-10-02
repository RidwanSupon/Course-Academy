<?php
require_once 'config.php';


if(!isset($_SESSION['user_email'])){
    header("Location: login.php");
    exit;
}

$userEmail = $_SESSION['user_email'];

$stmt = $pdo->prepare("
    SELECT e.*, c.title, c.short_desc, c.price
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.email = ?
    ORDER BY e.created_at DESC
");
$stmt->execute([$userEmail]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Courses - ILM PATH NETWORK</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

<?php include 'includes/header.php'; ?>

<main class="pt-32 px-4 md:px-6 max-w-7xl mx-auto">
<h1 class="text-3xl font-bold text-ilm-blue mb-8 text-center">My Enrolled Courses</h1>

<?php if(empty($enrollments)): ?>
    <p class="text-center text-gray-700 text-lg">You haven't enrolled in any courses yet.</p>
<?php else: ?>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach($enrollments as $enroll): ?>
    <div class="bg-white rounded-3xl shadow-lg p-6 border border-gray-100">
        <h2 class="text-xl font-bold text-ilm-blue mb-2"><?= htmlspecialchars($enroll['title']) ?></h2>
        <p class="text-gray-700 mb-4"><?= htmlspecialchars($enroll['short_desc']) ?></p>
        <p class="font-semibold mb-4"><?= $enroll['price'] > 0 ? '৳ '.number_format($enroll['price'],0) : 'Free' ?></p>
        <p class="text-sm text-gray-500 mb-4">Status: 
            <?php if($enroll['status'] == 'Approved'): ?>
                <span class="text-green-600 font-bold"><?= $enroll['status'] ?></span>
            <?php elseif($enroll['status'] == 'Pending'): ?>
                <span class="text-yellow-600 font-bold"><?= $enroll['status'] ?></span>
            <?php else: ?>
                <span class="text-red-600 font-bold"><?= $enroll['status'] ?></span>
            <?php endif; ?>
        </p>
        <a href="course.php?id=<?= $enroll['course_id'] ?>" class="inline-block bg-ilm-gold text-ilm-blue font-bold py-2 px-4 rounded hover:opacity-90 transition">Go to Course</a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>
