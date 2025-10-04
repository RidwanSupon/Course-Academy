<?php
// admin/enrolled_students.php (assuming this is the filename)
require_once __DIR__ . '/../includes/functions.php';
require_admin();

global $pdo;

// Fetch all approved enrollments, joining with courses to get the course title
try {
    $stmt = $pdo->prepare("
        SELECT 
            e.*, 
            c.title AS course_title
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.status = 'Approved'
        ORDER BY e.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Stop execution or redirect if database connection fails
    die("Database error: Could not fetch approved enrollments.");
}

// Get counts for dashboard/sidebar badge (optional, but keeping the logic)
try {
    $counts = [];
    $counts['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status='Pending'")->fetchColumn();
    $counts['free_requests'] = $pdo->query("SELECT COUNT(*) FROM free_class_requests WHERE status='New'")->fetchColumn();
} catch (PDOException $e) {
    $counts = ['enrollments' => 0, 'free_requests' => 0];
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin - Enrolled Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex">

        <?php include __DIR__ . '/header_admin.php'; ?>

        <main class="flex-1 p-8">
            <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-green-600">
                <div class="flex items-center">
                    <span class="material-icons text-4xl text-green-600 mr-4">school</span>
                    <h1 class="text-3xl font-extrabold text-gray-800">Approved Enrolled Students</h1>
                </div>
                <p class="text-lg text-gray-500">Total Enrolled: **<?= count($students) ?>**</p>
            </div>

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Student Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Enrolled Course</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact Details</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Payment Info</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($students)): ?>
                                <?php $i = 0;
                                foreach ($students as $student): ?>
                                    <tr class="<?= $i++ % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?> hover:bg-green-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($student['name']) ?></div>
                                            <div class="text-xs text-gray-500">#<?= htmlspecialchars($student['id']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                <?= htmlspecialchars($student['course_title']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="truncate" title="<?= htmlspecialchars($student['email']) ?>"><?= htmlspecialchars($student['email'] ?? 'N/A') ?></div>
                                            <div class="text-xs text-gray-500">Phone: <?= htmlspecialchars($student['phone'] ?? 'N/A') ?></div>
                                            <div class="text-xs text-gray-500">Location: <?= htmlspecialchars($student['location'] ?? 'N/A') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="font-medium"><?= htmlspecialchars($student['payment_method']) ?></div>
                                            <div class="text-xs text-gray-500">TXN ID: <?= htmlspecialchars($student['bkash_txn_id'] ?? 'N/A') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M d, Y', strtotime($student['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-lg text-gray-500 font-medium">
                                        <span class="material-icons text-4xl text-green-400 mb-2">check_circle</span><br>
                                        The approved student list is currently empty.
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