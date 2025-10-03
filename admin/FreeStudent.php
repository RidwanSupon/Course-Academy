<?php
// admin/free_class_scheduled.php (assuming this is the filename)
require_once __DIR__ . '/../includes/functions.php';
require_admin();

global $pdo;

// Fetch all scheduled (approved) free class requests, joining with courses to get the course title
try {
    $stmt = $pdo->prepare("
        SELECT 
            f.*, 
            c.title AS course_title
        FROM free_class_requests f
        JOIN courses c ON f.course_id = c.id
        WHERE f.status = 'Scheduled'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Stop execution or redirect if database connection fails
    die("Database error: Could not fetch scheduled free class requests.");
}

// Get counts for dashboard/sidebar badge (optional, but keeping the logic)
try {
    $counts = [];
    $counts['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status='Pending'")->fetchColumn();
    // Assuming 'New' is the pending status for free class requests
    $counts['free_requests'] = $pdo->query("SELECT COUNT(*) FROM free_class_requests WHERE status='New'")->fetchColumn(); 
} catch (PDOException $e) {
    $counts = ['enrollments' => 0, 'free_requests' => 0];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Scheduled Free Classes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
<div class="flex">

    <?php include __DIR__ . '/header_admin.php'; ?>    
    
    <main class="flex-1 p-8">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-purple-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-purple-600 mr-4">event_available</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Approved (Scheduled) Free Classes</h1>
            </div>
            <p class="text-lg text-gray-500">Total Scheduled: **<?= count($students) ?>**</p>
        </div>
        
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Student & Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Requested Course</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Scheduled Time</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Requested On</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($students)): ?>
                            <?php $i = 0; foreach ($students as $student): ?>
                            <tr class="<?= $i++ % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?> hover:bg-purple-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($student['name']) ?></div>
                                    <div class="text-xs text-gray-600">Email: <?= htmlspecialchars($student['email'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-gray-600">Phone: <?= htmlspecialchars($student['phone'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        <?= htmlspecialchars($student['course_title']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="font-bold text-purple-700">Date: <?= htmlspecialchars($student['preferred_date'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-gray-600">Time: <?= htmlspecialchars($student['preferred_time'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                        <?= htmlspecialchars($student['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($student['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-lg text-gray-500 font-medium">
                                    <span class="material-icons text-4xl text-purple-400 mb-2">event_busy</span><br>
                                    No free classes are currently scheduled.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
</body>
</html>