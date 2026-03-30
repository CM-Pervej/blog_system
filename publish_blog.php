<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) header("Location: login.php");
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $blog_id = intval($_GET['id']);

    // Toggle publish
    $stmt = $conn->prepare("UPDATE blogs SET is_publish = NOT is_publish WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
}

header("Location: dashboard.php");
exit;