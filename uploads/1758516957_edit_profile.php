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
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
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
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; padding:20px; }
        .form-box { background:white; padding:20px; width:500px; margin:auto; border-radius:8px; box-shadow:0px 2px 6px rgba(0,0,0,0.2);}
        label { display:block; margin-top:10px; font-weight:bold; }
        input, textarea { width:100%; padding:8px; margin-top:5px; }
        button { background:#0d6efd; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; margin-top:15px;}
    </style>
</head>
<body>
    <div class="form-box">
        <h2>?? Edit Profile</h2>
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
