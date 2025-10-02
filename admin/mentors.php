<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';
require_admin(); // Ensure admin is logged in

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM mentors WHERE id=?");
    $stmt->execute([$id]);
    set_flash("Mentor deleted successfully.");
    header('Location: mentors.php');
    exit;
}

// Fetch all mentors
$mentors = $pdo->query("SELECT * FROM mentors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$flash = get_flash();
?>
<?php include 'header_admin.php'; ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Mentors</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Mentors</h1>

    <!-- Flash Message -->
    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?= $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Add New Mentor Button -->
    <a href="mentor_add.php" class="inline-block mb-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Add New Mentor
    </a>

    <!-- Mentors Table -->
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-center">#</th>
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Email</th>
                    <th class="border p-2 text-left">Phone</th>
                    <th class="border p-2 text-left">Specialization</th>
                    <th class="border p-2 text-left">Bio</th> <!-- New Bio Column -->
                    <th class="border p-2 text-left">Photo</th>
                    <th class="border p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($mentors as $mentor): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2 text-center"><?= $i++ ?></td>
                        <td class="border p-2"><?= e($mentor['name']) ?></td>
                        <td class="border p-2"><?= e($mentor['email']) ?></td>
                        <td class="border p-2"><?= e($mentor['phone']) ?></td>
                        <td class="border p-2"><?= e($mentor['specialization']) ?></td>
                        <td class="border p-2"><?= e($mentor['bio']) ?></td> <!-- Display Bio -->
                        <td class="border p-2">
                            <?php if ($mentor['photo']): ?>
                                <img src="../assets/uploads/mentors/<?= e($mentor['photo']) ?>" alt="<?= e($mentor['name']) ?>" class="h-12 w-12 object-cover rounded-full">
                            <?php endif; ?>
                        </td>
                        <td class="">
                
                            <a href="?delete_id=<?= $mentor['id'] ?>" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this mentor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($mentors)): ?>
                    <tr>
                        <td colspan="8" class="border p-4 text-center text-gray-500">No mentors found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
