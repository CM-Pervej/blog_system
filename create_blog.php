<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['create'])) {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $short_desc = trim($_POST['short_description']);
    $long_desc = trim($_POST['long_description']);
    $is_publish = isset($_POST['is_publish']) ? 1 : 0;

    // Handle image upload
    $image = null;
    if ($_FILES['image']['name']) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid().".".$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
    }

    $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, short_description, long_description, image, is_publish) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $title, $short_desc, $long_desc, $image, $is_publish);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to create blog!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Blog</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded shadow-md w-full max-w-lg">
<h2 class="text-2xl font-bold mb-6">Create Blog</h2>

<?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required class="input input-bordered w-full mb-4">
    <textarea name="short_description" placeholder="Short Description" required class="textarea textarea-bordered w-full mb-4"></textarea>
    <textarea name="long_description" placeholder="Long Description" required class="textarea textarea-bordered w-full mb-4"></textarea>
    <input type="file" name="image" class="mb-4">
    <label class="flex items-center mb-4">
        <input type="checkbox" name="is_publish" class="checkbox mr-2"> Publish now
    </label>
    <button type="submit" name="create" class="btn btn-primary w-full">Create Blog</button>
</form>
<a href="dashboard.php" class="btn btn-ghost mt-4">Back to Dashboard</a>
</div>
</body>
</html>