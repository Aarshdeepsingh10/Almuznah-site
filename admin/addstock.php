
<?php
session_start();

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['logged-in'])
) {
    header('Location: ../sign-in.php');
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lowstock.php');
    exit();
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header('Location: lowstock.php?status=invalid');
    exit();
}

$sku_id = $_POST['sku_id'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$action = $_POST['action'] ?? '';

if (
    !ctype_digit((string)$sku_id) ||
    (int)$sku_id < 1 ||
    !ctype_digit((string)$quantity) ||
    (int)$quantity < 1 ||
    (int)$quantity > 1000
) {
    header('Location: lowstock.php?status=invalid');
    exit();
}

if (!in_array($action, ['increase', 'decrease'], true)) {
    header('Location: lowstock.php?status=invalid');
    exit();
}

$sku_id = (int)$sku_id;
$quantity = (int)$quantity;

$change = ($action === 'increase') ? $quantity : -$quantity;

try {

    require_once __DIR__ . '/../config.php';

    $sql = "UPDATE product_skus
            SET stock_quantity = stock_quantity + ?
            WHERE sku_id = ?
            AND stock_quantity + ? >= 0";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iii",
        $change,
        $sku_id,
        $change
    );

    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        $status = 'success';
    } else {
        $status = 'failed';
    }

    $stmt->close();
    $conn->close();

    header("Location: low-stock.php?status=$status");
    exit();

} catch (mysqli_sql_exception $e) {

    error_log($e->getMessage());

    header('Location: lowstock.php?status=error');
    exit();
}
