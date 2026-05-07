<?php
session_start();
include("../config/db.php");

$id = $_GET['id'];

// Fetch post
$sql = "SELECT * FROM posts WHERE id='$id'";
$result = $conn->query($sql);
$post = $result->fetch_assoc();

// Permission check
if (!($post['user_id'] == $_SESSION['id'] || $_SESSION['role'] == 'admin')) {
    die("? You are not allowed to delete this post.");
}

// Delete post
$sql = "DELETE FROM posts WHERE id='$id'";
if ($conn->query($sql)) {
    header("Location: list_posts.php");
    exit();
} else {
    echo "Error deleting post: " . $conn->error;
}
?>
