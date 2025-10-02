<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';
require_admin();

$flash = null;

// Folder for uploads
$uploadDir = __DIR__ . '/../assets/uploads/mentors/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Handle form submission
if(isset($_POST['add_mentor'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $specialization = trim($_POST['specialization']);

    // Handle photo upload
    $photoName = '';
    if(!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = uniqid('mentor_', true) . '.' . strtolower($ext);
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
    }

    $stmt = $pdo->prepare("INSERT INTO mentors (name,email,phone,bio,specialization,photo,created_at) VALUES (?,?,?,?,?,?,NOW())");
    $stmt->execute([$name,$email,$phone,$bio,$specialization,$photoName]);
    set_flash("Mentor added successfully.");
    header('Location: mentors.php');
    exit;
}
?>
<?php include 'header_admin.php'; ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Add Mentor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Add New Mentor</h1>

    <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-lg">
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" required class="w-full border rounded p-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Phone</label>
            <input type="text" name="phone" class="w-full border rounded p-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Bio</label>
            <textarea name="bio" class="w-full border rounded p-2"></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Specialization</label>
            <input type="text" name="specialization" class="w-full border rounded p-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Photo</label>
            <input type="file" name="photo" accept="image/*" class="w-full">
        </div>
        <button type="submit" name="add_mentor" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Mentor</button>
    </form>
</div>
