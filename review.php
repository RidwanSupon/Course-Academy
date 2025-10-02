<?php
require_once 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    die("You must be logged in to give a review.");
}

// Check if student is approved for at least one course
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE email=? AND status='Approved' LIMIT 1");
$stmt->execute([$_SESSION['user_email']]);
$enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$enrollment){
    die("You can only give a review after your enrollment is approved.");
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_SESSION['user_name'];
    $country = trim($_POST['country']);
    $message = trim($_POST['message']);
    $rating = intval($_POST['rating'] ?? 5);
    
    $stmt = $pdo->prepare("INSERT INTO reviews (name, country, message, rating) VALUES (?,?,?,?)");
    $stmt->execute([$name, $country, $message, $rating]);
    
    $success = "Thank you! Your review has been submitted.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Give Review</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center">Submit Your Review</h2>
    
    <?php if(isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>"; ?>
    
    <form method="POST">
        <input type="text" name="country" placeholder="Your Country" required class="w-full p-2 border border-gray-300 rounded mb-4">
        <textarea name="message" placeholder="Write your review" required class="w-full p-2 border border-gray-300 rounded mb-4"></textarea>
        <label class="block mb-2 font-semibold">Rating:</label>
        <select name="rating" class="w-full p-2 border border-gray-300 rounded mb-4">
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Good</option>
            <option value="3">3 - Average</option>
            <option value="2">2 - Poor</option>
            <option value="1">1 - Very Poor</option>
        </select>
        <button type="submit" class="w-full bg-blue-800 text-white p-2 rounded font-bold hover:opacity-90 transition">Submit Review</button>
    </form>
</div>

</body>
</html>
