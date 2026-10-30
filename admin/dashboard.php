<?php
// admin/dashboard.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Protect this page: only admins can access it
require_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">OCMS Admin</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Administrator Dashboard</h2>
        <p>You have successfully logged in. This is where you will manage users, courses, and view reports.</p>
        <div class="mt-4">
            <a href="manage_users.php" class="btn btn-outline-primary me-2">Manage Users</a>
            <a href="manage_courses.php" class="btn btn-outline-success">Manage Courses</a>
        </div>
    </div>
</body>
</html>