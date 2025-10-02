<?php
session_start();

// Destroy all admin session data
session_unset();
session_destroy();

// Redirect to admin login page
header("Location: index.php");
exit;
