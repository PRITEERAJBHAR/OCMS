<?php
// includes/auth.php
const IDLE_LIMIT = 1800; // 30 minutes

function require_role(string $role): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $loggedIn = isset($_SESSION['user_id'], $_SESSION['role']);
    $idleOk = $loggedIn && (time() - ($_SESSION['last_seen'] ?? 0)) < IDLE_LIMIT;
    
    if (!$loggedIn || !$idleOk || $_SESSION['role'] !== $role) {
        session_unset();
        session_destroy();
        header('Location: ../login.php?msg=access');
        exit;
    }
    
    $_SESSION['last_seen'] = time(); // refresh activity time
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void {
    $sent = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(400);
        die('Invalid request.');
    }
}

function e(string $s): string {
    // output escaping helper
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>