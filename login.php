<?php
// login.php
session_start();
require_once 'config/db.php';
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid e-mail and password.';
    } else {
        $stmt = $conn->prepare('SELECT user_id, full_name, password_hash, role, status FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true); // stop session fixation
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_seen'] = time();
            
            // Redirect to the correct role dashboard
            header('Location: ' . $user['role'] . '/dashboard.php');
            exit;
        } else {
            $error = 'Invalid e-mail or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Class Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">OCMS Login</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2"><?php echo e($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php">
                            <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>