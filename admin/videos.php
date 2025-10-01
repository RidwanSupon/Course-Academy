<?php
// admin/videos.php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Handle add video
if (isset($_POST['add_video'])) {
    $title = trim($_POST['title'] ?? '');
    $url   = trim($_POST['url'] ?? '');
    $desc  = trim($_POST['description'] ?? '');

    if ($title === '' || $url === '') {
        set_flash('Title and URL are required.', 'error');
        header('Location: videos.php');
        exit;
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        set_flash('Invalid URL.', 'error');
        header('Location: videos.php');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO videos (title,url,description) VALUES (?,?,?)");
    $stmt->execute([$title,$url,$desc]);
    set_flash('Video added successfully.');
    header('Location: videos.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM videos WHERE id=?")->execute([$id]);
    set_flash('Video deleted successfully.');
    header('Location: videos.php');
    exit;
}

// Fetch all videos
$videos = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC")->fetchAll();
$flash = get_flash();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - Videos</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include __DIR__ . '/header_admin.php'; ?>

<main class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Manage Videos</h1>

    <!-- Flash message -->
    <?php if($flash): ?>
        <div class="mb-4 p-4 rounded <?= $flash['type'] === 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Add Video Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Video</h2>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Title</label>
                <input name="title" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Video URL</label>
                <input name="url" class="w-full border p-2 rounded" placeholder="YouTube/Vimeo URL" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>
            <button name="add_video" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Video</button>
        </form>
    </div>

    <!-- Videos List -->
    <div class="grid gap-4">
        <?php foreach($videos as $v): ?>
            <div class="bg-white p-4 rounded shadow">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="font-semibold"><?= e($v['title']) ?></div>
                        <div class="text-sm text-gray-600"><?= e($v['url']) ?></div>
                        <?php if($v['description']): ?>
                            <div class="text-sm text-gray-500 mt-1"><?= e($v['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="?delete=<?= $v['id'] ?>" onclick="return confirm('Delete this video?')" 
                           class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Delete</a>
                    </div>
                </div>
                <?php
                    // Embed video preview if YouTube/Vimeo
                    $embed = '';
                    if (strpos($v['url'], 'youtube.com') !== false || strpos($v['url'], 'youtu.be') !== false) {
                        // convert to embed URL
                        if (preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $v['url'], $m)) {
                            $embed = 'https://www.youtube.com/embed/' . $m[1];
                        }
                    } elseif (strpos($v['url'], 'vimeo.com') !== false) {
                        if (preg_match('/vimeo\.com\/(\d+)/', $v['url'], $m)) {
                            $embed = 'https://player.vimeo.com/video/' . $m[1];
                        }
                    }
                ?>
                <?php if($embed): ?>
                    <iframe class="w-full h-64 mt-2 rounded" src="<?= e($embed) ?>" frameborder="0" allowfullscreen></iframe>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>
