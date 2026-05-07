<?php
session_start();
include("../config/db.php");

// Only alumni or admin can edit posts
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['alumni', 'admin'])) {
    die("Access denied.");
}

// Get post ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$post_id = intval($_GET['id']);

// Fetch existing post
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // File handling
    $file_path = $post['file_path']; 
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $uploads_dir = __DIR__ . "/../uploads/";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['file']['name']);
        $target = $uploads_dir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $file_path = "uploads/" . $filename;
        }
    }

    // Update query
    $sql = "UPDATE posts SET title=?, content=?, file_path=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $file_path, $post_id);

    if ($stmt->execute()) {
        header("Location: list_posts.php?msg=Post Updated Successfully");
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0; padding: 0;
            background: linear-gradient(135deg, #007BFF, #002855); /* light blue ? dark blue combo */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 500px;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 8px 30px rgba(0,0,0,0.25);
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 14px;
        }
        textarea { height: 120px; }
        .btn {
            display: block;
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .file-link {
            margin-top: 8px;
            display: inline-block;
            color: #28a745;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Post</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

            <label>Content</label>
            <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>

            <label>Current File</label>
            <?php if ($post['file_path']): ?>
                <a class="file-link" href="../<?php echo $post['file_path']; ?>" target="_blank">?? View Current File</a>
            <?php else: ?>
                <p style="color:gray;">No file uploaded</p>
            <?php endif; ?>

            <label>Upload New File (optional)</label>
            <input type="file" name="file">

            <button type="submit" class="btn">Update Post</button>
        </form>
    </div>
</body>
</html>
