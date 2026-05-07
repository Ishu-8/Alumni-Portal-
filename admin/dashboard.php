<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; color: #333; }

    /* Container */
    .container { max-width:1200px; margin:40px auto; padding:20px; text-align:center; }

    /* Heading */
    h1 { font-size:2.5em; color:#0d6efd; margin-bottom:40px; animation: fadeIn 1s ease forwards; }

    /* Grid layout for cards */
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(300px,1fr)); gap:25px; justify-items:center; }

    /* Cards */
    .card {
        background:#fff;
        padding:30px 25px;
        border-radius:15px;
        box-shadow:0 8px 20px rgba(0,0,0,0.1);
        width:100%;
        max-width:350px;
        transition: transform 0.3s, box-shadow 0.3s;
        animation: cardFade 1s ease forwards;
        text-align:center; /* center content */
    }
    .card:hover { transform:translateY(-5px); box-shadow:0 15px 25px rgba(0,0,0,0.2); }

    /* Card heading */
    .card h2 {
        color:#0d6efd;
        margin-bottom:20px;
        font-size:1.5em;
        display:flex;
        justify-content:center;
        align-items:center;
    }
    .card h2 i { margin-right:10px; color:#2193b0; }

    /* Card list */
    .card ul {
        list-style:none;
        padding-left:0;
        margin-bottom:20px;
        display: inline-block; /* shrink to content */
        text-align:left; /* list items left-aligned inside centered ul */
    }
    .card ul li {
        display:flex;
        align-items:center;
        justify-content:flex-start; /* icon + text aligned */
        gap:8px;
        margin-bottom:12px;
        font-size:1.05em;
    }
    .card ul li i { color:#0d6efd; font-size:1.2em; }

    /* Buttons container */
    .btn-group {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Buttons */
    .btn {
        display:inline-block;
        padding:10px 20px;
        background:#0d6efd;
        color:white;
        border-radius:25px;
        text-decoration:none;
        font-weight:bold;
        text-align:center;
        transition:0.3s;
    }
    .btn:hover { background:#176d82; transform:translateY(-2px); }

    /* Message Links */
    .message-links {
        margin-top:30px;
        display:flex;
        flex-wrap:wrap;
        gap:15px;
        justify-content:center;
    }

    /* Animations */
    @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
    @keyframes cardFade { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

    /* Responsive */
    @media(max-width:768px){ h1 { font-size:2em; } }
</style>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<div class="container">
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>

    <div class="grid">
        <!-- Manage Users -->
        <div class="card">
            <h2><i class="fas fa-users-cog"></i> Manage Users</h2>
            <ul>
                <li><i class="fas fa-user-check"></i> Activate / Deactivate accounts</li>
                <li><i class="fas fa-key"></i> Reset user passwords</li>
                <li><i class="fas fa-user-slash"></i> Delete inactive users</li>
            </ul>
            <div class="btn-group">
                <a class="btn" href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
            </div>
        </div>

        <!-- Manage Posts -->
        <div class="card">
            <h2><i class="fas fa-file-alt"></i> Manage Posts</h2>
            <ul>
                <li><i class="fas fa-plus-circle"></i> Create new posts</li>
                <li><i class="fas fa-check-circle"></i> Approve / Delete posts</li>
                <li><i class="fas fa-edit"></i> Edit alumni/student posts</li>
            </ul>
            <div class="btn-group">
                <a class="btn" href="../posts/add_post.php"><i class="fas fa-plus"></i> Create Post</a>
                <a class="btn" href="../posts/list_posts.php"><i class="fas fa-list"></i> Manage Posts</a>
            </div>
        </div>

        <!-- Manage Events -->
        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Manage Events</h2>
            <ul>
                <li><i class="fas fa-calendar-plus"></i> Create upcoming events</li>
                <li><i class="fas fa-edit"></i> Edit / Remove events</li>
                <li><i class="fas fa-bullhorn"></i> Publish events to all users</li>
            </ul>
            <div class="btn-group">
                <a class="btn" href="../events/add_event.php"><i class="fas fa-plus"></i> Create Event</a>
                <a class="btn" href="../events/list_events.php"><i class="fas fa-list"></i> Manage Events</a>
            </div>
        </div>
    </div>

    <!-- Message Section -->
    <div class="message-links">
        <a class="btn" href="send_message.php"><i class="fas fa-paper-plane"></i> Send Message</a>
        <a class="btn" href="view_messages.php"><i class="fas fa-envelope"></i> Manage All Messages</a>
    </div>
</div>

</body>
</html>
