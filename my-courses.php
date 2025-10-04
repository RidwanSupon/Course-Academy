<?php
session_start();
require_once 'config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch enrolled courses
$stmtEnroll = $pdo->prepare("
    SELECT e.*, c.title, c.short_desc, c.long_desc, c.teacher, c.price
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.created_at DESC
");
$stmtEnroll->execute([$user_id]);
$enrollments = $stmtEnroll->fetchAll(PDO::FETCH_ASSOC);

// Fetch free class requests
$stmtFree = $pdo->prepare("
    SELECT f.*, c.title, c.short_desc, c.long_desc, c.teacher, f.preferred_date, f.preferred_time
    FROM free_class_requests f
    JOIN courses c ON f.course_id = c.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmtFree->execute([$user_id]);
$free_requests = $stmtFree->fetchAll(PDO::FETCH_ASSOC);

// Include header
include 'includes/header.php';
?>

<div class="pt-28 max-w-6xl mx-auto p-4">
    <h1 class="text-3xl font-bold mb-8 text-center text-blue-900">My Courses & Free Classes</h1>
    <p class="text-center text-gray-500 mb-8">Click on any card to view the full course details.</p>

    <?php if(empty($enrollments) && empty($free_requests)): ?>
        <p class="text-gray-700 text-center">You have not enrolled in any courses or requested any free classes yet. 🙁</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php foreach ($enrollments as $course): ?>
                
                <a href="course.php?id=<?= $course['course_id'] ?>" 
                   class="block p-5 border rounded-xl border-green-400 shadow-md bg-green-50 hover:shadow-xl transform hover:scale-[1.02] transition duration-300 ease-in-out">
                    
                    <h2 class="text-xl font-bold mb-2 text-gray-800"><?= htmlspecialchars($course['title']) ?></h2>
                    <p class="text-gray-600 mb-3 text-sm"><?= htmlspecialchars($course['short_desc'] ?? 'No description') ?></p>
                    
                    <div class="border-t pt-3 mt-3 space-y-1">
                        <p class="text-xs text-gray-500">Teacher: <span class="font-medium text-gray-700"><?= htmlspecialchars($course['teacher'] ?? '-') ?></span></p>
                        <p class="text-xs text-gray-500">Price: <span class="font-medium text-gray-700"><?= $course['price'] > 0 ? '৳ '.number_format($course['price'], 0) : 'Free' ?></span></p>
                        <p class="text-xs text-gray-500">Enrollment Status: 
                            <?php if($course['status'] == 'Approved'): ?>
                                <span class="text-green-600 font-bold">✅ <?= $course['status'] ?></span>
                            <?php elseif($course['status'] == 'Pending'): ?>
                                <span class="text-yellow-600 font-bold">⏳ <?= $course['status'] ?></span>
                            <?php else: ?>
                                <span class="text-red-600 font-bold">❌ <?= $course['status'] ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if($course['status'] == 'Approved'): ?>
                            <p class="text-xs text-ilm-blue font-bold pt-1">Click to Access Course Content</p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>

            <?php foreach ($free_requests as $course): ?>
                
                <a href="course.php?id=<?= $course['course_id'] ?>" 
                   class="block p-5 border-2 border-blue-400 rounded-xl shadow-md bg-blue-50 hover:shadow-xl transform hover:scale-[1.02] transition duration-300 ease-in-out">
                    
                    <h2 class="text-xl font-bold mb-2 text-blue-800">
                        <?= htmlspecialchars($course['title']) ?> 
                        <span class="text-sm text-blue-600 font-normal">(Free Request)</span>
                    </h2>
                    <p class="text-gray-600 mb-3 text-sm"><?= htmlspecialchars($course['short_desc'] ?? 'No description') ?></p>
                    
                    <div class="border-t border-blue-200 pt-3 mt-3 space-y-1">
                        <p class="text-xs text-gray-500">Teacher: <span class="font-medium text-gray-700"><?= htmlspecialchars($course['teacher'] ?? '-') ?></span></p>
                        <p class="text-xs text-gray-500">Req. Date: <span class="font-medium text-gray-700"><?= $course['preferred_date'] ?? '-' ?></span></p>
                        <p class="text-xs text-gray-500">Req. Time: <span class="font-medium text-gray-700"><?= $course['preferred_time'] ?? '-' ?></span></p>
                        <p class="text-xs text-gray-500">Request Status: 
                            <?php if($course['status'] == 'New' || $course['status'] == 'Contacted'): ?>
                                <span class="text-yellow-600 font-bold">⏳ <?= $course['status'] ?></span>
                            <?php elseif($course['status'] == 'Scheduled'): ?>
                                <span class="text-green-600 font-bold">📅 <?= $course['status'] ?></span>
                            <?php else: ?>
                                <span class="text-red-600 font-bold">❌ <?= $course['status'] ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="text-xs text-blue-700 font-bold pt-1">Click for Details</p>
                    </div>
                </a>
                <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>