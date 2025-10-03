<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';
require_admin(); // Ensure admin is logged in

global $pdo; // Ensure $pdo is available

// Folder for uploads (relative path from this file)
$uploadDir = __DIR__ . '/../assets/uploads/mentors/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
         set_flash('Error: Could not create upload directory.', 'error');
         header('Location: mentors.php');
         exit;
    }
}

// Check for existing data for potential "Edit" mode (optional, but good practice for reuse)
$mentorData = null;
$pageTitle = "Add New Mentor";
$buttonText = "Add Mentor";
$isEditMode = false;

// --- Handle Form Submission (Add/Edit) ---
if(isset($_POST['save_mentor'])) {
    $id = intval($_POST['mentor_id'] ?? 0);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $specialization = trim($_POST['specialization']);

    $isEditMode = ($id > 0);
    $photoName = $_POST['existing_photo'] ?? '';

    try {
        // 1. Handle Photo Upload
        if(!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $newPhotoName = uniqid('mentor_', true) . '.' . strtolower($ext);

            // Move the new file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newPhotoName)) {
                // If in edit mode and a new photo is uploaded, delete the old one
                if ($isEditMode && $photoName && file_exists($uploadDir . $photoName)) {
                    unlink($uploadDir . $photoName);
                }
                $photoName = $newPhotoName;
            } else {
                 set_flash("Error uploading photo. Mentor details saved without new photo.", 'error');
            }
        }
        
        // 2. Database Operation
        if ($isEditMode) {
            // Update existing mentor
            $stmt = $pdo->prepare("UPDATE mentors SET name=?, email=?, phone=?, bio=?, specialization=?, photo=? WHERE id=?");
            $stmt->execute([$name, $email, $phone, $bio, $specialization, $photoName, $id]);
            set_flash("Mentor updated successfully.");
        } else {
            // Insert new mentor
            $stmt = $pdo->prepare("INSERT INTO mentors (name, email, phone, bio, specialization, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $phone, $bio, $specialization, $photoName]);
            set_flash("Mentor added successfully.");
        }
        
    } catch (PDOException $e) {
        set_flash("Database Error: Could not save mentor. " . $e->getMessage(), 'error');
    }

    header('Location: mentors.php');
    exit;
}
// --- End Handle Form Submission ---

// --- Handle Edit Data Fetch ---
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM mentors WHERE id = ?");
    $stmt->execute([$id]);
    $mentorData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($mentorData) {
        $pageTitle = "Edit Mentor: " . e($mentorData['name']);
        $buttonText = "Update Mentor";
        $isEditMode = true;
    } else {
        set_flash("Mentor not found.", 'error');
        header('Location: mentors.php');
        exit;
    }
}
// --- End Handle Edit Data Fetch ---

$flash = get_flash();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - <?= e($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>


<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-indigo-600 mr-4"><?= $isEditMode ? 'edit' : 'person_add' ?></span>
                <h1 class="text-3xl font-extrabold text-gray-800"><?= e($pageTitle) ?></h1>
            </div>
            <a href="mentors.php" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150">
                <span class="material-icons text-lg mr-1">arrow_back</span> Back to Mentor List
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-xl rounded-lg p-8 max-w-4xl mx-auto border-t-4 border-indigo-500">
            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <?php if ($isEditMode): ?>
                    <input type="hidden" name="mentor_id" value="<?= e($mentorData['id']) ?>">
                    <input type="hidden" name="existing_photo" value="<?= e($mentorData['photo']) ?>">
                <?php endif; ?>

                <div class="md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="<?= e($mentorData['name'] ?? '') ?>" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-1">
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Specialization/Title</label>
                    <input type="text" id="specialization" name="specialization" value="<?= e($mentorData['specialization'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="md:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($mentorData['email'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($mentorData['phone'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio/Short Description</label>
                    <textarea id="bio" name="bio" rows="4" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"><?= e($mentorData['bio'] ?? '') ?></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Mentor Photo (JPEG/PNG)</label>
                    <input type="file" id="photo" name="photo" accept="image/*" class="w-full p-3 border border-gray-300 rounded-lg bg-white">
                    
                    <?php if ($isEditMode && $mentorData['photo']): ?>
                        <div class="mt-4 flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Current Photo:</span>
                            <img src="<?= '../assets/uploads/mentors/' . e($mentorData['photo']) ?>" alt="Current Mentor Photo" class="h-16 w-16 object-cover rounded-full ring-2 ring-indigo-400">
                            <span class="text-xs text-gray-500">(Upload a new file to replace it)</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-gray-200">
                    <button type="submit" name="save_mentor" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-150 shadow-lg">
                        <span class="material-icons text-xl mr-2">save</span>
                        <?= e($buttonText) ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>