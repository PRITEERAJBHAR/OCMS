<?php
// admin/manage_courses.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('admin');

// Fetch courses with teacher names
$query = "SELECT c.course_id, c.course_code, c.course_name, u.full_name AS teacher_name 
          FROM courses c 
          LEFT JOIN users u ON c.teacher_id = u.user_id 
          ORDER BY c.course_id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Admin</a>
            <div class="d-flex text-white align-items-center">
                <a href="manage_users.php" class="btn btn-sm btn-outline-light me-2">Users</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Courses</h2>
            <a href="add_course.php" class="btn btn-primary">Add New Course</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Assigned Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo e($row['course_code']); ?></strong></td>
                            <td><?php echo e($row['course_name']); ?></td>
                            <td><?php echo e($row['teacher_name'] ?? 'Unassigned'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>