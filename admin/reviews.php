<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

global $pdo; // Ensure $pdo is globally accessible if needed by functions.php

// Server folder path for review images
$uploadFolder = __DIR__ . '/../assets/uploads/reviews/';
// Web-accessible URL for review images
$uploadUrl = '/basit/assets/uploads/reviews/';

// Create folder if it doesn't exist
if (!is_dir($uploadFolder)) {
    // Attempt to create directory recursively with safe permissions
    if (!mkdir($uploadFolder, 0755, true) && !is_dir($uploadFolder)) {
        // Handle error if directory creation fails
        set_flash('Error: Could not create upload directory.', 'error');
        header('Location: reviews.php');
        exit;
    }
}

/* ---------- MULTIPLE IMAGE UPLOAD ---------- */
if (isset($_POST['add_reviews'])) {
    $names    = $_POST['name'] ?? [];
    $messages = $_POST['message'] ?? [];
    $ratings  = $_POST['rating'] ?? [];
    $files    = $_FILES['photo'];

    $uploaded = 0;
    $errors   = 0;

    foreach ($names as $i => $name) {
        // Skip if name is empty (occurs if fields were added but left blank)
        if (trim($name) === '') continue;

        $name    = sanitize($name);
        $message = sanitize($messages[$i] ?? '');
        $rating  = max(1, min(5, intval($ratings[$i] ?? 5))); // Ensure rating is between 1 and 5
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

        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (name, message, rating, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $message, $rating, $photo]);
            $uploaded++;
        } catch (PDOException $e) {
            // Log database error if necessary, but just count as an error for user
            $errors++;
        }
    }

    $messageText = "Uploaded: $uploaded reviews.";
    if ($errors > 0) {
        $messageText .= " Failed to upload/process: $errors item(s).";
    }

    set_flash($messageText, $errors ? 'error' : 'success');
    header('Location: reviews.php');
    exit;
}

/* ---------- MULTIPLE DELETE ---------- */
if (isset($_POST['delete_selected']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', array_fill(0, count($ids), '?'));

    try {
        // Get images to delete
        $stmt = $pdo->prepare("SELECT image FROM reviews WHERE id IN ($in)");
        $stmt->execute($ids);
        $imagesToDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete physical files
        foreach ($imagesToDelete as $img) {
            if ($img && file_exists($uploadFolder . $img)) {
                unlink($uploadFolder . $img);
            }
        }

        // Delete DB entries
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id IN ($in)");
        $stmt->execute($ids);

        set_flash(count($ids) . ' review(s) deleted successfully.');
    } catch (PDOException $e) {
        set_flash('Database error during deletion.', 'error');
    }

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
    <title>Manage Reviews | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .star-rating {
            font-size: 1.25rem;
            line-height: 1;
        }
        .review-item {
            position: relative;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="flex">

    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-600">
            <div class="flex items-center">
                <h1 class="text-3xl font-extrabold text-gray-800">Manage Student Reviews</h1>
            </div>
            <p class="text-lg text-gray-500">Total Reviews: <?= count($reviews) ?></p>
        </div>

        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-xl rounded-lg p-6 mb-8 border-t-4 border-indigo-500">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">Add New Reviews</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div id="review-container" class="space-y-4">
                    <div class="review-item p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-inner">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <input type="text" name="name[]" placeholder="Student Name" required class="p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 col-span-2">
                            <input type="number" name="rating[]" placeholder="Rating (1-5)" min="1" max="5" value="5" class="p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="file" name="photo[]" accept="image/*" class="p-3 border border-gray-300 rounded-lg bg-white">
                        </div>
                        <div class="mt-3 relative">
                            <textarea name="message[]" placeholder="Review Message" required rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            <button type="button" class="remove-review hidden absolute top-1 right-1 text-red-500 hover:text-red-700 p-1 rounded-full bg-white transition duration-150">
                                <span class="material-icons text-lg">close</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="button" id="add-more" class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-700 transition duration-150 shadow-md">
                        <span class="material-icons text-xl mr-1">add</span>
                        Add Another Review
                    </button>
                    <button type="submit" name="add_reviews" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition duration-150 shadow-md">
                        <span class="material-icons text-xl mr-1">cloud_upload</span>
                        Upload All Reviews
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($reviews)): ?>
            <form method="POST">
                <div class="flex justify-between items-center mb-6 p-4 bg-white rounded-lg shadow-md border-l-4 border-red-500">
                    <h2 class="text-2xl font-bold text-gray-700">📚 All Existing Reviews</h2>
                    <button type="submit" name="delete_selected" onclick="return confirm('WARNING: You are about to permanently delete the selected reviews and their associated images. Continue?')" class="inline-flex items-center bg-red-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-red-700 transition duration-150 shadow-lg">
                        <span class="material-icons text-xl mr-1">delete_sweep</span>
                        Delete Selected
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach($reviews as $rev): ?>
                        <div class="bg-white p-4 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 review-item border-t-4 border-indigo-400">
                            <div class="relative mb-3">
                                <input type="checkbox" name="selected[]" value="<?= $rev['id'] ?>" class="absolute top-2 left-2 w-5 h-5 accent-red-600 z-10 rounded">
                                <?php 
                                    $imagePath = $uploadFolder . $rev['image'];
                                    $imageUrl = $uploadUrl . e($rev['image']);
                                ?>
                                <?php if ($rev['image'] && file_exists($imagePath)): ?>
                                    <img src="<?= $imageUrl ?>" class="w-full h-40 object-cover rounded-lg shadow-inner" alt="<?= e($rev['name']) ?>'s Photo">
                                <?php else: ?>
                                    <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-500 rounded-lg">No Image</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-lg text-gray-800"><?= e($rev['name']) ?></div>
                                <div class="star-rating text-yellow-500">
                                    <?= str_repeat('★', intval($rev['rating'])) ?>
                                    <span class="text-gray-400"><?= str_repeat('★', 5 - intval($rev['rating'])) ?></span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 italic line-clamp-3">"<?= e($rev['message']) ?>"</p>
                            <p class="text-xs text-gray-400 mt-2">Posted: <?= date('M d, Y', strtotime($rev['created_at'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php else: ?>
            <div class="bg-white p-10 rounded-lg shadow-xl text-center">
                <p class="text-2xl text-gray-500">No reviews found. Start by adding one above! 🚀</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<script>
    // Add multiple review forms dynamically
    document.getElementById('add-more').addEventListener('click', function() {
        const container = document.getElementById('review-container');
        const div = document.createElement('div');
        div.classList.add('review-item', 'p-4', 'border', 'border-gray-200', 'rounded-lg', 'bg-gray-50', 'shadow-inner', 'mt-4');
        div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="name[]" placeholder="Student Name" required class="p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 col-span-2">
                <input type="number" name="rating[]" placeholder="Rating (1-5)" min="1" max="5" value="5" class="p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <input type="file" name="photo[]" accept="image/*" class="p-3 border border-gray-300 rounded-lg bg-white">
            </div>
            <div class="mt-3 relative">
                <textarea name="message[]" placeholder="Review Message" required rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                <button type="button" class="remove-review absolute top-1 right-1 text-red-500 hover:text-red-700 p-1 rounded-full bg-white transition duration-150">
                    <span class="material-icons text-lg">close</span>
                </button>
            </div>
        `;
        container.appendChild(div);
        
        // Attach event listener to the new remove button
        div.querySelector('.remove-review').addEventListener('click', function() {
            div.remove();
        });
    });

    // Attach listener to initial item's remove button (if you want to allow removal of the first)
    // For this professional example, I will keep the first item mandatory and not add a remove button to it
    document.querySelectorAll('.review-item .remove-review').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.review-item').remove();
        });
    });
</script>
</body>
</html>