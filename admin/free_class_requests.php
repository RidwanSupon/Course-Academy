<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Ensure $pdo is available
global $pdo;

// ===========================================
// 1. ACTION LOGIC: Handle Status Update
// ===========================================
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Check if the current request is a *confirmation* redirect (e.g., ?update=success)
// This is crucial to prevent the redirect loop.
$is_confirmation_redirect = isset($_GET['update']);

// Simple validation to prevent bad updates
$allowed_statuses = ['Contacted', 'Scheduled', 'Cancelled', 'Completed'];

// *** MODIFIED CONDITIONAL: Only process the action if it's not a confirmation redirect ***
if ($request_id > 0 && in_array($new_status, $allowed_statuses) && !$is_confirmation_redirect) {
    try {
        $stmt = $pdo->prepare("
            UPDATE free_class_requests 
            SET status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$new_status, $request_id]);
        
        // Redirect to clear the original action GET parameters and display success message
        header("Location: free_class_requests.php?update=success&status=" . urlencode($new_status) . "&id=" . $request_id);
        exit;
        
    } catch (PDOException $e) {
        // Log error and redirect with an error message
        error_log("Failed to update request status: " . $e->getMessage());
        header("Location: free_class_requests.php?update=error");
        exit;
    }
}
// Note: If no action was performed or the request was invalid, execution continues to the view logic below.
// ===========================================


// ===========================================
// 2. VIEW LOGIC: Fetch Data and Prepare for Display
// ===========================================

// Fetch all New or Pending free class requests, ordered by creation date
$stmt = $pdo->prepare("
    SELECT 
        fcr.*, 
        c.title AS course_title
    FROM 
        free_class_requests fcr
    JOIN 
        courses c ON fcr.course_id = c.id
    WHERE 
        fcr.status IN ('New', 'Pending') 
    ORDER BY 
        fcr.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to safely output dates (optional, for readability)
function formatDate($date) {
    return $date ? date('Y-m-d H:i', strtotime($date)) : 'N/A';
}

// Set current page for sidebar highlighting
$current = basename($_SERVER['PHP_SELF']); 
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Free Class Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">
   
  <?php include __DIR__ . '/header_admin.php'; ?>

        <main class="flex-1 p-8">
            <h1 class="text-2xl font-bold mb-6">Free Class Requests (Pending)</h1>
            
            <?php
            // Display status update messages
            if (isset($_GET['update']) && $_GET['update'] === 'success') {
                $status = htmlspecialchars($_GET['status'] ?? 'Unknown');
                $id = htmlspecialchars($_GET['id'] ?? '');
                echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>Request ID ' . $id . ' status updated to: ' . $status . '</p>
                      </div>';
            } elseif (isset($_GET['update']) && $_GET['update'] === 'error') {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">
                        <p class="font-bold">Error!</p>
                        <p>Failed to update the request status. Check the logs for details.</p>
                      </div>';
            }
            ?>
            
            <?php if (empty($requests)): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
                    <p class="font-bold">Hooray!</p>
                    <p>There are no new pending free class requests at this time.</p>
                </div>
            <?php else: ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time Pref.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($req['id']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($req['name']) ?>
                                        <div class="text-xs text-gray-500">Email: <?= htmlspecialchars($req['email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?= htmlspecialchars($req['phone']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                                        <a href="course_edit.php?id=<?= $req['course_id'] ?>" title="View Course Details">
                                            <?= htmlspecialchars($req['course_title']) ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        Date: <?= htmlspecialchars($req['preferred_date'] ?? 'N/A') ?><br>
                                        Time: <span class="font-semibold"><?= htmlspecialchars($req['preferred_time'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($req['created_at']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <!-- Actions now include the Completed status, matching the previous SQL fix -->
                                        <a href="free_class_requests.php?id=<?= $req['id'] ?>&status=Scheduled" class="text-green-600 hover:text-green-900 mr-2">Scheduled</a>
                                        <a href="free_class_requests.php?id=<?= $req['id'] ?>&status=Cancelled" class="text-red-600 hover:text-red-900 mr-2">Cancel</a>
                                    </td>
                                </tr>
                                <?php if (!empty($req['message'])): ?>
                                    <tr class="bg-gray-50">
                                        <td colspan="7" class="px-6 py-2 text-xs text-gray-600 italic border-t border-gray-200">
                                            **Message:** <?= htmlspecialchars($req['message']) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
