<?php
// teacher/dashboard.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">OCMS Teacher</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Teacher Dashboard</h2>
        <p>Welcome! Here you will manage your courses, schedule sessions, and grade assignments.</p>
        <div class="mt-4">
            <a href="my_courses.php" class="btn btn-outline-primary">View My Courses</a>
        </div>
    </div>
</body>
</html>