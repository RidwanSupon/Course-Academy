<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Server folder path for review images
$uploadFolder = __DIR__ . '/../assets/uploads/reviews/';
// Web-accessible URL for review images
$uploadUrl = '/basit/assets/uploads/reviews/';

if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0755, true);

/* ---------- MULTIPLE IMAGE UPLOAD ---------- */
if (isset($_POST['add_reviews'])) {
    $names    = $_POST['name'] ?? [];
    $messages = $_POST['message'] ?? [];
    $ratings  = $_POST['rating'] ?? [];
    $files    = $_FILES['photo'];

    $uploaded = 0;
    $errors   = 0;

    foreach ($names as $i => $name) {
        $name    = sanitize($name);
        $message = sanitize($messages[$i] ?? '');
        $rating  = intval($ratings[$i] ?? 5);
        $photo   = '';

        if (!empty($files['name'][$i])) {
            $tmpName = $files['tmp_name'][$i];
            $ext     = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $newName = uniqid('rev_', true) . '.' . strtolower($ext);

            if (move_uploaded_file($tmpName, $uploadFolder . $newName)) {
                $photo = $newName;
            } else {
                $errors++;
                continue;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO reviews (name, message, rating, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $message, $rating, $photo]);
        $uploaded++;
    }

    set_flash("Uploaded: $uploaded | Failed: $errors", $errors ? 'error' : 'success');
    header('Location: reviews.php');
    exit;
}

/* ---------- MULTIPLE DELETE ---------- */
if (isset($_POST['delete_selected']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', array_fill(0, count($ids), '?'));

    // Get images to delete
    $stmt = $pdo->prepare("SELECT image FROM reviews WHERE id IN ($in)");
    $stmt->execute($ids);
    $imagesToDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($imagesToDelete as $img) {
        if ($img && file_exists($uploadFolder . $img)) unlink($uploadFolder . $img);
    }

    // Delete DB entries
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id IN ($in)");
    $stmt->execute($ids);

    set_flash(count($ids) . ' review(s) deleted successfully.');
    header('Location: reviews.php');
    exit;
}

/* ---------- FETCH ALL REVIEWS ---------- */
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$flash   = get_flash();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Reviews</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<?php include __DIR__ . '/header_admin.php'; ?>

<div class="container mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">Manage Student Reviews</h1>

    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?= $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'; ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Add New Reviews -->
    <div class="bg-white shadow-md rounded p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Reviews</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div id="review-container">
                <div class="review-item mb-4 border p-3 rounded">
                    <input type="text" name="name[]" placeholder="Student Name" required class="w-full p-2 border rounded mb-2">
                    <textarea name="message[]" placeholder="Review Message" required class="w-full p-2 border rounded mb-2"></textarea>
                    <input type="number" name="rating[]" placeholder="Rating (1-5)" min="1" max="5" value="5" class="w-full p-2 border rounded mb-2">
                    <input type="file" name="photo[]" accept="image/*" class="w-full mb-2">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" id="add-more" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Add Another Review</button>
                <button type="submit" name="add_reviews" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload Reviews</button>
            </div>
        </form>
    </div>

    <!-- Reviews Table -->
    <form method="POST">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">All Reviews</h2>
            <button type="submit" name="delete_selected" onclick="return confirm('Delete selected reviews?')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Delete Selected
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach($reviews as $rev): ?>
                <div class="bg-white p-3 rounded shadow relative">
                    <input type="checkbox" name="selected[]" value="<?= $rev['id'] ?>" class="absolute top-2 left-2 w-5 h-5 accent-red-600">
                    <?php if ($rev['image'] && file_exists($uploadFolder . $rev['image'])): ?>
                        <img src="<?= $uploadUrl . e($rev['image']) ?>" class="w-full h-40 object-cover rounded mb-2" alt="<?= e($rev['name']) ?>">
                    <?php endif; ?>
                    <div class="font-semibold"><?= e($rev['name']) ?></div>
                    <div class="text-sm text-gray-700"><?= e($rev['message']) ?></div>
                    <div class="text-yellow-500"><?= str_repeat('⭐️', intval($rev['rating'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<script>
// Add multiple review forms dynamically
document.getElementById('add-more').addEventListener('click', function() {
    const container = document.getElementById('review-container');
    const div = document.createElement('div');
    div.classList.add('review-item', 'mb-4', 'border', 'p-3', 'rounded');
    div.innerHTML = `
        <input type="text" name="name[]" placeholder="Student Name" required class="w-full p-2 border rounded mb-2">
        <textarea name="message[]" placeholder="Review Message" required class="w-full p-2 border rounded mb-2"></textarea>
        <input type="number" name="rating[]" placeholder="Rating (1-5)" min="1" max="5" value="5" class="w-full p-2 border rounded mb-2">
        <input type="file" name="photo[]" accept="image/*" class="w-full mb-2">
    `;
    container.appendChild(div);
});
</script>
</body>
</html>
