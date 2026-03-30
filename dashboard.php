<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Stats
$total_my_blogs = $conn->query("SELECT COUNT(*) as cnt FROM blogs WHERE user_id='$user_id'")->fetch_assoc()['cnt'];
$my_published = $conn->query("SELECT COUNT(*) as cnt FROM blogs WHERE user_id='$user_id' AND is_publish=1")->fetch_assoc()['cnt'];
$my_drafts = $total_my_blogs - $my_published;
$total_comments = $conn->query("SELECT COUNT(*) as cnt FROM comments WHERE user_id='$user_id'")->fetch_assoc()['cnt'];

// My Blogs
$my_blogs = $conn->query("SELECT * FROM blogs WHERE user_id='$user_id' ORDER BY id DESC");

// All Published Blogs
$all_blogs = $conn->query("SELECT b.*, u.name FROM blogs b JOIN users u ON b.user_id=u.id WHERE b.is_publish=1 ORDER BY b.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SaaS Dashboard</title>
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
    <span class="mr-4">Hello, <?php echo htmlspecialchars($user_name); ?></span>
    <a href="logout.php" class="btn btn-error btn-sm">Logout</a>
  </div>
</div>

<div class="flex pt-16"> <!-- pt-16 to avoid topbar overlap -->

  <!-- Sidebar -->
  <div class="w-64 bg-white p-4 shadow h-screen fixed top-16 left-0">
    <ul class="menu">
      <li><a href="dashboard.php" class="font-bold">Dashboard</a></li>
      <li><a href="create_blog.php" class="font-semibold mt-2">Create Blog</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="flex-1 ml-64 p-6 space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="card bg-white shadow p-4">
        <div class="card-body">
          <h2 class="card-title">My Blogs</h2>
          <p class="text-2xl font-bold"><?php echo $total_my_blogs; ?></p>
        </div>
      </div>
      <div class="card bg-white shadow p-4">
        <div class="card-body">
          <h2 class="card-title">Published Blogs</h2>
          <p class="text-2xl font-bold"><?php echo $my_published; ?></p>
        </div>
      </div>
      <div class="card bg-white shadow p-4">
        <div class="card-body">
          <h2 class="card-title">Draft Blogs</h2>
          <p class="text-2xl font-bold"><?php echo $my_drafts; ?></p>
        </div>
      </div>
      <div class="card bg-white shadow p-4">
        <div class="card-body">
          <h2 class="card-title">My Comments</h2>
          <p class="text-2xl font-bold"><?php echo $total_comments; ?></p>
        </div>
      </div>
    </div>

    <!-- My Blogs Table -->
    <h2 class="text-2xl font-bold mt-6 mb-4">My Blogs</h2>
    <?php if($my_blogs->num_rows>0): ?>
      <table class="table w-full bg-white shadow rounded">
        <thead>
          <tr>
            <th>Title</th>
            <th>Short Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = $my_blogs->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['short_description']); ?></td>
            <td><?php echo $row['is_publish']?'Published':'Draft'; ?></td>
            <td>
              <a href="view_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info mr-1">View</a>
              <a href="edit_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning mr-1">Edit</a>
              <a href="publish_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><?php echo $row['is_publish']?'Unpublish':'Publish'; ?></a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No blogs yet. <a href="create_blog.php" class="text-blue-500">Create one</a></p>
    <?php endif; ?>

    <!-- All Published Blogs -->
    <h2 class="text-2xl font-bold mt-10 mb-4">All Published Blogs</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php while($b = $all_blogs->fetch_assoc()): ?>
        <div class="card bg-white shadow p-4">
          <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($b['title']); ?></h3>
          <p class="text-sm text-gray-500">By <?php echo htmlspecialchars($b['name']); ?></p>
          <?php if($b['image']): ?>
            <img src="uploads/<?php echo $b['image']; ?>" class="my-2 w-full h-48 object-cover rounded">
          <?php endif; ?>
          <p><?php echo htmlspecialchars($b['short_description']); ?></p>
          <a href="view_blog.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-info mt-2">View & Comment</a>
        </div>
      <?php endwhile; ?>
    </div>

  </div>
</div>
</body>
</html>