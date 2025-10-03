<?php
// admin/mentors.php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';
require_admin(); // Ensure admin is logged in

global $pdo; // Ensure $pdo is available

// Base path for mentor photos (adjust 'basit/' if necessary based on your structure)
$mentor_upload_url = '../assets/uploads/mentors/';

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    try {
        // 1. Get the mentor's photo name before deletion
        $stmt = $pdo->prepare("SELECT photo FROM mentors WHERE id=?");
        $stmt->execute([$id]);
        $mentor = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Delete the physical file if it exists
        if ($mentor && $mentor['photo']) {
            $filePath = __DIR__ . '/' . $mentor_upload_url . $mentor['photo'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // 3. Delete the database record
        $stmt = $pdo->prepare("DELETE FROM mentors WHERE id=?");
        $stmt->execute([$id]);
        set_flash("Mentor deleted successfully, and associated photo removed.");
    } catch (PDOException $e) {
        set_flash("Database Error: Could not delete mentor.", 'error');
    }
    
    header('Location: mentors.php');
    exit;
}

// Fetch all mentors
$mentors = $pdo->query("SELECT * FROM mentors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$flash = get_flash();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Mentors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-indigo-600 mr-4">people_alt</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Manage Mentors</h1>
            </div>
            <p class="text-lg text-gray-500">Active Mentors: **<?= count($mentors) ?>**</p>
        </div>
        
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <div class="flex justify-end mb-6">
            <a href="mentor_add.php" class="inline-flex items-center bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-indigo-700 transition duration-150 shadow-md">
                <span class="material-icons text-xl mr-2">person_add</span>
                Add New Mentor
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name & Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Specialization</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Bio (Summary)</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($mentors)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-lg text-gray-500 font-medium">
                                    <span class="material-icons text-4xl text-indigo-400 mb-2">sentiment_dissatisfied</span><br>
                                    No mentor records found. Please add a new mentor.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($mentors as $mentor): ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-indigo-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($mentor['photo']): ?>
                                            <img src="<?= $mentor_upload_url . e($mentor['photo']) ?>" alt="<?= e($mentor['name']) ?>" class="h-12 w-12 object-cover rounded-full ring-2 ring-indigo-300">
                                        <?php else: ?>
                                            <span class="material-icons h-12 w-12 flex items-center justify-center bg-gray-200 text-gray-500 rounded-full">person</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900"><?= e($mentor['name']) ?></div>
                                        <div class="text-xs text-gray-600">Email: <?= e($mentor['email']) ?></div>
                                        <div class="text-xs text-gray-600">Phone: <?= e($mentor['phone']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= e($mentor['specialization']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs text-sm text-gray-700">
                                        <p class="line-clamp-2" title="<?= e($mentor['bio']) ?>"><?= e($mentor['bio']) ?></p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <a href="mentor_add.php?id=<?= $mentor['id'] ?>" 
                                           class="inline-flex items-center bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition duration-150 shadow-md">
                                            <span class="material-icons text-base mr-1">edit</span> Edit
                                        </a>
                                        
                                        <a href="?delete_id=<?= $mentor['id'] ?>" 
                                           class="inline-flex items-center bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition duration-150 shadow-md" 
                                           onclick="return confirm('Are you sure you want to PERMANENTLY delete the mentor <?= e($mentor['name']) ?> and their photo?');">
                                            <span class="material-icons text-base mr-1">delete</span> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>