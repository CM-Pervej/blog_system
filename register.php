<?php
session_start();
require_once "db.php";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_type = "user"; // default

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $password, $user_type);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_type'] = $user_type;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Registration failed!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6">Register</h2>

    <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Name" required class="input input-bordered w-full mb-4">
        <input type="email" name="email" placeholder="Email" required class="input input-bordered w-full mb-4">
        <input type="text" name="phone" placeholder="Phone" required class="input input-bordered w-full mb-4">
        <input type="password" name="password" placeholder="Password" required class="input input-bordered w-full mb-4">
        <button type="submit" name="register" class="btn btn-primary w-full">Register</button>
    </form>

    <p class="mt-4">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
</div>
</body>
</html>