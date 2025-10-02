<?php
// includes/functions.php
require_once __DIR__ . '/../config.php'; // Ensure $pdo is defined


/* ============================
   Admin Authentication
============================ */

/**
 * Check if admin is logged in
 * @return bool
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

/**
 * Require admin login to access a page
 * Redirects to login if not logged in
 */
function require_admin(): void {
    if (!is_admin_logged_in()) {
        header("Location: /admin/index.php");
        exit;
    }
}

/* ============================
   File Upload / Delete
============================ */

/**
 * Save an uploaded image to a destination folder
 * @param string $fileInput Name of the input field
 * @param string $destFolder Destination folder path
 * @param int $maxSize Maximum file size in bytes (default 5MB)
 * @param array $allowedTypes Optional array of allowed MIME types
 * @return array ['path' => filename] on success, ['error' => message] on failure
 */
function save_uploaded_image(string $fileInput, string $destFolder, int $maxSize = 5242880, array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']): array {
    if (!isset($_FILES[$fileInput])) {
        return ['error' => 'No file uploaded.'];
    }

    // Handle multiple files
    if (is_array($_FILES[$fileInput]['name'])) {
        $files = [];
        foreach ($_FILES[$fileInput]['name'] as $i => $name) {
            $tmp = $_FILES[$fileInput]['tmp_name'][$i];
            $size = $_FILES[$fileInput]['size'][$i];
            $error = $_FILES[$fileInput]['error'][$i];

            $file = ['name'=>$name, 'tmp_name'=>$tmp, 'size'=>$size, 'error'=>$error];
            $result = single_file_upload($file, $destFolder, $maxSize, $allowedTypes);
            if (isset($result['error'])) return ['error'=>$result['error']];
            $files[] = $result['path'];
        }
        return ['path' => $files];
    } else {
        return single_file_upload($_FILES[$fileInput], $destFolder, $maxSize, $allowedTypes);
    }
}

/**
 * Helper function for single file upload
 */
function single_file_upload(array $file, string $destFolder, int $maxSize, array $allowedTypes): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error.'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'File exceeds max size.'];
    }

    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Unsupported file type.'];
    }

    if (!is_dir($destFolder)) mkdir($destFolder, 0755, true);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('', true) . '.' . strtolower($extension);
    $destination = rtrim($destFolder, '/') . '/' . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['error' => 'Failed to move uploaded file.'];
    }

    return ['path' => $uniqueName];
}

/**
 * Delete an image file from a folder
 * @param string $folder Path to folder
 * @param string $filename File name to delete
 */
function delete_image(string $folder, string $filename): void {
    $filePath = rtrim($folder, '/') . '/' . $filename;
    if (file_exists($filePath)) unlink($filePath);
}

/* ============================
   Data Sanitization
============================ */

/**
 * Sanitize a string input for safe output
 * Use prepared statements for DB insertion (no need for escaping here)
 * @param string $data
 * @return string
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/* ============================
   Flash Messages
============================ */

/**
 * Set a flash message
 * @param string $message
 * @param string $type success|error|info
 */
function set_flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message'=>$message, 'type'=>$type];
}

/**
 * Get flash message and clear
 * @return array|null ['message'=>..., 'type'=>...]
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
