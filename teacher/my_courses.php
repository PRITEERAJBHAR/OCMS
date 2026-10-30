<?php
// teacher/my_courses.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

// Fetch only the courses assigned to the currently logged-in teacher
$teacher_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT course_id, course_code, course_name, description FROM courses WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Teacher</a>
            <div class="d-flex text-white align-items-center">
                <a href="my_courses.php" class="btn btn-sm btn-outline-light me-3">My Courses</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">My Assigned Courses</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo e($row['course_code']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo e($row['course_name']); ?></h6>
                                <p class="card-text"><?php echo e($row['description'] ?: 'No description provided.'); ?></p>
                                <a href="manage_sessions.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-primary btn-sm">Manage Class Sessions</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">You have not been assigned to any courses yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>