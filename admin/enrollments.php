<?php
// admin/enrollments.php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin(); // ensure admin is logged in

// Handle approve/cancel actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'approved' : 'canceled';

    $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
    $stmt->execute([$action, $id]);

    set_flash("Enrollment request has been $action.");
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
<body>
    <?php include __DIR__ . '/header_admin.php'; ?>
<div class="container mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">Manage Course Enrollments</h1>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'; ?>">
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
                               class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
