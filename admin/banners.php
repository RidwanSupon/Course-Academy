<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$success = '';

// Upload folder
$destFolder = __DIR__ . '/../assets/uploads/banners';
if (!is_dir($destFolder)) mkdir($destFolder, 0755, true);

// Handle Add Banner
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $link = $_POST['link'] ?? '';
    $sort_order = intval($_POST['sort_order'] ?? 0);

    $res = save_uploaded_image('image', $destFolder);
    if (isset($res['error'])) {
        $errors[] = $res['error'];
    } else {
        $imageName = basename($res['path']); // Save only filename
        $stmt = $pdo->prepare("INSERT INTO banners (image,title,subtitle,link,sort_order) VALUES (?,?,?,?,?)");
        $stmt->execute([$imageName, $title, $subtitle, $link, $sort_order]);
        $success = "Banner added successfully.";
    }
}

// Handle Delete Banner
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image FROM banners WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['image']) {
        $file = $destFolder . '/' . $row['image'];
        if (file_exists($file)) unlink($file);
    }
    $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$id]);
    header("Location: banners.php");
    exit;
}

// Fetch all banners
$banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order DESC, id DESC")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Banners</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/header_admin.php'; ?>

<div class="p-6 max-w-5xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Add Banner</h2>

    <!-- Display Errors -->
    <?php foreach($errors as $err): ?>
        <div class="bg-red-100 text-red-700 p-2 mb-2 rounded"><?= e($err) ?></div>
    <?php endforeach; ?>

    <!-- Success Message -->
    <?php if($success): ?>
        <div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= e($success) ?></div>
    <?php endif; ?>

    <!-- Add Banner Form -->
    <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow mb-6">
        <input type="hidden" name="action" value="add">

        <label class="block mb-2 font-medium">Image (recommended 1640x800)</label>
        <input type="file" name="image" class="mb-3 w-full" accept="image/*" required>

        <label class="block mb-2 font-medium">Title</label>
        <input name="title" class="w-full border p-2 mb-3 rounded" />

        <label class="block mb-2 font-medium">Subtitle</label>
        <input name="subtitle" class="w-full border p-2 mb-3 rounded" />

        <label class="block mb-2 font-medium">Link (optional)</label>
        <input name="link" class="w-full border p-2 mb-3 rounded" />

        <label class="block mb-2 font-medium">Sort Order</label>
        <input name="sort_order" type="number" class="w-full border p-2 mb-4 rounded" value="0" />

        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Banner</button>
    </form>

    <!-- Existing Banners -->
    <h3 class="text-xl font-bold mb-4">Existing Banners</h3>
    <div class="grid md:grid-cols-2 gap-6">
        <?php if (!empty($banners)): ?>
            <?php foreach($banners as $b): ?>
                <div class="bg-white p-4 rounded shadow flex flex-col md:flex-row items-start md:items-center">
                    <img src="../assets/uploads/banners/<?= e($b['image']) ?>" 
                         class="w-full md:w-80 h-40 md:h-20 object-cover rounded mb-3 md:mb-0 md:mr-4" 
                         alt="<?= e($b['title']) ?>">
                    <div class="flex-1">
                        <div class="font-semibold text-lg"><?= e($b['title']) ?></div>
                        <div class="text-sm text-gray-600"><?= e($b['subtitle']) ?></div>
                        <div class="mt-2">
                            <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete banner?')" class="text-red-600 hover:underline">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-gray-500 col-span-2 text-center">No banners found.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
