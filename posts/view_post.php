<?php
session_start();
include("../config/db.php");

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}

// ? Handle new comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        die("Login required to comment.");
    }
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id, $_SESSION['user_id'], $comment);
        $stmt->execute();
    }
    header("Location: view_post.php?id=" . $id);
    exit();
}

// ? Handle delete comment
if (isset($_GET['delete_comment'])) {
    $comment_id = intval($_GET['delete_comment']);

    // Check ownership
    $check = $conn->prepare("SELECT user_id FROM comments WHERE id = ? AND post_id = ?");
    $check->bind_param("ii", $comment_id, $id);
    $check->execute();
    $resultCheck = $check->get_result();
    $commentData = $resultCheck->fetch_assoc();

    if ($commentData) {
        if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $commentData['user_id']) {
            $del = $conn->prepare("DELETE FROM comments WHERE id = ? AND post_id = ?");
            $del->bind_param("ii", $comment_id, $id);
            $del->execute();
        }
    }

    header("Location: view_post.php?id=" . $id . "&deleted=1");
    exit();
}

// ? Fetch comments
$comments = $conn->prepare("SELECT c.*, u.name, u.role 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.post_id = ? 
                            ORDER BY c.created_at ASC");
$comments->bind_param("i", $id);
$comments->execute();
$comments = $comments->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Post</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #0d6efd, #1e3c72); /* light + dark blue gradient */
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 6px 18px rgba(0,0,0,0.2);
        }
        h2 { color: #0d6efd; margin-bottom: 10px; }
        .meta { font-size: 13px; color: #555; margin-bottom: 15px; }
        .content { margin-bottom: 20px; line-height: 1.6; }
        .file-box {
            margin-top: 20px;
            padding: 15px;
            background: #f1f1f1;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            margin-right: 8px;
        }
        .btn-back { background: #6c757d; color: #fff; }
        .btn-download { background: #198754; color: #fff; }
        .btn-delete { background: #dc3545; color: #fff; font-size: 12px; padding: 5px 8px; }
        .btn:hover { opacity: 0.9; }
        iframe, img {
            margin-top: 10px;
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .comments { margin-top: 30px; }
        .comments h3 { color: #0d6efd; }
        .comment-box {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .comment-author { font-weight: bold; color: #1e3c72; }
        .comment-meta { font-size: 12px; color: #777; margin-top: 5px; }
        .comment-form textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            resize: vertical;
        }
        .comment-form button {
            margin-top: 10px;
            padding: 10px 15px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .comment-form button:hover {
            background: #0b5ed7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($post['title']); ?></h2>
        <div class="meta">
            By <?= htmlspecialchars($post['author']); ?> (<?= $post['role']; ?>) | <?= $post['created_at']; ?>
        </div>
        <div class="content">
            <?= nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if (!empty($post['file_path'])): ?>
            <div class="file-box">
                <strong>?? Attached File:</strong><br>
                <?php 
                $fileExt = strtolower(pathinfo($post['file_path'], PATHINFO_EXTENSION));
                if (in_array($fileExt, ['jpg','jpeg','png','gif'])) {
                    echo "<img src='../" . htmlspecialchars($post['file_path']) . "' alt='Image Preview'>";
                } elseif ($fileExt === 'pdf') {
                    echo "<iframe src='../" . htmlspecialchars($post['file_path']) . "' width='100%' height='500px'></iframe>";
                } else {
                    echo "File uploaded: " . basename($post['file_path']);
                }
                ?>
                <br><br>
                <a class="btn btn-download" href="../<?= $post['file_path']; ?>" download>? Download File</a>
            </div>
        <?php endif; ?>

        <!-- ? Comments Section -->
        <div class="comments">
            <h3>?? Comments</h3>
            <?php while ($c = $comments->fetch_assoc()): ?>
                <div class="comment-box">
                    <div class="comment-author"><?= htmlspecialchars($c['name']); ?> (<?= htmlspecialchars($c['role']); ?>)</div>
                    <div><?= nl2br(htmlspecialchars($c['comment'])); ?></div>
                    <div class="comment-meta">
                        <?= $c['created_at']; ?>
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $c['user_id']): ?>
                            | <a class="btn btn-delete" href="?id=<?= $id ?>&delete_comment=<?= $c['id'] ?>" onclick="return confirm('Delete this comment?')">?? Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Comment Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form class="comment-form" method="POST">
                    <textarea name="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p><em>You must <a href="../auth/login.php">login</a> to comment.</em></p>
            <?php endif; ?>
        </div>

        <br>
        <a class="btn btn-back" href="../<?= $_SESSION['role']; ?>/dashboard.php">? Back to Dashboard</a>
    </div>
</body>
</html>
