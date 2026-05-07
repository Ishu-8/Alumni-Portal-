<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$error = '';
$user = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id    = intval($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? 'student';

    if ($id <= 0) {
        $error = "Invalid user ID.";
    } elseif ($name === '' || $email === '') {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $sql = "UPDATE users SET name=?, email=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        if ($stmt->execute()) {
            header("Location: manage_users.php?msg=" . urlencode("User Updated Successfully"));
            exit();
        } else {
            $error = "Failed to update user: " . $stmt->error;
        }
    }
}

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc() ?: null;
}

if (!$user && $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage_users.php?msg=" . urlencode("User not found or id missing."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #0d6efd, #4dabf7); 
            margin:0;
            min-height:100vh;
        }
        /* Navbar fixed on top */
        .navbar {
            background:#0b5ed7;
            padding:15px 20px;
            color:#fff;
            position:fixed;
            top:0;
            left:0;
            right:0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            z-index:1000;
        }
        .navbar a {
            color:#fff;
            text-decoration:none;
            margin-left:15px;
            font-weight:600;
        }
        .page-wrapper {
            display:flex;
            justify-content:center;
            align-items:flex-start;
            padding:120px 20px 40px; /* space for navbar */
        }
        .container { 
            padding: 30px; 
            max-width: 600px; 
            width:100%;
            background:white; 
            border-radius:12px; 
            box-shadow:0 6px 20px rgba(0,0,0,0.2);
        }
        h1 { color:#0d6efd; text-align:center; margin-bottom:20px; }
        label { display:block; margin-top:12px; font-weight:bold; color:#333; }
        input, select { width:100%; padding:10px; margin-top:5px; border:1px solid #d7dbe0; border-radius:8px; box-sizing:border-box; }
        button { margin-top:20px; padding:12px 20px; background:#0d6efd; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600; width:100%; transition: background 0.3s; }
        button:hover { background:#0b5ed7; }
        .back-btn { display:inline-block; margin-top:15px; padding:10px 15px; background:#6c757d; color:white; border-radius:8px; text-decoration:none; font-weight:bold; }
        .error-msg { color:#8b1f1f; background:#ffe9e9; padding:10px; border-radius:8px; margin-bottom:15px; border:1px solid #f3c3c3; text-align:center; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div><strong>Alumni Portal</strong></div>
        <div>
            <a href="dashboard.php">Admin Dashboard</a>
            <a href="manage_users.php"> Manage Users</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="container">
            <h1>Edit User</h1>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id'] ?? ($_POST['id'] ?? '')); ?>">

                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ($_POST['name'] ?? '')); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ($_POST['email'] ?? '')); ?>" required>

                <label>Role:</label>
                <?php $currentRole = $user['role'] ?? ($_POST['role'] ?? 'student'); ?>
                <select name="role" required>
                    <option value="student" <?php echo ($currentRole === "student") ? "selected" : ""; ?>>Student</option>
                    <option value="alumni"  <?php echo ($currentRole === "alumni")  ? "selected" : ""; ?>>Alumni</option>
                    <option value="admin"   <?php echo ($currentRole === "admin")   ? "selected" : ""; ?>>Admin</option>
                </select>

                <button type="submit">Update User</button>
            </form>

            <a href="manage_users.php" class="back-btn">? Back to Users</a>
        </div>
    </div>
</body>
</html>
