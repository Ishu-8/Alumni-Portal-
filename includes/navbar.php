<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    .navbar {
        background: #0d6efd;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .navbar h2 {
        color: white;
        margin: 0;
    }
    .navbar a {
        color: white;
        margin-left: 15px;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        background: #0a58ca;
    }
    .navbar a:hover {
        background: #084298;
    }
</style>

<div class="navbar">
    <h2>?? Alumni Portal</h2>
    <div>
        <a href="/alumni_portal_php_enhanced/index.php">Home</a>
       
        
        <?php if(isset($_SESSION['role'])): ?>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="/alumni_portal_php_enhanced/admin/dashboard.php">Admin Dashboard</a>
                <a href="/alumni_portal_php_enhanced/posts/list_posts.php">Posts</a>
                <a href="/alumni_portal_php_enhanced/events/list_events.php">Events</a>
            <?php elseif($_SESSION['role'] === 'alumni'): ?>
                <a href="/alumni_portal_php_enhanced/alumni/dashboard.php">Alumni Dashboard</a>
                <a href="/alumni_portal_php_enhanced/posts/list_posts.php">Posts</a>
                <a href="/alumni_portal_php_enhanced/events/list_events.php">Events</a>
                <a href="/alumni_portal_php_enhanced/alumni/profile.php">Profile</a>
            <?php elseif($_SESSION['role'] === 'student'): ?>
                <a href="/alumni_portal_php_enhanced/student/dashboard.php">Student Dashboard</a>
                <a href="/alumni_portal_php_enhanced/posts/list_posts.php">Posts</a>
                <a href="/alumni_portal_php_enhanced/events/list_events.php">Events</a>
                <a href="/alumni_portal_php_enhanced/student/profile.php">Profile</a>
            <?php endif; ?>
            <a href="/alumni_portal_php_enhanced/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/alumni_portal_php_enhanced/auth/login.php">Login</a>
            <a href="/alumni_portal_php_enhanced/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>
