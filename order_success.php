
<?php
session_start();

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if (!$order_id || $order_id < 1) {
    header("Location: shop.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$sql = "
    SELECT order_id, customer_name, grand_total, payment_method,
           payment_status, order_status, created_at
    FROM tbl_orders
    WHERE order_id = ? AND user_id = ?
";

$stmt = $conn->prepare($sql);
$user_id = (int) $_SESSION['user_id'];
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();

if (!$order) {
    http_response_code(404);
    exit("Order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style2.css">
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-4">
        <h1>Thank you for your order!</h1>
        <p>Your order has been placed successfully.</p>
    </div>

    <div class="card mx-auto p-4" style="max-width: 600px;">
        <h4>Order Details</h4>
        <hr>

        <p>
            <strong>Order number:</strong>
            <?php echo (int) $order['order_id']; ?>
        </p>

        <p>
            <strong>Customer:</strong>
            <?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Total:</strong>
            AED <?php echo number_format((float) $order['grand_total'], 2); ?>
        </p>

        <p>
            <strong>Payment method:</strong>
            <?php
            echo $order['payment_method'] === 'cod'
                ? 'Cash on delivery'
                : 'Online payment';
            ?>
        </p>

        <p>
            <strong>Payment status:</strong>
            <?php echo htmlspecialchars($order['payment_status'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Order status:</strong>
            <?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Order date:</strong>
            <?php echo htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <div class="text-center mt-3">
            <a href="shop.php" class="btn btn-dark">Continue Shopping</a>
        </div>
    </div>
</div>

</body>
</html>
