<?php
session_start();
require_once "db.php";

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex flex-col items-center justify-center">
    <h1 class="text-4xl font-bold mb-6">Welcome to the Blog System</h1>

    <div class="space-x-4">
        <a href="register.php" class="btn btn-primary">Register</a>
        <a href="login.php" class="btn btn-secondary">Login</a>
    </div>
</div>

</body>
</html>