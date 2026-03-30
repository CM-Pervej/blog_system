<?php
session_start();
require_once "db.php";

// Redirect if no blog ID
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$blog_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT b.*, u.name FROM blogs b JOIN users u ON b.user_id=u.id WHERE b.id=?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    die("Blog not found");
}

// Fetch comments
$comments = $conn->query("SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id=u.id WHERE c.blog_id='$blog_id' ORDER BY c.created_at DESC");

// Handle new comment
if (isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment']);
    if(!empty($comment_text)){
        $stmt = $conn->prepare("INSERT INTO comments (blog_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $blog_id, $user_id, $comment_text);
        $stmt->execute();
    }
    header("Location: view_blog.php?id=$blog_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($blog['title']); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">

<!-- Topbar -->
<div class="navbar bg-base-100 shadow px-6 fixed top-0 left-0 w-full z-50">
  <div class="flex-1">
    <a class="btn btn-ghost normal-case text-xl">Blog SaaS</a>
  </div>
  <div class="flex-none">
    <?php if(isset($_SESSION['user_name'])): ?>
      <span class="mr-4">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="logout.php" class="btn btn-error btn-sm">Logout</a>
    <?php endif; ?>
  </div>
</div>

<div class="flex pt-16">

  <!-- Sidebar -->
  <div class="w-64 bg-white p-4 shadow h-screen fixed top-16 left-0">
    <ul class="menu">
      <li><a href="dashboard.php" class="font-bold">Dashboard</a></li>
      <li><a href="create_blog.php" class="font-semibold mt-2">Create Blog</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="flex-1 ml-64 p-6 space-y-6">

    <!-- Blog Card -->
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($blog['title']); ?></h1>
        <p class="text-sm text-gray-500 mb-4">
            By <?php echo htmlspecialchars($blog['name']); ?> | 
            <?php echo $blog['is_publish'] ? "Published" : "Unpublished"; ?>
        </p>
        <?php if($blog['image']): ?>
            <img src='uploads/<?php echo $blog['image']; ?>' 
                 class='mb-4 w-full h-96 object-cover rounded' 
                 alt='Blog Image'>
        <?php endif; ?>
        <p class="mb-6"><?php echo nl2br(htmlspecialchars($blog['long_description'])); ?></p>
    </div>

    <!-- Comments Section -->
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-semibold mb-4">Comments</h3>

        <?php if ($comments->num_rows > 0): ?>
            <div class="space-y-2 mb-4 max-h-80 overflow-y-auto">
            <?php while ($c = $comments->fetch_assoc()): ?>
                <div class="p-3 border rounded bg-gray-50">
                    <b><?php echo htmlspecialchars($c['name']); ?>:</b> <?php echo htmlspecialchars($c['comment']); ?>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mb-4">No comments yet.</p>
        <?php endif; ?>

        <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" class="space-y-2">
            <textarea name="comment" placeholder="Add a comment..." required class="textarea textarea-bordered w-full"></textarea>
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="btn btn-ghost mt-4">Back to Dashboard</a>
  </div>

</div>
</body>
</html>