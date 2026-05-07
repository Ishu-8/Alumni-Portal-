<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role'];

// Handle delete (admin only)
if ($role == 'admin' && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM events WHERE id=$id");
    header("Location: list_events.php?deleted=1");
    exit();
}

// Fetch events
$result = $conn->query("SELECT e.*, u.name as creator 
                        FROM events e 
                        LEFT JOIN users u ON e.created_by=u.id 
                        ORDER BY e.event_date DESC");

// Decide cancel redirect path
$cancelLink = "#";
if ($role == 'alumni') {
    $cancelLink = "../alumni/dashboard.php";
} elseif ($role == 'student') {
    $cancelLink = "../student/dashboard.php";
} elseif ($role == 'admin') {
    $cancelLink = "../admin/dashboard.php";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #0d6efd, #4dabf7); 
            padding:50px 20px; 
            min-height: 100vh;
        }
        .container { 
            max-width:900px; 
            margin:auto; 
            background:white; 
            padding:30px; 
            border-radius:12px; 
            box-shadow:0px 6px 15px rgba(0,0,0,0.3);
        }
        h2 { 
            color:#0d6efd; 
            margin-bottom:20px; 
            text-align:center;
        }
        table { 
            width:100%; 
            border-collapse:collapse; 
            margin-top:20px;
        }
        th, td { 
            border:1px solid #ddd; 
            padding:12px; 
            text-align:left;
        }
        th { 
            background:#0d6efd; 
            color:white; 
        }
        a.btn { 
            padding:8px 15px; 
            border-radius:8px; 
            text-decoration:none; 
            color:white; 
            margin-right:5px;
            font-weight:bold;
            transition: background 0.3s;
        }
        .edit { background:#198754; }
        .edit:hover { background:#157347; }
        .delete { background:#dc3545; }
        .delete:hover { background:#b02a37; }
        .add { background:#0d6efd; margin-bottom:10px; display:inline-block; }
        .add:hover { background:#0b5ed7; }
        .cancel { background:#6c757d; margin-bottom:10px; display:inline-block; }
        .cancel:hover { background:#5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Events</h2>

        <?php if ($role == 'admin') { ?>
            <a href="add_event.php" class="btn add">+ Add New Event</a>
        <?php } ?>
        
        <!-- Role-based Cancel Button -->
        <a href="<?= $cancelLink ?>" class="btn cancel">Cancel</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>Date</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['event_date'] ?></td>
                <td><?= $row['creator'] ?? 'Admin' ?></td>
                <td>
                    <a class="btn edit" href="view_event.php?id=<?= $row['id'] ?>">View</a>
                    <?php if ($role == 'admin') { ?>
                        <a class="btn delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this event?')">Delete</a>
                    <?php } ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
