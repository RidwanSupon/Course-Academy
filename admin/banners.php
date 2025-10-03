<?php
// admin/banners.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Ensure $pdo is available
global $pdo; 

$errors = [];
$flash = get_flash(); // Use the standard flash function

// Upload folder
$destFolder = __DIR__ . '/../assets/uploads/banners';
if (!is_dir($destFolder)) {
    if (!mkdir($destFolder, 0755, true) && !is_dir($destFolder)) {
        set_flash('Error: Could not create upload directory.', 'error');
        header("Location: banners.php");
        exit;
    }
}

// Handle Add Banner
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    // NOTE: $link removed from processing here
    $sort_order = intval($_POST['sort_order'] ?? 0);

    // Basic validation
    if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Please select a banner image.";
    }

    if (empty($errors)) {
        $res = save_uploaded_image('image', $destFolder);
        
        if (isset($res['error'])) {
            $errors[] = $res['error'];
        } else {
            try {
                $imageName = basename($res['path']); // Save only filename
                // IMPORTANT: Removed 'link' column from INSERT statement and the corresponding value ($link)
                $stmt = $pdo->prepare("INSERT INTO banners (image,title,subtitle,sort_order) VALUES (?,?,?,?)");
                $stmt->execute([$imageName, $title, $subtitle, $sort_order]);
                set_flash("Banner **{$title}** added successfully.");
                header("Location: banners.php");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Database error: Could not save banner.";
                // Optionally delete uploaded file if DB insertion failed
                if (file_exists($res['path'])) unlink($res['path']);
            }
        }
    }
}

// Handle Delete Banner
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("SELECT image FROM banners WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        // 1. Delete the physical file
        if ($row && $row['image']) {
            $file = $destFolder . '/' . $row['image'];
            if (file_exists($file)) unlink($file);
        }
        
        // 2. Delete the database record
        $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$id]);
        set_flash("Banner deleted successfully.");
    } catch (PDOException $e) {
        set_flash("Database error: Could not delete banner.", 'error');
    }
    header("Location: banners.php");
    exit;
}

// Fetch all banners
$banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order DESC, id DESC")->fetchAll();

// Re-fetch flash messages if form submission resulted in errors/success
if (empty($flash)) {
    $flash = get_flash();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Banners</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">

<div class="flex">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-teal-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-teal-600 mr-4">wallpaper</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Manage Homepage Banners</h1>
            </div>
            <p class="text-lg text-gray-500">Total Banners: **<?= count($banners) ?>**</p>
        </div>
        
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
        <?php foreach($errors as $err): // Display direct form validation errors ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md bg-red-100 text-red-700 border border-red-300">
                <?= e($err) ?>
            </div>
        <?php endforeach; ?>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <div class="xl:col-span-1 bg-white p-6 rounded-xl shadow-xl border-t-4 border-blue-900 h-fit">
                <h3 class="text-xl font-bold mb-4 text-blue-900 flex items-center">
                    <span class="material-icons text-2xl mr-2 text-blue-900">add_photo_alternate</span>
                    Add New Banner
                </h3>
                
                <form method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Recommended size: 1640x800 px.</p>
                        <input type="file" name="image" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500" accept="image/*" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title (H1)</label>
                        <input name="title" class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500" value="<?= e($_POST['title'] ?? '') ?>" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle (Short Description)</label>
                        <input name="subtitle" class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500" value="<?= e($_POST['subtitle'] ?? '') ?>" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order (Higher number appears first)</label>
                        <input name="sort_order" type="number" class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500" value="<?= e($_POST['sort_order'] ?? 0) ?>" />
                    </div>

                    <button type="submit" class="w-full bg-blue-900 text-white font-semibold px-4 py-3 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md flex items-center justify-center">
                        <span class="material-icons mr-2">save</span>
                        Add Banner
                    </button>
                </form>
            </div>

            <div class="xl:col-span-2 space-y-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <span class="material-icons text-2xl mr-2 text-teal-600">view_carousel</span>
                    Existing Banners List
                </h3>
                
                <?php if (!empty($banners)): ?>
                    <?php foreach($banners as $b): ?>
                        <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-teal-400 flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                            
                            <img src="../assets/uploads/banners/<?= e($b['image']) ?>" 
                                 class="w-full sm:w-48 h-24 object-cover rounded-lg flex-shrink-0" 
                                 alt="<?= e($b['title']) ?>">
                            
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-lg truncate text-gray-900" title="<?= e($b['title']) ?>">
                                    <?= e($b['title']) ?>
                                </div>
                                <div class="text-sm text-gray-600 truncate" title="<?= e($b['subtitle']) ?>">
                                    <?= e($b['subtitle']) ?>
                                </div>
                                </div>
                            
                            <div class="flex flex-col items-end space-y-2 sm:space-y-0 sm:space-x-4">
                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                                    Order: <?= e($b['sort_order']) ?>
                                </span>
                                <a href="?delete=<?= $b['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete the banner: <?= e($b['title']) ?>?');" 
                                   class="inline-flex items-center text-red-600 hover:text-red-800 font-medium transition duration-150">
                                    <span class="material-icons text-lg mr-1">delete</span> Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg text-center text-gray-500 border-l-4 border-yellow-400">
                        <span class="material-icons text-4xl text-yellow-400 mb-2">info</span><br>
                        No banners have been added yet. Use the form on the left to create one.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>
</body>
</html>