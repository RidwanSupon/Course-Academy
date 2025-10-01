<?php
// admin/gallery.php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_admin(); // ensure admin is logged in

// Folder to store gallery images
$uploadFolder = '../uploads/gallery/';

// Handle image upload
if (isset($_POST['add_image'])) {
    $result = save_uploaded_image('image', $uploadFolder);
    if (isset($result['error'])) {
        set_flash($result['error'], 'error');
    } else {
        $filename = sanitize($result['path']);
        $stmt = $conn->prepare("INSERT INTO gallery (filename) VALUES (?)");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        set_flash('Image uploaded successfully.');
    }
    header('Location: gallery.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->prepare("SELECT filename FROM gallery WHERE id=?");
    $res->bind_param("i", $id);
    $res->execute();
    $result = $res->get_result();
    $img = $result->fetch_assoc();

    if ($img) {
        delete_image($uploadFolder, $img['filename']);
        $stmt = $conn->prepare("DELETE FROM gallery WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        set_flash('Image deleted successfully.');
    }

    header('Location: gallery.php');
    exit;
}


// Fetch all images
$images = mysqli_query($conn, "SELECT * FROM gallery ORDER BY created_at DESC");

$flash = get_flash();
?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Photo Gallery</h1>

    <!-- Flash message -->
    <?php if($flash): ?>
        <div class="p-4 mb-6 rounded <?php echo $flash['type'] == 'error' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Upload New Image -->
    <div class="bg-white shadow-md rounded p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Image</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="image" accept="image/*" required
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" name="add_image"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload Image</button>
        </form>
    </div>

    <!-- Gallery Table -->
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
        <h2 class="text-xl font-semibold mb-4">All Images</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-center">#</th>
                    <th class="border p-2 text-center">Image</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; while($img = mysqli_fetch_assoc($images)): ?>
                    <tr>
                        <td class="border p-2 text-center"><?php echo $i++; ?></td>
                        <td class="border p-2 text-center">
                            <img src="../uploads/gallery/<?php echo htmlspecialchars($img['filename']); ?>" 
                                 alt="Gallery Image" class="h-24 w-24 object-cover rounded shadow">
                        </td>
                        <td class="border p-2 text-center">
                            <a href="?delete=<?php echo $img['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this image?')"
                               class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
