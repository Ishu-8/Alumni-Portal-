<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'alumni') {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Dashboard</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin:0; padding:0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; color: #333; }

        /* Container */
        .container { max-width: 900px; margin: 40px auto; padding: 20px; text-align: center; }

        /* Heading */
        h1 { font-size:2.5em; color:#0d6efd; margin-bottom:40px; animation: fadeIn 1s ease forwards; }

        /* Card */
        .card {
            background:#fff;
            padding:35px 25px;
            border-radius:15px;
            box-shadow:0 8px 20px rgba(0,0,0,0.1);
            display:inline-block;
            min-width: 300px;
            width: 100%;
            max-width: 600px;
            animation: cardFade 1s ease forwards;
        }
        .card h2 { color:#0d6efd; margin-bottom:20px; font-size:1.8em; display:flex; justify-content:center; align-items:center; }
        .card h2 i { margin-right:10px; color:#2193b0; }
        .card ul { list-style:none; padding-left:0; margin-bottom:25px; text-align:center; }
        .card ul li { display:flex; justify-content:center; align-items:center; margin-bottom:12px; font-size:1.1em; }
        .card ul li i { color:#0d6efd; margin-right:10px; font-size:1.2em; }

        /* Buttons */
        .card .btn {
            display:inline-block;
            margin:5px 5px 0 0;
            padding:10px 20px;
            background:#0d6efd;
            color:white;
            border-radius:25px;
            text-decoration:none;
            font-weight:bold;
            transition:0.3s;
        }
        .card .btn:hover { background:#176d82; transform:translateY(-2px); }

        /* Message Links */
        .message-links { margin-top:25px; display:flex; flex-wrap:wrap; gap:10px; justify-content:center; }

        /* Animations */
        @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
        @keyframes cardFade { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

        /* Responsive */
        @media(max-width:768px){
            h1 { font-size:2em; }
        }
    </style>
</head>
<body>

    <?php include("../includes/navbar.php"); ?>

    <div class="container">
        <h1><i class="fas fa-user-graduate"></i> Alumni Dashboard</h1>

        <div class="card">
            <h2><i class="fas fa-briefcase"></i> Alumni Features</h2>
            <ul>
                <li><i class="fas fa-plus-circle"></i> Post job opportunities</li>
                <li><i class="fas fa-handshake"></i> Post internships & training programs</li>
                <li><i class="fas fa-file-upload"></i> Upload documents (PDF, PPT, DOC, Images, Videos)</li>
                <li><i class="fas fa-comments"></i> Interact with students</li>
            </ul>
            <div>
                <a class="btn" href="../posts/add_post.php"><i class="fas fa-plus"></i> Create Post</a>
                <a class="btn" href="../posts/list_posts.php"><i class="fas fa-list"></i> View All Posts</a>
                <a class="btn" href="../events/list_events.php"><i class="fas fa-calendar-alt"></i> View Events</a>
            </div>
        </div>

        <div class="message-links">
            <a class="btn" href="send_message.php"><i class="fas fa-paper-plane"></i> Send Message</a>
            <a class="btn" href="view_messages.php"><i class="fas fa-envelope"></i> View My Messages</a>
        </div>
    </div>

</body>
</html>
