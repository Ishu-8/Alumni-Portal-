<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch messages based on role
if($role != 'admin'){
    $result = $conn->query("
        SELECT m.id, u1.name AS sender, u2.name AS receiver, m.message, m.created_at
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        WHERE m.sender_id = $user_id OR m.receiver_id = $user_id
        ORDER BY m.created_at DESC
    ");
} else {
    $result = $conn->query("
        SELECT m.id, u1.name AS sender, u2.name AS receiver, m.message, m.created_at, m.sender_role
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        ORDER BY m.created_at DESC
    ");
}

// Admin delete message
if($role == 'admin' && isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM messages WHERE id=$id");
    header("Location: view_messages.php");
    exit;
}

// Dashboard URL
if($role == 'admin') $dashboard_url = 'dashboard.php';
elseif($role == 'alumni') $dashboard_url = '../alumni/dashboard.php';
else $dashboard_url = '../student/dashboard.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0d6efd, #3b82f6, #60a5fa);
            min-height: 100vh;
        }
        /* Navbar */
        .navbar {
            background: #0d6efd;
            padding: 15px 30px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
        }
        .navbar h1 {
            font-size: 20px;
            margin: 0;
        }
        .navbar a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 1000px;
            margin: 40px auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
            text-align: left;
        }
        th {
            background: #0d6efd;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f4f9ff;
        }
        tr:nth-child(odd) {
            background: #ffffff;
        }

        /* Delete button */
        a.delete {
            color: #e63946;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid #e63946;
            border-radius: 4px;
            transition: 0.3s;
        }
        a.delete:hover {
            background: #e63946;
            color: #fff;
        }

        /* Back button */
        .btn-cancel {
            padding: 10px 20px;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            float: right;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-cancel:hover {
            background: #084298;
        }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Alumni Portal</h1>
        <div>
            <a href="<?php echo $dashboard_url; ?>"> Student Dashboard</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <h2>Messages</h2>
        <button class="btn-cancel" onclick="window.location.href='<?php echo $dashboard_url; ?>'">Back to Dashboard</button>
        <div class="clearfix"></div>
        <table>
            <tr>
                <th>ID</th>
                <th>Sender</th>
                <?php if($role == 'admin') echo "<th>Role</th>"; ?>
                <th>Receiver</th>
                <th>Message</th>
                <th>Sent At</th>
                <?php if($role == 'admin') echo "<th>Action</th>"; ?>
            </tr>
            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['sender']); ?></td>
                <?php if($role == 'admin') echo "<td>".ucfirst(htmlspecialchars($row['sender_role']))."</td>"; ?>
                <td><?php echo htmlspecialchars($row['receiver']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <?php if($role == 'admin'){ ?>
                <td><a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
