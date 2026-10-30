<?php
// admin/add_user.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

require_role('admin');

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (!$fullName || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password || !in_array($role, ['teacher', 'student', 'admin'])) {
        $msg = 'Please provide valid inputs for all fields.';
        $msgType = 'danger';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $fullName, $email, $hash, $role);
        
        if ($stmt->execute()) {
            $msg = 'User added successfully!';
            $msgType = 'success';
        } else {
            if ($conn->errno === 1062) { // MySQL duplicate entry error code
                $msg = 'An account with this email already exists.';
            } else {
                $msg = 'Database error. Please try again.';
            }
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
    <title>Add User - OCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">OCMS Admin</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Welcome, <?php echo e($_SESSION['full_name']); ?>!</span>
                <a href="../login.php" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New User</h2>
            <a href="manage_users.php" class="btn btn-secondary">Back to Users</a>
        </div>

        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body p-4">
                <?php if ($msg): ?>
                    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="add_user.php">
                    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="" disabled selected>Select a role...</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create User</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>