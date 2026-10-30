<?php
// admin/manage_users.php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Protect this page: only admins can access it
require_role('admin');

// Fetch all users from the database
$result = $conn->query("SELECT user_id, full_name, email, role, status FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - OCMS</title>
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
            <h2>Manage Users</h2>
            <a href="add_user.php" class="btn btn-primary">Add New User</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo e($row['user_id']); ?></td>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['email']); ?></td>
                            <td><?php echo ucfirst(e($row['role'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst(e($row['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>