<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
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

        /* Profile Card */
        .card {
            background:white;
            padding:30px;
            max-width:600px;
            margin:50px auto;
            border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,0.25);
            text-align:center;
        }
        h2 {
            color:#0d6efd;
            margin-bottom:20px;
        }
        .profile-pic {
            width:120px;
            height:120px;
            border-radius:50%;
            object-fit:cover;
            margin-bottom:20px;
            border:3px solid #0d6efd;
        }
        p {
            font-size:16px;
            margin:8px 0;
            color:#333;
        }

        /* Buttons */
        a.btn {
            display:inline-block;
            padding:10px 20px;
            background:#0d6efd;
            color:white;
            border-radius:8px;
            text-decoration:none;
            margin-top:15px;
            transition: background 0.3s;
            font-weight:600;
        }
        a.btn:hover { background:#084298; }

        a.cancel {
            background:#6c757d;
        }
        a.cancel:hover { background:#495057; }

        @media(max-width:650px){
            .card { width:90%; margin:30px auto; padding:20px; }
            .navbar { flex-direction: column; align-items: flex-start; }
            .navbar a { margin:5px 0 0 0; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Alumni Portal</h1>
        <div>
            <a href="../alumni/dashboard.php">Alumni Dashboard</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <h2>My Profile</h2>
        <img class="profile-pic" src="<?= $user['photo'] ? '../uploads/'.$user['photo'] : '../uploads/default.png' ?>" alt="Profile Picture">
        
        <p><b>Name:</b> <?= htmlspecialchars($user['name']) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
        <p><b>Phone:</b> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
        <p><b>Bio:</b> <?= htmlspecialchars($user['bio'] ?? '-') ?></p>
        <p><b>LinkedIn:</b> 
            <?php if (!empty($user['linkedin'])): ?>
                <a href="<?= htmlspecialchars($user['linkedin']) ?>" target="_blank"><?= htmlspecialchars($user['linkedin']) ?></a>
            <?php else: ?> - <?php endif; ?>
        </p>
        <p><b>Skills:</b> <?= htmlspecialchars($user['skills'] ?? '-') ?></p>

        <!-- Buttons -->
        <a class="btn" href="edit_profile.php">Edit Profile</a>
        <a class="btn cancel" href="../alumni/dashboard.php">Cancel</a>
    </div>
</body>
</html>
