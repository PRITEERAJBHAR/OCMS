<?php
// student/view_sessions.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('student');
$student_id = $_SESSION['user_id'];
$course_id = (int)($_GET['course_id'] ?? 0);

// Verify that the student is actually enrolled in this course
check_enrollment($student_id, $course_id);

// Fetch course details
$stmt = $conn->prepare("SELECT c.course_code, c.course_name, u.full_name AS teacher_name FROM courses c LEFT JOIN users u ON c.teacher_id = u.user_id WHERE c.course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (! $course) {
    die("Course not found.");
}

// Fetch scheduled sessions for this course
$sessions = $conn->prepare("SELECT topic, session_date, start_time, end_time, meeting_link, status FROM class_sessions WHERE course_id = ? ORDER BY session_date ASC, start_time ASC");
$sessions->bind_param('i', $course_id);
$sessions->execute();
$sessions_result = $sessions->get_result();

// Helper function to verify enrollment
function check_enrollment($student_id, $course_id) {
    global $conn;
    $check = $conn->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check->bind_param('ii', $student_id, $course_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        die("Access denied. You are not enrolled in this course.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - OCMS Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Student</a>
            <div class="d-flex text-white align-items-center">
                <a href="my_courses.php" class="btn btn-sm btn-outline-light me-3">Back to My Courses</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-2">Class Schedule: <?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></h2>
        <p class="text-muted mb-4">Instructor: <strong><?php echo e($course['teacher_name'] ?? 'TBA'); ?></strong></p>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($sessions_result->num_rows > 0): ?>
                            <?php while ($s = $sessions_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo e($s['topic']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($s['session_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($s['start_time'])) . ' - ' . date('h:i A', strtotime($s['end_time'])); ?></td>
                                <td>
                                    <?php if ($s['meeting_link']): ?>
                                        <a href="<?php echo e($s['meeting_link']); ?>" target="_blank" class="btn btn-sm btn-success">Join Online Class</a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">In-person / No Link</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No class sessions have been scheduled for this course yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>