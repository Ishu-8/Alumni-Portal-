<?php
session_start();
include("../config/db.php");

// Only logged-in users
if (!isset($_SESSION['role'])) {
    die("Access denied.");
}

$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0; 
            padding: 30px;
            background: linear-gradient(135deg, #2196F3, #0d47a1); /* light blue ? dark blue */
            min-height: 100vh;
        }
        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 30px;
            font-size: 26px;
            letter-spacing: 1px;
        }
        .container { 
            max-width: 900px; 
            margin: auto; 
        }
        .post {
            background: #fff;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .post:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 12px 28px rgba(0,0,0,0.25);
        }
        .post h3 { 
            margin: 0; 
            color: #0d6efd; 
        }
        .meta { 
            font-size: 13px; 
            color: #666; 
            margin: 6px 0 12px; 
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            margin-right: 6px;
            transition: 0.2s;
        }
        .btn-view { background: #0d6efd; color: #fff; }
        .btn-edit { background: #198754; color: #fff; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn:hover { opacity: 0.9; }
        .no-posts { 
            text-align:center; 
            color:#fff; 
            font-size:17px;
            margin-top:50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>?? All Posts / Opportunities</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="meta">
                        ?? <?php echo htmlspecialchars($row['author']); ?> (<?php echo $row['role']; ?>) | 
                        ??? <?php echo $row['created_at']; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 150))); ?>...</p>

                    <a class="btn btn-view" href="view_post.php?id=<?php echo $row['id']; ?>">??? View</a>

                    <?php if (in_array($_SESSION['role'], ['alumni','admin'])): ?>
                        <a class="btn btn-edit" href="edit_post.php?id=<?php echo $row['id']; ?>">?? Edit</a>
                        <a class="btn btn-delete" href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">??? Delete</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-posts">No posts available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
