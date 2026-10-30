<?php
// student/available_courses.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');
$student_id = $_SESSION['user_id'];
$msg = '';
$msgType = '';

// Handle Enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $course_id = (int)($_POST['course_id'] ?? 0);
    
    if ($course_id > 0) {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $student_id, $course_id);
        
        if ($stmt->execute()) {
            $msg = 'Successfully enrolled! You can now view it in My Courses.';
            $msgType = 'success';
        } else {
            $msg = ($conn->errno === 1062) ? 'You are already enrolled.' : 'Database error.';
            $msgType = 'danger';
        }
    }
}

// Fetch courses the student is NOT enrolled in yet
$query = "SELECT c.course_id, c.course_code, c.course_name, c.description, u.full_name AS teacher_name 
          FROM courses c 
          LEFT JOIN users u ON c.teacher_id = u.user_id 
          WHERE c.course_id NOT IN (SELECT course_id FROM enrollments WHERE student_id = ?)
          ORDER BY c.course_id DESC";
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
    <title>Available Courses - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Student</a>
            <div class="d-flex text-white align-items-center">
                <a href="available_courses.php" class="btn btn-sm btn-outline-light me-2">Browse Courses</a>
                <a href="my_courses.php" class="btn btn-sm btn-outline-light me-3">My Courses</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Available Courses</h2>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo e($row['course_code']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo e($row['course_name']); ?></h6>
                                <p class="card-text small"><strong>Teacher:</strong> <?php echo e($row['teacher_name'] ?? 'TBA'); ?></p>
                                <form method="POST">
                                    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
                                    <button type="submit" class="btn btn-success w-100 mt-2">Enroll Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No new courses available for enrollment.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>