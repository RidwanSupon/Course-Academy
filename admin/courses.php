<?php
// admin/courses.php
// Manage courses: add / edit / delete / toggle active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Ensure $pdo is available
global $pdo;

$errors = [];
$flash = get_flash(); 

// --- Data Fetching: Mentors List ---
// Fetch all mentor IDs and names for the new dropdown selection
$mentorsList = [];
try {
    $stmtMentors = $pdo->query("SELECT id, name FROM mentors ORDER BY name ASC");
    $mentorsList = $stmtMentors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In a real application, you'd log this error
}

// --- Form Submission Handling (Add and Update) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $long_desc = trim($_POST['long_desc'] ?? '');
    // === UPDATED: Get mentor_id instead of teacher name ===
    $mentor_id = intval($_POST['mentor_id'] ?? 0); 
    $gender = in_array($_POST['gender'] ?? 'Both', ['Male', 'Female', 'Both']) ? $_POST['gender'] : 'Both';
    $price = is_numeric($_POST['price'] ?? '') ? number_format((float)$_POST['price'], 2, '.', '') : '0.00';
    $duration = trim($_POST['duration'] ?? '');
    $active = !empty($_POST['active']) ? 1 : 0;

    if ($title === '') {
        $errors[] = "Course Title is required.";
    }

    // Set $teacher to the name of the selected mentor (optional, for backward compatibility/display)
    $teacher = 'N/A';
    foreach($mentorsList as $m) {
        if ($m['id'] == $mentor_id) {
            $teacher = $m['name'];
            break;
        }
    }


    if (empty($errors)) {
        try {
            if ($action === 'add') {
                // IMPORTANT: Insert 'teacher' as a fallback/redundant field, use 'mentor_id' for linking
                $stmt = $pdo->prepare("INSERT INTO courses (title, short_desc, long_desc, mentor_id, teacher, gender, price, duration, active) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$title, $short_desc, $long_desc, $mentor_id, $teacher, $gender, $price, $duration, $active]);
                set_flash("Course **{$title}** added successfully.");
            } elseif ($action === 'update' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE courses SET title=?, short_desc=?, long_desc=?, mentor_id=?, teacher=?, gender=?, price=?, duration=?, active=? WHERE id=?");
                $stmt->execute([$title, $short_desc, $long_desc, $mentor_id, $teacher, $gender, $price, $duration, $active, $id]);
                set_flash("Course **{$title}** updated successfully.");
            }
            // Redirect to the main page to prevent form resubmission
            header("Location: courses.php");
            exit;
        } catch (PDOException $e) {
             $errors[] = "Database error during save: " . $e->getMessage();
        }
    }
}

// --- Action Handling (Delete and Toggle) ---

// handle delete (GET)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
        set_flash("Course deleted successfully. (Related enrollments also deleted if applicable.)");
    } catch (PDOException $e) {
        set_flash("Database error: Could not delete course.", 'error');
    }
    header("Location: courses.php");
    exit;
}

// handle toggle active (GET)
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    try {
        $cur = $pdo->prepare("SELECT active FROM courses WHERE id=? LIMIT 1");
        $cur->execute([$id]);
        $row = $cur->fetch();
        if ($row) {
            $new = $row['active'] ? 0 : 1;
            $pdo->prepare("UPDATE courses SET active=? WHERE id=?")->execute([$new, $id]);
            set_flash("Course status updated to " . ($new ? 'Active' : 'Inactive') . ".");
        }
    } catch (PDOException $e) {
        set_flash("Database error: Could not toggle course status.", 'error');
    }
    header("Location: courses.php");
    exit;
}

// --- Data Fetching: Course for Edit ---

$edit_course = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $edit_course = $stmt->fetch();
    $flash = get_flash(); 
}

// --- Data Fetching: All Courses ---

// Fetch all courses, joining with a count of active enrollments
$courses = $pdo->query("
    SELECT 
        c.*, 
        COUNT(e.id) AS enrollment_count
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.status = 'Approved'
    GROUP BY c.id
    ORDER BY c.created_at DESC
")->fetchAll();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Manage Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex">
    <?php include __DIR__ . '/header_admin.php'; ?>
    
    <main class="flex-1 p-8">
        
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 flex items-center justify-between border-b-4 border-indigo-600">
            <div class="flex items-center">
                <span class="material-icons text-4xl text-indigo-600 mr-4">school</span>
                <h1 class="text-3xl font-extrabold text-gray-800">Course Management</h1>
            </div>
            <p class="text-lg text-gray-500">Total Courses: **<?= count($courses) ?>**</p>
        </div>
        
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
        <?php foreach($errors as $err): // Display direct form validation errors ?>
            <div class="mb-6 p-4 rounded-lg font-medium shadow-md bg-red-100 text-red-700 border border-red-300">
                <?= e($err) ?>
            </div>
        <?php endforeach; ?>

        <div class="flex flex-col xl:flex-row gap-8">
            
            <section class="xl:w-1/3 bg-white p-6 rounded-xl shadow-xl border-t-4 border-blue-900 h-fit">
                <h2 class="text-2xl font-bold mb-4 text-blue-900 flex items-center">
                    <span class="material-icons text-3xl mr-2 text-yellow-500"><?= $edit_course ? 'edit' : 'add_box' ?></span>
                    <?= $edit_course ? "Edit Course (#" . $edit_course['id'] . ")" : "Add New Course" ?>
                </h2>

                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="<?= $edit_course ? 'update' : 'add' ?>">
                    <?php if ($edit_course): ?>
                        <input type="hidden" name="id" value="<?= intval($edit_course['id']) ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="<?= e($edit_course['title'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                        <input type="text" name="short_desc" value="<?= e($edit_course['short_desc'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Long Description (Details)</label>
                        <textarea name="long_desc" class="w-full border border-gray-300 p-3 rounded-lg h-28 focus:border-indigo-500 focus:ring-indigo-500"><?= e($edit_course['long_desc'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teacher/Instructor (Mentor ID)</label>
                            <select name="mentor_id" class="w-full border border-gray-300 p-3 rounded-lg bg-white">
                                <option value="0">-- Select Mentor (Optional) --</option>
                                <?php 
                                $current_mentor_id = $edit_course['mentor_id'] ?? 0;
                                foreach ($mentorsList as $mentor): 
                                ?>
                                    <option value="<?= intval($mentor['id']) ?>" 
                                        <?= $current_mentor_id == $mentor['id'] ? 'selected' : '' ?>>
                                        <?= e($mentor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="teacher" value="<?= e($edit_course['teacher'] ?? '') ?>"> 
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender Focus</label>
                            <select name="gender" class="w-full border border-gray-300 p-3 rounded-lg bg-white">
                                <?php $curGender = $edit_course['gender'] ?? 'Both'; ?>
                                <option value="Both" <?= $curGender === 'Both' ? 'selected' : '' ?>>Both</option>
                                <option value="Male" <?= $curGender === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $curGender === 'Female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="text" name="price" value="<?= e($edit_course['price'] ?? '0.00') ?>" class="w-full border border-gray-300 p-3 rounded-lg" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (e.g., 3 months)</label>
                            <input type="text" name="duration" value="<?= e($edit_course['duration'] ?? '') ?>" class="w-full border border-gray-300 p-3 rounded-lg">
                        </div>
                    </div>

                    <label class="inline-flex items-center mt-2">
                        <input type="checkbox" name="active" value="1" <?= (!isset($edit_course) || $edit_course['active']) ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                        <span class="ml-2 text-sm font-medium text-blue-900">Set as Active (Visible on site)</span>
                    </label>

                    <div class="mt-6 flex justify-between items-center">
                        <button type="submit" class="bg-blue-900 text-white font-semibold px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md flex items-center">
                            <span class="material-icons mr-2"><?= $edit_course ? 'save' : 'add' ?></span>
                            <?= $edit_course ? 'Update Course' : 'Add Course' ?>
                        </button>
                        <?php if ($edit_course): ?>
                            <a href="courses.php" class="text-sm text-gray-600 hover:text-indigo-600 font-medium">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
                
            </section>

            <section class="xl:w-2/3 space-y-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="material-icons text-3xl mr-2 text-indigo-600">list_alt</span>
                    Existing Course List
                </h2>
                
                <?php if (empty($courses)): ?>
                    <div class="bg-white p-6 rounded-xl shadow-lg text-center text-gray-500 border-l-4 border-yellow-400">
                        <span class="material-icons text-4xl text-yellow-400 mb-2">info</span><br>
                        No courses have been added yet. Use the form to create one.
                    </div>
                <?php else: ?>
                    <?php foreach ($courses as $c): ?>
                        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 <?= $c['active'] ? 'border-green-500' : 'border-red-500' ?>">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="font-bold text-xl text-gray-900"><?= e($c['title']) ?></div>
                                    <div class="text-sm text-gray-600 mt-1"><?= e($c['short_desc']) ?></div>
                                </div>
                                
                                <a href="?toggle=<?= intval($c['id']) ?>" class="flex-shrink-0 cursor-pointer transition duration-150 p-1 rounded-full border 
                                    <?= $c['active'] ? 'bg-green-100 text-green-700 border-green-300 hover:bg-green-200' : 'bg-red-100 text-red-700 border-red-300 hover:bg-red-200' ?>">
                                    <span class="text-xs font-semibold px-2">
                                        <?= $c['active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700 border-t border-gray-100 pt-3">
                                <div><span class="font-medium">Price:</span> <span class="text-indigo-600 font-semibold">$<?= number_format((float)$c['price'], 2) ?></span></div>
                                <div><span class="font-medium">Duration:</span> <?= e($c['duration'] ?: '—') ?></div>
                                <div><span class="font-medium">Teacher:</span> <?= e($c['teacher'] ?: '—') ?></div> 
                                <div><span class="font-medium">Gender:</span> <?= e($c['gender']) ?></div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center space-x-3 text-sm">
                                <a href="courses.php?edit=<?= intval($c['id']) ?>" class="text-yellow-600 hover:text-yellow-700 flex items-center font-medium">
                                    <span class="material-icons text-lg mr-1">edit</span> Edit
                                </a>
                                <a href="enrollments.php?course_id=<?= intval($c['id']) ?>" class="text-blue-600 hover:text-blue-700 flex items-center font-medium">
                                    <span class="material-icons text-lg mr-1">person_add</span> Enrolled (<?= $c['enrollment_count'] ?>)
                                </a>
                                <a href="?delete=<?= intval($c['id']) ?>" 
                                    onclick="return confirm('WARNING: Delete course <?= e($c['title']) ?>? This action is irreversible and will delete all <?= $c['enrollment_count'] ?> related enrollments.');" 
                                    class="text-red-600 hover:text-red-700 flex items-center font-medium">
                                    <span class="material-icons text-lg mr-1">delete</span> Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>
</body>
</html>