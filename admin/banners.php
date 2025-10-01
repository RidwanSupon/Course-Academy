<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$success = '';

// folder
$destFolder = __DIR__ . '/../assets/uploads/banners';
if(!is_dir($destFolder)) mkdir($destFolder, 0755, true);

// Add banner
if(isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $link = $_POST['link'] ?? '';
    $sort_order = intval($_POST['sort_order'] ?? 0);

    $res = save_uploaded_image('image', $destFolder);
    if(isset($res['error'])) {
        $errors[] = $res['error'];
    } else {
        $imageName = $res['path'];
        $stmt = $pdo->prepare("INSERT INTO banners (image,title,subtitle,link,sort_order) VALUES (?,?,?,?,?)");
        $stmt->execute([$imageName, $title, $subtitle, $link, $sort_order]);
        $success = "Banner added.";
    }
}

// Delete
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image FROM banners WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if($row) {
        $file = __DIR__ . '/../assets/uploads/banners/' . $row['image'];
        if (file_exists($file)) @unlink($file);
    }
    $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$id]);
    header("Location: banners.php");
    exit;
}

$banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order DESC, id DESC")->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Manage Banners</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100">
<?php include __DIR__ . '/header_admin.php'; // you can create small admin header or reuse dashboard nav ?>
<div class="p-6 max-w-4xl mx-auto">
  <h2 class="text-xl font-bold mb-4">Add Banner</h2>
  <?php foreach($errors as $err): ?>
    <div class="bg-red-100 text-red-700 p-2 mb-2"><?= e($err) ?></div>
  <?php endforeach; ?>
  <?php if($success): ?>
    <div class="bg-green-100 text-green-700 p-2 mb-2"><?= e($success) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow mb-6">
    <input type="hidden" name="action" value="add">
    <label class="block mb-2">Image (recommended 1400x600)</label>
    <input type="file" name="image" class="mb-2" accept="image/*" required>
    <label class="block mb-2">Title</label>
    <input name="title" class="w-full border p-2 mb-2" />
    <label class="block mb-2">Subtitle</label>
    <input name="subtitle" class="w-full border p-2 mb-2" />
    <label class="block mb-2">Link (optional)</label>
    <input name="link" class="w-full border p-2 mb-2" />
    <label class="block mb-2">Sort Order</label>
    <input name="sort_order" type="number" class="w-full border p-2 mb-4" value="0" />
    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Add Banner</button>
  </form>

  <h3 class="text-lg font-bold mb-2">Existing Banners</h3>
  <div class="grid md:grid-cols-2 gap-4">
    <?php foreach($banners as $b): ?>
      <div class="bg-white p-4 rounded shadow flex items-center">
        <img src="/assets/uploads/banners/<?= e($b['image']) ?>" class="w-36 h-20 object-cover rounded mr-4">
        <div>
          <div class="font-semibold"><?= e($b['title']) ?></div>
          <div class="text-sm text-gray-600"><?= e($b['subtitle']) ?></div>
          <div class="mt-2">
            <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete banner?')" class="text-red-600 mr-3">Delete</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>

