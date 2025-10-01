<?php
// includes/functions.php
require_once __DIR__ . '/../config.php';

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

/**
 * Save an uploaded image to a destination folder
 * @param string $fileInput Name of the input field
 * @param string $destFolder Destination folder path
 * @param int $maxSize Maximum file size in bytes (default 5MB)
 * @param array $allowedTypes Optional array of allowed MIME types
 * @return array ['path' => filename] on success, ['error' => message] on failure
 */
function save_uploaded_image(string $fileInput, string $destFolder, int $maxSize = 5242880, array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']): array {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No file uploaded or upload error.'];
    }

    $file = $_FILES[$fileInput];

    // Check file size
    if ($file['size'] > $maxSize) {
        return ['error' => 'File size exceeds the allowed limit.'];
    }

    // Check MIME type
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Unsupported file type.'];
    }

    // Ensure destination folder exists
    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0777, true);
    }

    // Generate unique file name
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('', true) . '.' . $extension;
    $destinationPath = rtrim($destFolder, '/') . '/' . $uniqueName;

    // Move the uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
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
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

/**
 * Sanitize a string input before DB insert
 * @param string $data
 * @return string
 */
function sanitize(string $data): string {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Set flash message
 * @param string $message
 * @param string $type success|error|info
 */
function set_flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

/**
 * Get flash message and clear
 * @return array|null ['message' => ..., 'type' => ...]
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
