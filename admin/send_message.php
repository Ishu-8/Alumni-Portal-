<?php
session_start();
include('../config/db.php');

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

// Dashboard URL
if($sender_role == 'admin'){
    $dashboard_url = 'dashboard.php';
} elseif($sender_role == 'alumni'){
    $dashboard_url = '../alumni/dashboard.php';
} else {
    $dashboard_url = '../student/dashboard.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
            min-height:100vh;
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
            background:#fff;
            padding:30px;
            border-radius:12px;
            width:450px;
            margin:50px auto;
            box-shadow:0 6px 20px rgba(0,0,0,0.25);
        }
        h2 {
            text-align:center;
            margin-bottom:20px;
            color:#0d6efd;
        }
        label {
            font-weight:bold;
            margin-top:10px;
            display:block;
            color:#333;
        }
        input, textarea, select {
            width:100%;
            padding:12px;
            margin:8px 0 15px;
            border-radius:8px;
            border:1px solid #ccc;
            font-size:14px;
            box-sizing:border-box;
        }
        button {
            padding:12px 20px;
            border:none;
            border-radius:8px;
            font-size:14px;
            cursor:pointer;
            font-weight:600;
        }
        .send-btn {
            background:#0d6efd;
            color:#fff;
            transition: background 0.3s;
        }
        .send-btn:hover { background:#084298; }
        .cancel-btn {
            background:#6c757d;
            color:#fff;
            margin-left:10px;
            transition: background 0.3s;
        }
        .cancel-btn:hover { background:#495057; }

        .success {
            color:#0f5132;
            background:#d1e7dd;
            border:1px solid #badbcc;
            padding:10px;
            border-radius:8px;
            margin-bottom:15px;
            text-align:center;
        }
        .error {
            color:#842029;
            background:#f8d7da;
            border:1px solid #f5c2c7;
            padding:10px;
            border-radius:8px;
            margin-bottom:15px;
            text-align:center;
        }
        .btn-group {
            text-align:right;
        }

        @media(max-width:500px){
            .container{width:90%; margin:30px auto;}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Alumni Portal</h1>
        <div>
            <a href="<?= $dashboard_url ?>">Admin Dashboard</a>
            <a href="../admin/view_messages.php">View Messages</a>
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
                <?php while($row = $users_result->fetch_assoc()){ ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php } ?>
            </select>

            <label>Message:</label>
            <textarea name="message" rows="5" placeholder="Type your message..." required></textarea>

            <div class="btn-group">
                <button type="submit" name="send" class="send-btn">Send Message</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='<?= $dashboard_url ?>'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
