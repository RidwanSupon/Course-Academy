<?php
// admin/gallery.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Ensure $pdo is available
global $pdo;

// Folder for uploads
$uploadFolder = __DIR__ . '/../assets/uploads/gallery/';
if (!is_dir($uploadFolder)) {
    if (!mkdir($uploadFolder, 0755, true) && !is_dir($uploadFolder)) {
        set_flash('Error: Could not create upload directory.', 'error');
        header('Location: gallery.php');
        exit;
    }
}

/* ---------- MULTIPLE IMAGE UPLOAD ---------- */
if (isset($_POST['add_images'])) {
    $caption = trim($_POST['caption'] ?? '');
    $files = $_FILES['images'];

    $uploaded = 0;
    $errors = 0;

    if (!empty($files['name'][0])) {
        // Prepare statement outside the loop for efficiency
        $stmt = $pdo->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
        
        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('img_', true) . '.' . strtolower($ext);

                if (move_uploaded_file($tmpName, $uploadFolder . $newName)) {
                    try {
                        $stmt->execute([$newName, $caption]);
                        $uploaded++;
                    } catch (PDOException $e) {
                        // DB error
                        $errors++;
                        // Optionally unlink the file if DB failed
                        if (file_exists($uploadFolder . $newName)) unlink($uploadFolder . $newName);
                    }
                } else {
                    // File move error
                    $errors++;
                }
            } else {
                // Upload error (file too big, etc.)
                $errors++;
            }
        }
        $msg = "Upload complete. **{$uploaded}** image(s) uploaded successfully.";
        if ($errors > 0) {
            $msg .= " **{$errors}** failed.";
        }
        set_flash($msg, $errors ? 'error' : 'success');
    } else {
        set_flash('No images selected for upload.', 'error');
    }

    header('Location: gallery.php');
    exit;
}

/* ---------- MULTIPLE DELETE ---------- */
if (isset($_POST['delete_selected']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $count = count($ids);

    try {
        // 1. Get image names
        $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id IN ($in)");
        $stmt->execute($ids);
        $imagesToDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2. Delete from folder
        foreach ($imagesToDelete as $img) {
            $filePath = $uploadFolder . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 3. Delete from DB
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id IN ($in)");
        $stmt->execute($ids);

        set_flash("Successfully deleted **{$count}** image(s).");
    } catch (PDOException $e) {
        set_flash("Database error: Could not delete selected images.", 'error');
    }

    header('Location: gallery.php');
    exit;
}

/* ---------- FETCH ALL IMAGES ---------- */
$images = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Custom style to make the checkbox stand out slightly more */
        .image-card:hover .checkbox-overlay {
            opacity: 1;
        }
        .checkbox-overlay {
            opacity: 0.8;
            transition: opacity 0.2s;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-pink-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-pink-600 mr-4">photo_library</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Photo Gallery Management</h1>
            </div>
            <p class="text-lg text-gray-500">Total Images: **<?= count($images) ?>**</p>
        </div>
        
        <?php if($flash): ?>
            <div class="p-4 mb-6 rounded-lg font-medium shadow-md <?= $flash['type']==='error'?'bg-red-100 text-red-700 border border-red-300':'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">

            <div class="xl:col-span-1 bg-white p-6 rounded-xl shadow-xl border-t-4 border-indigo-500 h-fit">
                <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                    <span class="material-icons text-2xl mr-2 text-indigo-500">cloud_upload</span>
                    Add New Images
                </h2>
                <p class="text-sm text-gray-500 mb-4">Select multiple images at once to upload to the gallery.</p>
                
                <form method="post" enctype="multipart/form-data" class="space-y-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select File(s)</label>
                        <input type="file" name="images[]" accept="image/*" multiple required 
                               class="w-full p-2 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Caption (Optional)</label>
                        <input type="text" name="caption" placeholder="Event name or short description" 
                               class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <button type="submit" name="add_images"
                            class="w-full bg-indigo-600 text-white font-semibold px-4 py-3 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md flex items-center justify-center">
                        <span class="material-icons mr-2">publish</span>
                        Upload Images
                    </button>
                </form>
            </div>

            <div class="xl:col-span-3">
                <form method="post">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="material-icons text-3xl mr-2 text-pink-600">view_module</span>
                            All Gallery Images
                        </h2>
                        <button type="submit" name="delete_selected"
                                onclick="return confirm('Are you sure you want to delete the selected images? This action cannot be undone.')"
                                class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-700 transition duration-150 shadow-md flex items-center">
                            <span class="material-icons text-lg mr-1">delete_forever</span>
                            Delete Selected
                        </button>
                    </div>

                    <?php if (empty($images)): ?>
                        <div class="bg-white p-6 rounded-xl shadow-lg text-center text-gray-500 border-l-4 border-yellow-400">
                            <span class="material-icons text-4xl text-yellow-400 mb-2">info</span><br>
                            The gallery is currently empty. Upload images using the form on the left.
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <?php foreach($images as $img): ?>
                                <div class="bg-white p-3 rounded-xl shadow-lg relative overflow-hidden image-card border border-gray-100">
                                    
                                    <label class="absolute top-3 right-3 z-10 cursor-pointer checkbox-overlay">
                                        <input type="checkbox" name="selected[]" value="<?= $img['id'] ?>" 
                                            class="w-6 h-6 rounded-full appearance-none border-2 border-white bg-gray-800/50 checked:bg-red-600 checked:border-red-800 transition duration-150">
                                        <span class="absolute top-1/2 left-1/2 -mt-2 -ml-2 text-white material-icons text-sm pointer-events-none">
                                            done
                                        </span>
                                    </label>
                                    
                                    <img src="../assets/uploads/gallery/<?= e($img['image']) ?>"
                                         alt="<?= e($img['caption']) ?>"
                                         class="w-full h-40 object-cover rounded-lg mb-3">
                                    
                                    <?php if($img['caption']): ?>
                                        <div class="text-xs font-medium text-gray-700 truncate text-center" title="<?= e($img['caption']) ?>">
                                            <?= e($img['caption']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-xs text-gray-400 text-center">No Caption</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

    </main>
</div>
</body>
</html>