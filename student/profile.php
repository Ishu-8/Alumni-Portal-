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
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background-color: #0d6efd;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar h1 {
            font-size: 20px;
            margin: 0;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-weight: 500;
        }
        .navbar a:hover { text-decoration: underline; }

        /* Profile card */
        .card {
            background: #fff;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            margin: 40px auto;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            text-align: center;
        }

        h2 {
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid #0d6efd;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
            color: #333;
        }

        a.btn {
            display: inline-block;
            padding: 10px 20px;
            background: #0d6efd;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 15px;
            font-weight: 600;
            transition: background 0.3s;
        }
        a.btn:hover { background: #084298; }

        a.cancel {
            background: #6c757d;
        }
        a.cancel:hover { background: #495057; }

        @media(max-width:650px){
            .card { padding: 20px; }
            .navbar h1 { font-size: 18px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>Alumni Portal</h1>
        <div>
            <a href="../alumni/dashboard.php">Student Dashboard</a>
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
        <a class="btn cancel" href="../student/dashboard.php">Cancel</a>
    </div>
</body>
</html>
