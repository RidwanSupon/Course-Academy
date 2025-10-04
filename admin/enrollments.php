<?php
// admin/enrollments.php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin(); // ensure admin is logged in

global $pdo;

// ===============================
// Handle Approve / Cancel / Delete
// ===============================
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = strtolower(trim($_GET['action']));

    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE enrollments SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$id]);
            set_flash("Enrollment request for ID: $id has been approved.");
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE enrollments SET status = 'Canceled' WHERE id = ?");
            $stmt->execute([$id]);
            set_flash("Enrollment request for ID: $id has been canceled.");
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
            $stmt->execute([$id]);
            set_flash("Enrollment record for ID: $id has been permanently deleted.");
        }
    } catch (PDOException $e) {
        set_flash("Database Error: Unable to perform $action on enrollment.", 'error');
    }

    header('Location: enrollments.php');
    exit;
}

// ===============================
// Fetch Pending Enrollments
// ===============================
$stmt = $pdo->query("
    SELECT e.*, c.title AS course_name
    FROM enrollments e
    LEFT JOIN courses c ON e.course_id = c.id
    WHERE e.status = 'Pending'
    ORDER BY e.created_at DESC
");
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = get_flash();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin - Manage Enrollment Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">

    <div class="flex">
        <!-- Sidebar / Header -->
        <?php include __DIR__ . '/header_admin.php'; ?>

        <main class="flex-1 p-8">
            <!-- Page Header -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-600">
                <div class="flex items-center">
                    <span class="material-icons text-4xl text-indigo-600 mr-4">how_to_reg</span>
                    <h1 class="text-3xl font-extrabold text-gray-800">New Enrollment Requests</h1>
                </div>
                <p class="text-lg text-gray-500">
                    Pending Approvals: <strong><?= count($enrollments) ?></strong>
                </p>
            </div>

            <!-- Flash Message -->
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-lg font-medium shadow-md
                <?= $flash['type'] === 'error'
                    ? 'bg-red-100 text-red-700 border border-red-300'
                    : 'bg-green-100 text-green-700 border border-green-300' ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Enrollment Table -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Student & Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Payment Details</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Requested On</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($enrollments)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-lg text-gray-500 font-medium">
                                    <span class="material-icons text-4xl text-green-400 mb-2">check_circle_outline</span><br>
                                    All clear! No pending enrollment requests at this time.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1;
                            foreach ($enrollments as $row): ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-indigo-50 transition duration-150">
                                    <!-- Index -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $i++ ?></td>

                                    <!-- Student Info -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900"><?= e($row['name']) ?></div>
                                        <div class="text-xs text-gray-600">Email: <?= e($row['email'] ?? 'N/A') ?></div>
                                        <div class="text-xs text-gray-600">Phone: <?= e($row['phone'] ?? 'N/A') ?></div>
                                        <div class="text-xs text-gray-600">Location: <?= e($row['location'] ?? 'N/A') ?></div>
                                    </td>

                                    <!-- Course -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        <?= e($row['course_name'] ?? 'Course Deleted') ?>
                                    </td>

                                    <!-- Payment -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-800"><?= e($row['payment_method']) ?></div>
                                        <div class="text-xs text-gray-500">TXN ID: <?= e($row['bkash_txn_id'] ?? 'N/A') ?></div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">
                                            <?= e(ucfirst($row['status'])) ?>
                                        </span>
                                    </td>

                                    <!-- Requested On -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-y-2">
                                        <a href="?action=approve&id=<?= $row['id'] ?>"
                                            class="inline-flex items-center justify-center bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition duration-150 shadow-md">
                                            <span class="material-icons text-base mr-1">done</span> Approve
                                        </a>
                                        <a href="?action=delete&id=<?= $row['id'] ?>"
                                            onclick="return confirm('Are you sure you want to PERMANENTLY delete this enrollment record?');"
                                            class="inline-flex items-center justify-center bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
                                            <span class="material-icons text-base mr-1">delete</span> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>