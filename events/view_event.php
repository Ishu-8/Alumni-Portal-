<?php
session_start();
include("../config/db.php");

if (!isset($_GET['id'])) {
    die("Event not found.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT e.*, u.name as creator 
                        FROM events e 
                        LEFT JOIN users u ON e.created_by=u.id 
                        WHERE e.id=$id");
$event = $result->fetch_assoc();
if (!$event) {
    die("Event not found.");
}

// Check role from session
$role = $_SESSION['role'] ?? '';
$backLink = "#"; // default fallback
if ($role === 'alumni') {
    $backLink = "../alumni/dashboard.php";
} elseif ($role === 'student') {
    $backLink = "../student/dashboard.php";
} elseif ($role === 'admin') {
    $backLink = "../admin/dashboard.php";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($event['title']) ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #0d6efd, #4dabf7); 
            padding:50px 20px; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .card { 
            background:white; 
            padding:30px; 
            max-width:600px; 
            width:100%;
            border-radius:12px; 
            box-shadow:0px 6px 15px rgba(0,0,0,0.3);
        }
        h2 { 
            color:#0d6efd; 
            margin-bottom:20px; 
            text-align:center;
        }
        p { 
            line-height:1.6; 
            color:#333;
        }
        .meta { 
            color:#555; 
            margin-top:10px; 
            font-size:14px;
            text-align:right;
        }
        a.btn { 
            display:inline-block; 
            margin-top:20px; 
            padding:12px 20px; 
            background:#0d6efd; 
            color:white; 
            border-radius:8px; 
            text-decoration:none; 
            font-weight:bold;
            transition: background 0.3s;
        }
        a.btn:hover { 
            background:#0b5ed7; 
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><?= htmlspecialchars($event['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <p class="meta">Date: <?= $event['event_date'] ?> | Created by: <?= $event['creator'] ?? 'Admin' ?></p>
        
        <!-- Back button now redirects based on role -->
        <a class="btn" href="<?= $backLink ?>">Back to Dashboard</a>
    </div>
</body>
</html>
