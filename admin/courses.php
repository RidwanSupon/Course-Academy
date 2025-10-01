<?php
// admin/courses.php
// Manage courses: add / edit / delete / toggle active
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$success = '';

// handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $long_desc = trim($_POST['long_desc'] ?? '');
    $teacher = trim($_POST['teacher'] ?? '');
    $gender = in_array($_POST['gender'] ?? 'Both', ['Male', 'Female', 'Both']) ? $_POST['gender'] : 'Both';
    $price = is_numeric($_POST['price'] ?? '') ? number_format((float)$_POST['price'], 2, '.', '') : '0.00';
    $duration = trim($_POST['duration'] ?? '');
    $active = !empty($_POST['active']) ? 1 : 0;

    if ($title === '') {
        $errors[] = "Title is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, short_desc, long_desc, teacher, gender, price, duration, active) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $short_desc, $long_desc, $teacher, $gender, $price, $duration, $active]);
        $success = "Course added successfully.";
        // redirect to avoid form resubmission
        header("Location: courses.php?msg=added");
        exit;
    }
}

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $long_desc = trim($_POST['long_desc'] ?? '');
    $teacher = trim($_POST['teacher'] ?? '');
    $gender = in_array($_POST['gender'] ?? 'Both', ['Male', 'Female', 'Both']) ? $_POST['gender'] : 'Both';
    $price = is_numeric($_POST['price'] ?? '') ? number_format((float)$_POST['price'], 2, '.', '') : '0.00';
    $duration = trim($_POST['duration'] ?? '');
    $active = !empty($_POST['active']) ? 1 : 0;

    if ($title === '') {
        $errors[] = "Title is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE courses SET title=?, short_desc=?, long_desc=?, teacher=?, gender=?, price=?, duration=?, active=? WHERE id=?");
        $stmt->execute([$title, $short_desc, $long_desc, $teacher, $gender, $price, $duration, $active, $id]);
        $success = "Course updated successfully.";
        header("Location: courses.php?msg=updated");
        exit;
    }
}

// handle delete (GET)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // warn: enrollments have FK ON DELETE CASCADE so related enrollments will be removed automatically
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
    header("Location: courses.php?msg=deleted");
    exit;
}

// handle toggle active (GET)
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $cur = $pdo->prepare("SELECT active FROM courses WHERE id=? LIMIT 1");
    $cur->execute([$id]);
    $row = $cur->fetch();
    if ($row) {
        $new = $row['active'] ? 0 : 1;
        $pdo->prepare("UPDATE courses SET active=? WHERE id=?")->execute([$new, $id]);
    }
    header("Location: courses.php");
    exit;
}

// fetch course for edit if requested
$edit_course = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $edit_course = $stmt->fetch();
}

// fetch all courses
$courses = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php
    // include a small admin header/navigation (create admin/header_admin.php later)
    if (file_exists(__DIR__ . '/header_admin.php')) {
        include __DIR__ . '/header_admin.php';
    } else {
        // Minimal inline header if header_admin.php not yet created
        ?>
        <header class="bg-white shadow">
            <div class="max-w-6xl mx-auto p-4 flex justify-between items-center">
                <h1 class="text-lg font-bold">Admin Panel</h1>
                <nav class="space-x-3">
                    <a href="dashboard.php" class="text-sm text-gray-600 hover:underline">Dashboard</a>
                    <a href="banners.php" class="text-sm text-gray-600 hover:underline">Banners</a>
                    <a href="courses.php" class="text-sm font-semibold text-indigo-700">Courses</a>
                    <a href="videos.php" class="text-sm text-gray-600 hover:underline">Videos</a>
                    <a href="gallery.php" class="text-sm text-gray-600 hover:underline">Gallery</a>
                    <a href="enrollments.php" class="text-sm text-gray-600 hover:underline">Enrollments</a>
                    <a href="reviews.php" class="text-sm text-gray-600 hover:underline">Reviews</a>
                    <a href="logout.php" class="text-sm text-red-600 hover:underline">Logout</a>
                </nav>
            </div>
        </header>
        <?php
    }
    ?>

    <main class="max-w-6xl mx-auto p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Add / Edit form -->
            <section class="md:w-1/3 bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">
                    <?= $edit_course ? "Edit Course" : "Add Course" ?>
                </h2>

                <?php if (!empty($errors)): ?>
                    <div class="mb-4">
                        <?php foreach ($errors as $err): ?>
                            <div class="bg-red-50 text-red-700 p-2 rounded mb-2"><?= e($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_GET['msg'])): ?>
                    <div class="mb-4 bg-green-50 text-green-700 p-2 rounded">
                        <?= e($_GET['msg'] === 'added' ? 'Course added.' : ($_GET['msg'] === 'updated' ? 'Course updated.' : ($_GET['msg'] === 'deleted' ? 'Course deleted.' : ''))) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="space-y-3">
                    <input type="hidden" name="action" value="<?= $edit_course ? 'update' : 'add' ?>">
                    <?php if ($edit_course): ?>
                        <input type="hidden" name="id" value="<?= intval($edit_course['id']) ?>">
                    <?php endif; ?>

                    <label class="block text-sm font-medium">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?= e($edit_course['title'] ?? '') ?>" class="w-full border p-2 rounded" required>

                    <label class="block text-sm font-medium">Short Description</label>
                    <input type="text" name="short_desc" value="<?= e($edit_course['short_desc'] ?? '') ?>" class="w-full border p-2 rounded">

                    <label class="block text-sm font-medium">Long Description</label>
                    <textarea name="long_desc" class="w-full border p-2 rounded h-28"><?= e($edit_course['long_desc'] ?? '') ?></textarea>

                    <label class="block text-sm font-medium">Teacher</label>
                    <input type="text" name="teacher" value="<?= e($edit_course['teacher'] ?? '') ?>" class="w-full border p-2 rounded">

                    <label class="block text-sm font-medium">Gender</label>
                    <select name="gender" class="w-full border p-2 rounded">
                        <?php $curGender = $edit_course['gender'] ?? 'Both'; ?>
                        <option value="Both" <?= $curGender === 'Both' ? 'selected' : '' ?>>Both</option>
                        <option value="Male" <?= $curGender === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $curGender === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>

                    <label class="block text-sm font-medium">Price (optional)</label>
                    <input type="text" name="price" value="<?= e($edit_course['price'] ?? '0.00') ?>" class="w-full border p-2 rounded" placeholder="0.00">

                    <label class="block text-sm font-medium">Duration (e.g., 3 months)</label>
                    <input type="text" name="duration" value="<?= e($edit_course['duration'] ?? '') ?>" class="w-full border p-2 rounded">

                    <label class="inline-flex items-center mt-2">
                        <input type="checkbox" name="active" value="1" <?= (!isset($edit_course) || $edit_course['active']) ? 'checked' : '' ?> class="form-checkbox">
                        <span class="ml-2 text-sm">Active</span>
                    </label>

                    <div class="mt-4">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                            <?= $edit_course ? 'Update Course' : 'Add Course' ?>
                        </button>
                        <?php if ($edit_course): ?>
                            <a href="courses.php" class="ml-3 text-sm text-gray-600 hover:underline">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <!-- Courses list -->
            <section class="md:w-2/3">
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Courses</h2>
                        <div class="text-sm text-gray-500"><?= count($courses) ?> courses</div>
                    </div>

                    <?php if (empty($courses)): ?>
                        <div class="p-4 text-gray-600">No courses found. Add one using the form.</div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($courses as $c): ?>
                                <div class="border rounded p-3 flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <div class="font-semibold text-lg"><?= e($c['title']) ?></div>
                                        <div class="text-sm text-gray-600"><?= e($c['short_desc']) ?></div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Teacher: <?= e($c['teacher'] ?: '—') ?> · Duration: <?= e($c['duration'] ?: '—') ?> · Price: <?= number_format((float)$c['price'], 2) ?>
                                        </div>
                                    </div>

                                    <div class="mt-3 md:mt-0 flex items-center space-x-2">
                                        <a href="courses.php?edit=<?= intval($c['id']) ?>" class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded text-sm hover:shadow">Edit</a>

                                        <a href="?toggle=<?= intval($c['id']) ?>" class="px-3 py-1 rounded text-sm border
                                            <?= $c['active'] ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-700 border-gray-200' ?>">
                                            <?= $c['active'] ? 'Active' : 'Inactive' ?>
                                        </a>

                                        <a href="?delete=<?= intval($c['id']) ?>" onclick="return confirm('Delete this course and all related enrollments?')" class="px-3 py-1 bg-red-50 text-red-700 rounded text-sm">Delete</a>

                                        <a href="enrollments.php?course_id=<?= intval($c['id']) ?>" class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-sm">View Enrollments</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>