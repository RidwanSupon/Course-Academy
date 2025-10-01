<?php
require_once __DIR__ . '/config.php';
if(session_status() === PHP_SESSION_NONE) session_start();

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: index.php");
    exit;
}

$course_id = intval($_POST['course_id']);
$name = trim($_POST['name']);
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$location = trim($_POST['location'] ?? '');
$password = $_POST['password'] ?? '';
$payment_method = $_POST['payment_method'];
$bkash_txn_id = $_POST['transaction_id'] ?? null;

$pdo->beginTransaction();

try {
    // If guest and password/email/phone provided, create account
    if(!isset($_SESSION['user_id'])){
        // Check if email or phone exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=? OR phone=? LIMIT 1");
        $stmt->execute([$email, $phone]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$existing && $password){ // create new user only if password provided
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,phone,password_hash) VALUES (?,?,?,?)");
            $stmt->execute([$name,$email,$phone,$password_hash]);
            $user_id = $pdo->lastInsertId();

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone;
            $_SESSION['user_location'] = $location;

        } else {
            // Existing user, fetch
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? OR phone=? LIMIT 1");
            $stmt->execute([$email,$phone]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_location'] = $location;
        }
    }

    // Use session info for enrollment to ensure correct data
    $name = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];
    $phone = $_SESSION['user_phone'];
    $location = $_SESSION['user_location'];

    // Insert enrollment
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id,name,email,phone,location,payment_method,bkash_txn_id) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$course_id,$name,$email,$phone,$location,$payment_method,$bkash_txn_id]);

    $pdo->commit();

    header("Location: course.php?id={$course_id}&enrolled=true");
    exit;

} catch(Exception $e){
    $pdo->rollBack();
    die("Error: ".$e->getMessage());
}
