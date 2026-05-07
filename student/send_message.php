<?php
session_start();
include('../config/db.php'); // Adjust path

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$sender_role = $_SESSION['role'];

// Handle form submission
if(isset($_POST['send'])){
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if(!empty($message)){
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_role, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $sender_id, $receiver_id, $sender_role, $message);
        $stmt->execute();
        $stmt->close();
        $success = "Message sent successfully!";
    } else {
        $error = "Message cannot be empty!";
    }
}

// Fetch users depending on role
if($sender_role == 'student'){
    $users_result = $conn->query("SELECT id, name FROM users WHERE role='alumni'");
} elseif($sender_role == 'alumni'){
    $users_result = $conn->query("SELECT id, name FROM users WHERE role='student'");
} else { // admin
    $users_result = $conn->query("SELECT id, name FROM users WHERE role IN ('student','alumni')");
}

// Determine dashboard URL
if($sender_role == 'admin'){
    $dashboard_url = 'dashboard.php';
} elseif($sender_role == 'alumni'){
    $dashboard_url = '../alumni/dashboard.php';
} else {
    $dashboard_url = '../student/dashboard.php';
}

// Link to view messages
if($sender_role == 'admin'){
    $view_messages_url = 'view_messages.php';
} elseif($sender_role == 'alumni'){
    $view_messages_url = '../alumni/view_messages.php';
} else {
    $view_messages_url = '../student/view_messages.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
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
            margin: 0;
            font-size: 20px;
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
            max-width: 500px;
            margin: 50px auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        select, input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .btn-group {
            margin-top: 15px;
            text-align: right;
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }
        .send-btn {
            background: #0d6efd;
            color: #fff;
        }
        .send-btn:hover {
            background: #084298;
        }
        .cancel-btn {
            background: #6c757d;
            color: #fff;
            margin-left: 10px;
        }
        .cancel-btn:hover {
            background: #495057;
        }

        .success { color: green; font-weight: bold; text-align: center; }
        .error { color: red; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Alumni Portal</h1>
        <div>
            <a href="<?php echo $dashboard_url; ?>">Student Dashboard</a>
            <a href="<?php echo $view_messages_url; ?>">View Messages</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <h2>Send Message</h2>

        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Send To:</label>
            <select name="receiver_id" required>
                <option value="">Select User</option>
                <?php while($row = $users_result->fetch_assoc()){
                    echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
                } ?>
            </select>

            <label>Message:</label>
            <textarea name="message" rows="5" placeholder="Type your message..." required></textarea>

            <div class="btn-group">
                <button type="submit" name="send" class="send-btn">Send Message</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='<?php echo $dashboard_url; ?>'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
