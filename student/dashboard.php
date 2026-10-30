<?php
// student/dashboard.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">OCMS Student</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Student Dashboard</h2>
        <p>Welcome! Here you will view your enrolled courses, download materials, and submit assignments.</p>
        <div class="mt-4">
            <a href="available_courses.php" class="btn btn-primary me-2">Browse Available Courses</a>
            <a href="my_courses.php" class="btn btn-outline-success">View My Courses</a>
        </div>
    </div>
</body>
</html>