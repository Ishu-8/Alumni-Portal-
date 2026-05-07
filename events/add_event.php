<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $created_by = $_SESSION['id']; // admin id from session

    $sql = "INSERT INTO events (title, description, event_date, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $event_date, $created_by);

    if ($stmt->execute()) {
        header("Location: list_events.php?success=1");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
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
        .form-box { 
            background:white; 
            padding:30px; 
            width:450px; 
            border-radius:12px; 
            box-shadow:0px 6px 15px rgba(0,0,0,0.3);
        }
        h2 { 
            color:#0d6efd; 
            text-align:center;
            margin-bottom:20px;
        }
        label { 
            font-weight:bold; 
            display:block; 
            margin-top:10px; 
            color:#333;
        }
        input, textarea { 
            width:100%; 
            padding:10px; 
            margin-top:5px; 
            border:1px solid #ccc; 
            border-radius:6px; 
            font-size:14px;
        }
        .buttons { 
            display:flex; 
            justify-content:space-between; 
            margin-top:20px; 
        }
        button, a.cancel-btn {
            background:#0d6efd; 
            color:white; 
            padding:10px 20px; 
            border:none; 
            border-radius:8px; 
            cursor:pointer; 
            text-decoration:none; 
            text-align:center;
            font-weight:bold;
            transition: background 0.3s;
        }
        button:hover { background:#0b5ed7; }
        a.cancel-btn { background:#6c757d; }
        a.cancel-btn:hover { background:#5a6268; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Add New Event</h2>
        <form method="POST">
            <label>Event Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" rows="5" required></textarea>

            <label>Event Date</label>
            <input type="date" name="event_date" required>

            <div class="buttons">
                <button type="submit">Create Event</button>
                <a class="cancel-btn" href="../admin/dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
