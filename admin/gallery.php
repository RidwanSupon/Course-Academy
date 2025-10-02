<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Folder for uploads
$uploadFolder = __DIR__ . '/../assets/uploads/gallery/';
if (!is_dir($uploadFolder)) {
    mkdir($uploadFolder, 0755, true);
}

/* ---------- MULTIPLE IMAGE UPLOAD ---------- */
if (isset($_POST['add_images'])) {
    $caption = trim($_POST['caption'] ?? '');
    $files = $_FILES['images'];

    $uploaded = 0;
    $errors = 0;

    if (!empty($files['name'][0])) {
        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('img_', true) . '.' . strtolower($ext);

                if (move_uploaded_file($tmpName, $uploadFolder . $newName)) {
                    $stmt = $pdo->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
                    $stmt->execute([$newName, $caption]);
                    $uploaded++;
                } else {
                    $errors++;
                }
            } else {
                $errors++;
            }
        }
        set_flash("Uploaded: $uploaded | Failed: $errors", $errors ? 'error' : 'success');
    } else {
        set_flash('No images selected.', 'error');
    }

    header('Location: gallery.php');
    exit;
}

/* ---------- MULTIPLE DELETE ---------- */
if (isset($_POST['delete_selected']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', array_fill(0, count($ids), '?'));

    // Get image names
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id IN ($in)");
    $stmt->execute($ids);
    $imagesToDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete from folder
    foreach ($imagesToDelete as $img) {
        $filePath = $uploadFolder . $img;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Delete from DB
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id IN ($in)");
    $stmt->execute($ids);

    set_flash(count($ids) . ' image(s) deleted successfully.');
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
  <title>Manage Gallery</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/header_admin.php'; ?>

<div class="p-6 max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Manage Photo Gallery</h1>

    <!-- Flash Message -->
    <?php if($flash): ?>
      <div class="p-3 mb-4 rounded <?= $flash['type']==='error'?'bg-red-200 text-red-800':'bg-green-200 text-green-800' ?>">
          <?= e($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Upload Form -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-3">Add New Images</h2>
        <form method="post" enctype="multipart/form-data" class="space-y-3">
            <input type="file" name="images[]" accept="image/*" multiple required class="w-full border rounded p-2">
            <input type="text" name="caption" placeholder="Enter caption (optional)" class="w-full border rounded p-2">
            <button type="submit" name="add_images"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Upload Images
            </button>
        </form>
    </div>

    <!-- Gallery with Multiple Delete -->
    <form method="post">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">All Images</h2>
            <button type="submit" name="delete_selected"
                    onclick="return confirm('Delete selected images?')"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Delete Selected
            </button>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
            <?php foreach($images as $img): ?>
                <div class="bg-white p-2 rounded shadow relative">
                    <input type="checkbox" name="selected[]" value="<?= $img['id'] ?>" 
                           class="absolute top-2 left-2 w-5 h-5 accent-red-600">
                    <img src="../assets/uploads/gallery/<?= e($img['image']) ?>"
                         alt="<?= e($img['caption']) ?>"
                         class="w-full h-40 object-cover rounded mb-2">
                    <?php if($img['caption']): ?>
                        <div class="text-sm text-gray-700 text-center"><?= e($img['caption']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>
</body>
</html>
