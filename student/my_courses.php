<?php
// student/my_courses.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');
$student_id = $_SESSION['user_id'];

// Fetch enrolled courses and their assigned teachers
$query = "SELECT c.course_id, c.course_code, c.course_name, c.description, u.full_name AS teacher_name 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.course_id 
          LEFT JOIN users u ON c.teacher_id = u.user_id 
          WHERE e.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OCMS Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Student</a>
            <div class="d-flex text-white align-items-center">
                <a href="my_courses.php" class="btn btn-sm btn-outline-light me-3">My Courses</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">My Enrolled Courses</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success"><?php echo e($row['course_code']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo e($row['course_name']); ?></h6>
                                <p class="card-text small"><strong>Teacher:</strong> <?php echo e($row['teacher_name'] ?? 'TBA'); ?></p>
                                <p class="card-text"><?php echo e($row['description'] ?: 'No description provided.'); ?></p>
                                <a href="view_sessions.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-success btn-sm w-100">View Class Schedule</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">You are not enrolled in any courses yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>