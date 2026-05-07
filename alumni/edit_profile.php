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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];
    $linkedin = $_POST['linkedin'];
    $skills = $_POST['skills'];

    // Handle photo upload
    $photo = $user['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = $photoName;
        }
    }

    $sql = "UPDATE users SET phone=?, bio=?, linkedin=?, skills=?, photo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $phone, $bio, $linkedin, $skills, $photo, $id);
    
    if ($stmt->execute()) {
        header("Location: profile.php?updated=1");
        exit();
    } else {
        echo "<p style='color:red;'>Error updating profile</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin:0;
            padding:20px;
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
        }

        .form-box {
            background: #fff;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        h2 {
            color: #0d6efd;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #333;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea { resize: vertical; }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
            background: #0d6efd;
            margin-top: 15px;
            transition: background 0.3s;
        }

        button:hover { background: #084298; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

            <label>Bio</label>
            <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>

            <label>LinkedIn</label>
            <input type="url" name="linkedin" value="<?= htmlspecialchars($user['linkedin']) ?>">

            <label>Skills</label>
            <input type="text" name="skills" value="<?= htmlspecialchars($user['skills']) ?>">

            <label>Profile Picture</label>
            <input type="file" name="photo">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
