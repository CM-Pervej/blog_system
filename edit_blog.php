<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) header("Location: login.php");
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) header("Location: dashboard.php");
$blog_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM blogs WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();
if (!$blog) die("Blog not found");

if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $short_desc = trim($_POST['short_description']);
    $long_desc = trim($_POST['long_description']);
    $is_publish = isset($_POST['is_publish']) ? 1 : 0;

    // Handle image
    $image = $blog['image'];
    if ($_FILES['image']['name']) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid().".".$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
    }

    $stmt = $conn->prepare("UPDATE blogs SET title=?, short_description=?, long_description=?, image=?, is_publish=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssiii", $title, $short_desc, $long_desc, $image, $is_publish, $blog_id, $user_id);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to update blog!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Blog</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded shadow-md w-full max-w-lg">
<h2 class="text-2xl font-bold mb-6">Edit Blog</h2>

<?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required class="input input-bordered w-full mb-4" value="<?php echo htmlspecialchars($blog['title']); ?>">
    <textarea name="short_description" placeholder="Short Description" required class="textarea textarea-bordered w-full mb-4"><?php echo htmlspecialchars($blog['short_description']); ?></textarea>
    <textarea name="long_description" placeholder="Long Description" required class="textarea textarea-bordered w-full mb-4"><?php echo htmlspecialchars($blog['long_description']); ?></textarea>
    <input type="file" name="image" class="mb-4">
    <?php if($blog['image']) echo "<img src='uploads/{$blog['image']}' class='mb-4 w-32'>"; ?>
    <label class="flex items-center mb-4">
        <input type="checkbox" name="is_publish" class="checkbox mr-2" <?php echo $blog['is_publish'] ? "checked" : ""; ?>> Publish
    </label>
    <button type="submit" name="update" class="btn btn-primary w-full">Update Blog</button>
</form>
<a href="dashboard.php" class="btn btn-ghost mt-4">Back to Dashboard</a>
</div>
</body>
</html>