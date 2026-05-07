<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Portal - Home</title>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin:0; padding:0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; color: #333; line-height:1.6; }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            color: white;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            position: relative;
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite, fadeIn 1s ease forwards;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeIn { from {opacity:0;} to{opacity:1;} }
        @keyframes slideDown { from {opacity:0; transform:translateY(-30px);} to {opacity:1; transform:translateY(0);} }
        @keyframes slideUp { from {opacity:0; transform:translateY(30px);} to {opacity:1; transform:translateY(0);} }
        @keyframes cardFade { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

        .hero h1 { font-size: 3.5em; margin-bottom: 20px; animation: slideDown 1s ease forwards; }
        .hero p { font-size: 1.3em; max-width: 700px; margin: 0 auto; animation: slideUp 1s ease forwards; }
        .hero .cta-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 35px;
            font-size: 18px;
            color: white;
            background: #0d6efd;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .hero .cta-btn:hover {
            background: #084298;
            transform: translateY(-3px);
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: -50px auto 50px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        /* Cards */
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: cardFade 1s ease forwards;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(0,0,0,0.2); }
        .card h2 { color: #0d6efd; margin-bottom: 15px; font-size: 1.8em; display:flex; align-items:center; }
        .card h2 i { margin-right: 10px; color: #2193b0; }
        .card p, .card ul { font-size: 1em; color: #555; }
        .card ul { list-style:none; padding-left:0; }
        .card ul li { margin-bottom: 12px; display:flex; align-items:center; }
        .card ul li i { color: #0d6efd; margin-right:10px; font-size: 1.1em; }

        /* Footer CTA */
        .dashboard-cta { text-align:center; margin-top:20px; }
        .dashboard-cta a {
            display:inline-block; padding:10px 25px; background:#2193b0; color:white;
            border-radius:25px; text-decoration:none; font-weight:bold; transition:0.3s;
        }
        .dashboard-cta a:hover { background:#176d82; }

        /* Responsive */
        @media(max-width:768px){
            .hero h1 { font-size: 2.5em; }
            .hero p { font-size: 1.1em; }
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include("includes/navbar.php"); ?>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to the Alumni Portal</h1>
        <p>Connecting Students, Alumni, and College Management in one platform</p>
        <a href="auth/register.php" class="cta-btn">Get Started</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-info-circle"></i> About the Portal</h2>
            <p>
                This Alumni Portal helps students interact with alumni and stay updated with 
                career opportunities, events, and workshops. Alumni can share job opportunities, 
                internships, and career guidance. Admins can oversee and manage all activities.
            </p>
        </div>

        <div class="card">
            <h2><i class="fas fa-list-check"></i> Features</h2>
            <ul>
                <li><i class="fas fa-user-graduate"></i> Student, Alumni, and Admin Login</li>
                <li><i class="fas fa-briefcase"></i> Alumni can post Jobs, Internships, and Career Guidance</li>
                <li><i class="fas fa-file-upload"></i> Upload documents, images, videos, and presentations</li>
                <li><i class="fas fa-search"></i> Students can explore opportunities and events</li>
                <li><i class="fas fa-cogs"></i> Admin can manage all posts, users, and events</li>
            </ul>
        </div>

        <div class="card">
            <h2><i class="fas fa-rocket"></i> Get Started</h2>
            <?php if(isset($_SESSION['role'])): ?>
                <p>Welcome back, <b><?php echo $_SESSION['role']; ?></b>! Go to your dashboard.</p>
                <div class="dashboard-cta">
                    <a href="<?php
                        if($_SESSION['role']=='student') echo 'student/dashboard.php';
                        elseif($_SESSION['role']=='alumni') echo 'alumni/dashboard.php';
                        elseif($_SESSION['role']=='admin') echo 'admin/dashboard.php';
                    ?>">Go to Dashboard</a>
                </div>
            <?php else: ?>
                <p>Login or Register to explore the portal and connect with alumni.</p>
                <div class="dashboard-cta">
                    <a href="auth/login.php">Login</a>
                    <a href="auth/register.php" style="margin-left:10px;">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
