<?php
session_start();
require_once "db.php";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $name, $hashed_password, $user_type);
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_type'] = $user_type;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6">Login</h2>

    <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required class="input input-bordered w-full mb-4">
        <input type="password" name="password" placeholder="Password" required class="input input-bordered w-full mb-4">
        <button type="submit" name="login" class="btn btn-primary w-full">Login</button>
    </form>

    <p class="mt-4">Don't have an account? <a href="register.php" class="text-blue-500">Register</a></p>
</div>
</body>
</html>