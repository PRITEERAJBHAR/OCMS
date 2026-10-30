<?php
// admin/add_course.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('admin');

$msg = '';
$msgType = '';

// Fetch all active teachers for the dropdown
$teachers = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'teacher' AND status = 'active'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $teacherId = (int)($_POST['teacher_id'] ?? 0);
    
    if (!$code || !$name || !$teacherId) {
        $msg = 'Course Code, Course Name, and Teacher are required.';
        $msgType = 'danger';
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, description, teacher_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $code, $name, $desc, $teacherId);
        
        if ($stmt->execute()) {
            $msg = 'Course created successfully!';
            $msgType = 'success';
        } else {
            $msg = ($conn->errno === 1062) ? 'This Course Code already exists.' : 'Database error.';
            $msgType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Admin</a>
            <div class="d-flex text-white align-items-center">
                <a href="manage_users.php" class="btn btn-sm btn-outline-light me-2">Users</a>
                <a href="manage_courses.php" class="btn btn-sm btn-outline-light me-3">Courses</a>
                <a href="../login.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Course</h2>
            <a href="manage_courses.php" class="btn btn-secondary">Back to Courses</a>
        </div>

        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body p-4">
                <?php if ($msg): ?>
                    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="add_course.php">
                    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="course_code" class="form-control" placeholder="e.g., CS101" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-control" placeholder="e.g., Intro to Programming" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Assign Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="" disabled selected>Select a teacher...</option>
                            <?php while ($t = $teachers->fetch_assoc()): ?>
                                <option value="<?php echo $t['user_id']; ?>">
                                    <?php echo e($t['full_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Create Course</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>