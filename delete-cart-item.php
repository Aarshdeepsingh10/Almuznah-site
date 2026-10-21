<?php
session_start();

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['cart_id']) || !ctype_digit($_GET['cart_id']) || (int) $_GET['cart_id'] < 1) {
    http_response_code(400);
    exit("Invalid cart item ID.");
}

try{
    require_once _DIR_ . 'config.php';
}catch(mysqli_sql_exception $e){
    error_log($e->get_message());
    http_status_code(500);
    exit("Something went wrong. Please try again later.");
}

$conn->set_charset("utf8mb4");

$cart_id = (int) $_GET['cart_id'];
$user_id = (int) $_SESSION['user_id'];

$sql = "DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: shopcart.php");
exit;
?>
