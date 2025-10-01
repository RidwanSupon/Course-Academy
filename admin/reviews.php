<?php
// admin/reviews.php
require_once __DIR__ . '/../includes/functions.php';
require_admin(); // ensure admin is logged in

$uploadFolder = '../uploads/reviews/';

// Handle adding new review
if (isset($_POST['add_review'])) {
    $name   = sanitize($_POST['name']);
    $text   = sanitize($_POST['text']);
    $rating = intval($_POST['rating']);
    $photo  = '';

    if (!empty($_FILES['photo']['name'])) {
        $result = save_uploaded_image('photo', $uploadFolder);
        if (isset($result['error'])) {
            set_flash($result['error'], 'error');
            header('Location: reviews.php');
            exit;
        }
        $photo = sanitize($result['path']);
    }

    $stmt = $pdo->prepare("INSERT INTO reviews (name, text, rating, photo) VALUES (?,?,?,?)");
    $stmt->execute([$name, $text, $rating, $photo]);
    set_flash('Review added successfully.');
    header('Location: reviews.php');
    exit;
}

// Handle deleting a review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT photo FROM reviews WHERE id=?");
    $stmt->execute([$id]);
    $rev = $stmt->fetch();

    if ($rev && !empty($rev['photo'])) {
        delete_image($uploadFolder, $rev['photo']);
    }

    $pdo->prepare("DELETE FROM reviews WHERE id=?")->execute([$id]);
    set_flash('Review deleted successfully.');
    header('Location: reviews.php');
    exit;
}

// Fetch all reviews
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll();
$flash = get_flash();
?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Student Reviews</h1>

    <!-- Flash message -->
    <?php if ($flash): ?>
        <div class="p-4 mb-6 rounded <?= $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'; ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Add New Review -->
    <div class="bg-white shadow-md rounded p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Review</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" placeholder="Student Name" required
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <textarea name="text" placeholder="Review Text" required
                      class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            <input type="number" name="rating" placeholder="Rating (1-5)" min="1" max="5" required
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="file" name="photo" accept="image/*"
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" name="add_review"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Review</button>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
        <h2 class="text-xl font-semibold mb-4">All Reviews</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-center">#</th>
                    <th class="border p-2 text-center">Photo</th>
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Review</th>
                    <th class="border p-2 text-center">Rating</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($reviews as $rev): ?>
                <tr>
                    <td class="border p-2 text-center"><?= $i++ ?></td>
                    <td class="border p-2 text-center">
                        <?php if ($rev['photo'] && file_exists($uploadFolder . $rev['photo'])): ?>
                            <img src="<?= e($uploadFolder . $rev['photo']) ?>" 
                                 alt="Review Photo" class="h-16 w-16 rounded-full mx-auto object-cover shadow">
                        <?php else: ?>
                            <span class="text-gray-400">No Photo</span>
                        <?php endif; ?>
                    </td>
                    <td class="border p-2"><?= e($rev['name']) ?></td>
                    <td class="border p-2"><?= e($rev['text']) ?></td>
                    <td class="border p-2 text-center"><?= str_repeat('⭐️', intval($rev['rating'])) ?></td>
                    <td class="border p-2 text-center">
                        <a href="?delete=<?= $rev['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this review?')"
                           class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
