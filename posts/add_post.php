<?php
// show errors (useful while developing)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include("../config/db.php");

// -- Access check: only alumni or admin --
$roleSession = $_SESSION['role'] ?? null;
if (!$roleSession || !in_array($roleSession, ['alumni','admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

// get user id and name from session
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
$name   = $_SESSION['name'] ?? $_SESSION['username'] ?? null;

if (empty($name) && $user_id && isset($conn)) {
    $uStmt = $conn->prepare("SELECT name, username FROM users WHERE id = ? LIMIT 1");
    if ($uStmt) {
        $uStmt->bind_param("i", $user_id);
        $uStmt->execute();
        $uRes = $uStmt->get_result();
        if ($uRes && $row = $uRes->fetch_assoc()) {
            $name = $row['name'] ?? $row['username'] ?? null;
        }
        $uStmt->close();
    }
}

$message = "";

// handle post submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author  = $name ?? 'Unknown';
    $role    = $roleSession;

    if ($title === '') {
        $message = "Title is required.";
    } else {
        $file_path = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $message = "File upload error.";
            } else {
                $uploads_dir = __DIR__ . "/../uploads/";
                if (!is_dir($uploads_dir)) {
                    mkdir($uploads_dir, 0777, true);
                }
                $allowed = ['jpg','jpeg','png','gif','pdf','doc','docx','ppt','pptx','txt','zip','mp4'];
                $originalName = basename($_FILES['file']['name']);
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $maxSize = 10 * 1024 * 1024;

                if (!in_array($ext, $allowed)) {
                    $message = "Invalid file type.";
                } elseif ($_FILES['file']['size'] > $maxSize) {
                    $message = "File too large.";
                } else {
                    $filename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalName);
                    $target   = $uploads_dir . $filename;
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                        $file_path = "uploads/" . $filename;
                    } else {
                        $message = "Failed to move uploaded file.";
                    }
                }
            }
        }

        if ($message === "") {
            $sql = "INSERT INTO posts (title, content, author, role, file_path, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $title, $content, $author, $role, $file_path);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: ../$role/dashboard.php");
                exit();
            } else {
                $message = "Insert failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Create Post — Alumni Portal</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
    :root{--accent:#0d6efd;--accent-dark:#0056b3;}
    body{
        font-family:Segoe UI, Arial, sans-serif;
        margin:0; padding:32px;
        background: linear-gradient(135deg, #0d6efd, #1e3c72); /* light + dark blue */
        min-height:100vh;
    }
    .wrap{max-width:720px;margin:0 auto;}
    .card{
        background:#fff;
        border-radius:12px;
        padding:28px;
        box-shadow:0 6px 20px rgba(0,0,0,0.15);
    }
    h1{margin:0 0 12px;color:var(--accent);}
    p.lead{color:#555; margin-top:0;}
    label{display:block;font-weight:600;color:#333;margin:12px 0 6px;}
    input[type="text"], textarea, input[type="file"]{
        width:100%; padding:12px; border-radius:8px;
        border:1px solid #ccc; font-size:14px; box-sizing:border-box;
    }
    textarea{min-height:140px; resize:vertical;}
    .btn{
        background:var(--accent); color:#fff;
        padding:10px 16px; border-radius:8px;
        border:none; font-weight:600; cursor:pointer;
    }
    .btn:hover{background:var(--accent-dark);}
    .btn-ghost{
        background:#f1f3f5; color:#333; border:0; padding:10px 16px; border-radius:8px; text-decoration:none;
    }
    .meta{display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:14px;}
    .msg{padding:10px;border-radius:8px;margin-bottom:14px}
    .msg.error{background:#ffe9e9;color:#8b1f1f;border:1px solid #f3c3c3}
    .msg.ok{background:#e9f6ee;color:#1b6f3b;border:1px solid #cfe9d6}
    .top-link{margin-bottom:12px; display:block; color:#fff; text-decoration:none; font-weight:600;}
</style>
</head>
<body>
<div class="wrap">
    <a class="top-link" href="list_posts.php">? Back to Posts</a>

    <div class="card">
        <h1>Create New Post</h1>
        <p class="lead">Share jobs, internships or events with students.</p>

        <?php if ($message): ?>
            <div class="msg error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" required>

            <label for="content">Description</label>
            <textarea id="content" name="content" required></textarea>

            <label for="file">Attach File (optional)</label>
            <input id="file" name="file" type="file">

            <div class="meta">
                <div>
                    <button type="submit" class="btn">?? Publish Post</button>
                    <a href="../<?php echo ($roleSession==='alumni'?'alumni':'admin'); ?>/dashboard.php" class="btn-ghost">Cancel</a>
                </div>
                <div style="color:#666;font-size:13px">
                    Posting as: <strong><?php echo htmlspecialchars($name ?? 'You'); ?></strong>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
