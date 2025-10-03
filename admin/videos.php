<?php
// admin/videos.php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

global $pdo;

// --- Video URL Utility Functions ---
/**
 * Extracts the video embed URL from a standard YouTube or Vimeo link.
 * @param string $url The video URL.
 * @return string|null The embed URL or null if not recognized.
 */
function get_video_embed_url(string $url): ?string {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        // YouTube
        if (preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
    } elseif (strpos($url, 'vimeo.com') !== false) {
        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
    }
    return null;
}
// ------------------------------------


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
        set_flash('Invalid URL format.', 'error');
        header('Location: videos.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO videos (title, url, description) VALUES (?, ?, ?)");
        $stmt->execute([$title, $url, $desc]);
        set_flash('Video added successfully.');
    } catch (PDOException $e) {
        set_flash('Database error: Could not add video.', 'error');
    }
    
    header('Location: videos.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $pdo->prepare("DELETE FROM videos WHERE id=?")->execute([$id]);
        set_flash('Video deleted successfully.');
    } catch (PDOException $e) {
         set_flash('Database error: Could not delete video.', 'error');
    }
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">

<div class="flex">

    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="flex-1 p-8">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-500">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-indigo-600 mr-4">subscriptions</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Manage Videos</h1>
            </div>
            <p class="text-lg text-gray-500">Total Videos: <?= count($videos) ?></p>
        </div>

        <?php if($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-xl h-fit">
                <h2 class="text-xl font-bold mb-4 text-indigo-700 border-b pb-2">➕ Add New Video</h2>
                <form method="post" class="space-y-5">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Video Title</label>
                        <input name="title" id="title" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Video URL (YouTube/Vimeo)</label>
                        <input name="url" id="url" type="url" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., https://www.youtube.com/watch?v=..." required>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <button name="add_video" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-150 shadow-md">
                        <span class="material-icons align-middle text-lg mr-1">add_circle</span>
                        Save Video
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-xl overflow-x-auto">
                <h2 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">🎥 Current Videos</h2>
                
                <?php if (empty($videos)): ?>
                    <p class="text-center py-10 text-gray-500">No videos have been added yet.</p>
                <?php else: ?>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title & Link</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Preview</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($videos as $v): ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-4 whitespace-nowrap align-top max-w-xs">
                                <div class="font-semibold text-gray-900"><?= e($v['title']) ?></div>
                                <div class="text-sm text-indigo-600 truncate" title="<?= e($v['url']) ?>"><?= e(substr($v['url'], 0, 40)) ?>...</div>
                                <?php if($v['description']): ?>
                                    <div class="text-xs text-gray-500 mt-2 italic"><?= e($v['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 align-top">
                                <?php $embed = get_video_embed_url($v['url']); ?>
                                <?php if($embed): ?>
                                    <div class="w-48 h-28 rounded overflow-hidden shadow-lg border border-gray-200">
                                        <iframe class="w-full h-full" src="<?= e($embed) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                <?php else: ?>
                                    <span class="text-red-500 text-sm">Cannot embed.</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 whitespace-nowrap text-right align-top">
                                <a href="?delete=<?= $v['id'] ?>" onclick="return confirm('Are you sure you want to permanently delete this video: <?= e($v['title']) ?>?')" 
                                   class="inline-flex items-center bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 text-sm transition duration-150 font-medium shadow-md">
                                    <span class="material-icons text-base mr-1">delete</span> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>