<?php
// admin/enrollments.php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_admin(); // ensure admin is logged in

// Handle approve/cancel actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'approved' : 'canceled';

    $stmt = $conn->prepare("UPDATE enrollments SET status=? WHERE id=?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();

    set_flash("Enrollment request has been $action.");
    header('Location: enrollments.php');
    exit;
}

// Fetch all enrollments
$enrollments = mysqli_query($conn, "SELECT * FROM enrollments ORDER BY created_at DESC");

$flash = get_flash();
?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Course Enrollments</h1>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
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
                <?php $i = 1; while ($row = mysqli_fetch_assoc($enrollments)): ?>
                <tr>
                    <td class="border p-2 text-center"><?php echo $i++; ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['location']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['course']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td class="border p-2 text-center">
                        <?php if($row['status'] === 'approved'): ?>
                            <span class="text-green-700 font-bold">Approved</span>
                        <?php elseif($row['status'] === 'canceled'): ?>
                            <span class="text-red-700 font-bold">Canceled</span>
                        <?php else: ?>
                            <span class="text-yellow-700 font-bold">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="border p-2 text-center space-x-2">
                        <?php if($row['status'] !== 'approved'): ?>
                            <a href="?action=approve&id=<?php echo $row['id']; ?>"
                               class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Approve</a>
                        <?php endif; ?>
                        <?php if($row['status'] !== 'canceled'): ?>
                            <a href="?action=cancel&id=<?php echo $row['id']; ?>"
                               class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
