<?php
// teacher/manage_sessions.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('teacher');

$course_id = (int)($_GET['course_id'] ?? 0);
$teacher_id = $_SESSION['user_id'];

// Verify this course belongs to the logged-in teacher
$stmt = $conn->prepare("SELECT course_name, course_code FROM courses WHERE course_id = ? AND teacher_id = ?");
$stmt->bind_param('ii', $course_id, $teacher_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Course not found or access denied.");
}

$msg = '';
$msgType = '';

// Handle new session creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $topic = trim($_POST['topic'] ?? '');
    $session_date = trim($_POST['session_date'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $meeting_link = trim($_POST['meeting_link'] ?? '');
    
    if (!$topic || !$session_date || !$start_time || !$end_time) {
        $msg = "Topic, Date, Start Time, and End Time are required.";
        $msgType = "danger";
    } else {
        // Using the exact column names from your database: topic, session_date, start_time, end_time
        $insert = $conn->prepare("INSERT INTO class_sessions (course_id, topic, session_date, start_time, end_time, meeting_link) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param('isssss', $course_id, $topic, $session_date, $start_time, $end_time, $meeting_link);
        if ($insert->execute()) {
            $msg = "Session scheduled successfully!";
            $msgType = "success";
        } else {
            $msg = "Error scheduling session.";
            $msgType = "danger";
        }
    }
}

// Fetch existing sessions using the exact column names
$sessions = $conn->prepare("SELECT session_id, topic, session_date, start_time, end_time, meeting_link, status FROM class_sessions WHERE course_id = ? ORDER BY session_date ASC, start_time ASC");
$sessions->bind_param('i', $course_id);
$sessions->execute();
$sessions_result = $sessions->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sessions - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Teacher</a>
            <div class="d-flex text-white align-items-center">
                <a href="my_courses.php" class="btn btn-sm btn-outline-light me-3">Back to Courses</a>
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Sessions: <?php echo e($course['course_code'] . ' - ' . $course['course_name']); ?></h2>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Schedule New Session Form -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><strong>Schedule New Session</strong></div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label">Session Topic</label>
                                <input type="text" name="topic" class="form-control" placeholder="e.g., Week 1: Introduction" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="session_date" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Start Time</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">End Time</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meeting Link (Optional)</label>
                                <input type="url" name="meeting_link" class="form-control" placeholder="https://zoom.us/j/123...">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Schedule Class</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List of Upcoming Sessions -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Link / Status</th>
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
                                                <a href="<?php echo e($s['meeting_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Join Class</a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">In-person</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No sessions scheduled yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>