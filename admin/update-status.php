
<?php
session_start();

if (
    empty($_SESSION['logged-in']) ||
    empty($_SESSION['user_id'])
) {
    header('Location: ../sign-in.php');
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: customers.php');
    exit();
}

require_once __DIR__ . '/../config.php';

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id <= 0 || !in_array($status, ['active', 'inactive','onhold'], true)) {
    exit('Invalid customer or status.');
}

$stmt = $conn->prepare(
    "UPDATE tbl_almuzah_register
     SET status = ?
     WHERE id = ? AND role = 'customer'"
);

$stmt->bind_param('si', $status, $id);
$stmt->execute();

$stmt->close();

header('Location: customers.php');
exit();
?>
