<?php
session_start();
include("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Update password
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $new_password, $email);

        if ($update->execute()) {
            $success = "? Password reset successfully! You can now login with your new password.";
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    } else {
        $error = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(to right, #2193b0, #6dd5ed); /* Blue gradient */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: #fff; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2); 
            width: 400px; 
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 { 
            text-align: center; 
            margin-bottom: 20px; 
            color: #2193b0; 
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            outline: none; 
            transition: 0.3s;
        }
        input:focus {
            border-color: #2193b0;
            box-shadow: 0 0 5px rgba(33,147,176,0.5);
        }
        button { 
            width: 100%; 
            padding: 12px; 
            border: none; 
            background: #2193b0; 
            color: #fff; 
            border-radius: 6px; 
            font-size: 16px; 
            cursor: pointer; 
            transition: 0.3s;
        }
        button:hover { 
            background: #176d82; 
        }
        .error { 
            color: red; 
            text-align: center; 
            margin-bottom: 10px; 
        }
        .success { 
            color: green; 
            text-align: center; 
            margin-bottom: 10px; 
        }
        .login-link { 
            text-align: center; 
            margin-top: 15px; 
            font-size: 14px; 
        }
        .login-link a { 
            color: #2193b0; 
            text-decoration: none; 
        }
        .login-link a:hover { 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Forgot Password</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
        <div class="login-link">
            Remembered password? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
