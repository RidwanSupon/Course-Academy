<?php
// admin/enrollments.php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin(); // ensure admin is logged in

// Handle approve / cancel / delete actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = strtolower($_GET['action']);

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash("Enrollment request has been approved.");
    }
    elseif ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'canceled' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash("Enrollment request has been canceled.");
    }
    elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->execute([$id]);
        set_flash("Enrollment record has been deleted (treated as canceled).");
    }

    header('Location: enrollments.php');
    exit;
}

// Fetch all enrollments with course title
$stmt = $pdo->query("
    SELECT e.*, c.title AS course_name
    FROM enrollments e
    LEFT JOIN courses c ON e.course_id = c.id
    ORDER BY e.created_at DESC
");
$enrollments = $stmt->fetchAll();

$flash = get_flash();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Enrollments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<?php include __DIR__ . '/header_admin.php'; ?>

<div class="container mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">Manage Course Enrollments</h1>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?= $flash['type'] === 'error'
            ? 'bg-red-200 text-red-800'
            : 'bg-green-200 text-green-800' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Enrollments Table -->
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-center">#</th>
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Email</th>
                    <th class="border p-2 text-left">Location</th>
                    <th class="border p-2 text-left">Phone</th>
                    <th class="border p-2 text-left">Course</th>
                    <th class="border p-2 text-left">Payment Method</th>
                    <th class="border p-2 text-left">Transaction ID</th>
                    <th class="border p-2 text-center">Status</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($enrollments as $row): ?>
                    <tr>
                        <td class="border p-2 text-center"><?= $i++ ?></td>
                        <td class="border p-2"><?= e($row['name']) ?></td>
                        <td class="border p-2"><?= e($row['email'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= e($row['location'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= e($row['phone'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= e($row['course_name'] ?? 'N/A') ?></td>
                        <td class="border p-2"><?= e($row['payment_method']) ?></td>
                        <td class="border p-2"><?= e($row['bkash_txn_id'] ?? 'N/A') ?></td>
                        <td class="border p-2 text-center">
                            <?php if (strtolower($row['status']) === 'approved'): ?>
                                <span class="text-green-700 font-bold">Approved</span>
                            <?php elseif (strtolower($row['status']) === 'canceled'): ?>
                                <span class="text-red-700 font-bold">Canceled</span>
                            <?php else: ?>
                                <span class="text-yellow-700 font-bold">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="border p-2 text-center space-x-2">
                            <?php if (strtolower($row['status']) !== 'approved'): ?>
                                <a href="?action=approve&id=<?= $row['id'] ?>"
                                   class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Approve</a>
                            <?php endif; ?>
                            <?php if (strtolower($row['status']) !== 'canceled'): ?>
                                <a href="?action=cancel&id=<?= $row['id'] ?>"
                                   class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">Cancel</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?= $row['id'] ?>"
                               onclick="return confirm('Are you sure you want to delete this enrollment? This action cannot be undone.');"
                               class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-800">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
