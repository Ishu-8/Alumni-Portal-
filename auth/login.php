<?php
session_start();
include("../config/db.php");

// Show errors for debugging (remove/comment in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $validPassword = false;

        // ✅ First try normal password_verify()
        if (password_verify($password, $user['password'])) {
            $validPassword = true;
        }

        // ✅ If password_verify fails, check MD5 (for pre-inserted admins)
        if (!$validPassword && $user['password'] === md5($password)) {
            $validPassword = true;
        }

        if ($validPassword) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'student') {
                header("Location: ../student/dashboard.php");
            } elseif ($user['role'] === 'alumni') {
                header("Location: ../alumni/dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No active account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Alumni Portal</title>
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
    .login-card {
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
      width: 360px;
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
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      outline: none;
      transition: 0.3s;
    }
    input:focus {
      border-color: #2193b0;
      box-shadow: 0 0 5px rgba(33,147,176,0.5);
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: #2193b0;
      border: none;
      border-radius: 6px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover {
      background: #176d82;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .extra-links {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }
    .extra-links a {
      color: #2193b0;
      text-decoration: none;
    }
    .extra-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>

    <div class="extra-links">
      <p>Don’t have an account? <a href="register.php">Register here</a></p>
      <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>
  </div>
</body>
</html>
