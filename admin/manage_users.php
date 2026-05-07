<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'activate') {
        $conn->query("UPDATE users SET status='active' WHERE id=$id");
    } elseif ($_GET['action'] == 'deactivate') {
        $conn->query("UPDATE users SET status='inactive' WHERE id=$id");
    } elseif ($_GET['action'] == 'delete') {
        $conn->query("DELETE FROM users WHERE id=$id");
    } elseif ($_GET['action'] == 'reset') {
        // Reset password to default "123456"
        $conn->query("UPDATE users SET password='" . md5('123456') . "' WHERE id=$id");
    }
    header("Location: manage_users.php?msg=" . urlencode("Action completed successfully."));
    exit();
}

// Fetch all users
$result = $conn->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f6fa; }
        .container { max-width: 1000px; margin:auto; padding:20px; }
        table { width:100%; border-collapse: collapse; background:white; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
        th { background:#0d6efd; color:white; }
        tr:nth-child(even) { background:#f9f9f9; }
        a.btn { padding:6px 10px; margin:2px; border-radius:4px; text-decoration:none; color:white; font-size:13px; }
        .activate { background:green; }
        .deactivate { background:orange; }
        .delete { background:red; }
        .reset { background:purple; }
        .edit { background:#0d6efd; }
        h1 { color:#0d6efd; }
        .msg { background:#d1e7dd; color:#0f5132; padding:10px; border-radius:5px; margin-bottom:15px; border:1px solid #badbcc; }
    </style>
</head>
<body>
    <?php include("../includes/navbar.php"); ?>
    <div class="container">
        <h1>?? Manage Users</h1>

        <?php if (isset($_GET['msg'])) { ?>
            <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php } ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
            <?php if ($result && $result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?php if($row['status'] == 'inactive') { ?>
                            <a class="btn activate" href="?action=activate&id=<?= $row['id'] ?>">Activate</a>
                        <?php } else { ?>
                            <a class="btn deactivate" href="?action=deactivate&id=<?= $row['id'] ?>">Deactivate</a>
                        <?php } ?>
                        <a class="btn reset" href="?action=reset&id=<?= $row['id'] ?>" onclick="return confirm('Reset password to 123456?')">Reset</a>
                        <a class="btn edit" href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                        <a class="btn delete" href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="7">No users found</td></tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
