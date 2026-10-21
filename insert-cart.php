<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

$conn = new mysqli("localhost", "root", "", "user", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = (int) $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$sku_id = filter_input(INPUT_POST, 'sku_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$product_id || !$sku_id || !$quantity || $quantity < 1) {
    die(
        "product_id: " . var_export($_POST['product_id'] ?? null, true) .
        "<br>sku_id: " . var_export($_POST['sku_id'] ?? null, true) .
        "<br>quantity: " . var_export($_POST['quantity'] ?? null, true)
    );
}

$sql = "SELECT sku_id FROM product_skus WHERE sku_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $sku_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid product variation.");
}

$stmt->close();

$sql = "SELECT cart_id, quantity FROM tbl_cart WHERE user_id = ? AND sku_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $sku_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $cart_item = $result->fetch_assoc();
    $new_quantity = (int) $cart_item['quantity'] + $quantity;
    $stmt->close();

    $sql = "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $new_quantity, $cart_item['cart_id'], $user_id);
    $stmt->execute();
} else {
    $stmt->close();

    $sql = "INSERT INTO tbl_cart (user_id, sku_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $sku_id, $quantity);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: add-to-cart.php");
exit;
?>