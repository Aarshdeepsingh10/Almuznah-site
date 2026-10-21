
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$user_id = (int) $_SESSION['user_id'];

try {
    $customer_name = trim($_POST['text'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phoneno'] ?? '');

    $billing_address1 = trim($_POST['address1'] ?? '');
    $billing_address2 = trim($_POST['address2'] ?? '');
    $billing_city = trim($_POST['city'] ?? '');
    $billing_state = trim($_POST['state'] ?? '');
    $billing_country = trim($_POST['country'] ?? '');

    $payment_method = $_POST['payment_method'] ?? '';
    $order_note = trim($_POST['order_note'] ?? '');

    $same_as_billing = isset($_POST['same_as_billing']);

    $required_fields = [
        'name' => $customer_name,
        'email' => $email,
        'phone' => $phone,
        'address1' => $billing_address1,
        'city' => $billing_city,
        'state' => $billing_state,
        'country' => $billing_country,
        'payment_method' => $payment_method
    ];

    foreach ($required_fields as $field => $value) {
        if ($value === '') {
            throw new Exception("Missing field: " . $field);
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address.");
    }

    if (!in_array($payment_method, ['cod', 'razorpay'], true)) {
        throw new Exception("Invalid payment method.");
    }

    if (empty($_POST['confirm_order'])) {
        throw new Exception("Please confirm your order.");
    }

    if ($same_as_billing) {
        $shipping_address1 = $billing_address1;
        $shipping_address2 = $billing_address2;
        $shipping_city = $billing_city;
        $shipping_state = $billing_state;
    } else {
        $shipping_address1 = trim($_POST['shipping_address_line1'] ?? '');
        $shipping_address2 = trim($_POST['shipping_address_line2'] ?? '');
        $shipping_city = trim($_POST['shipping_city'] ?? '');
        $shipping_state = trim($_POST['shipping_state'] ?? '');

        if (
            $shipping_address1 === '' ||
            $shipping_city === '' ||
            $shipping_state === ''
        ) {
            throw new Exception("Please complete your shipping address.");
        }
    }

    $conn->begin_transaction();

    $cart_sql = "
        SELECT ci.sku_id, ci.quantity, p.value
        FROM cart_items ci
        JOIN product_skus ps ON ci.sku_id = ps.sku_id
        JOIN tbl_product p ON ps.product_id = p.id
        WHERE ci.user_id = ?
        FOR UPDATE
    ";

    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();

    $cart_items = $cart_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $cart_stmt->close();

    if (empty($cart_items)) {
        throw new Exception("Your cart is empty.");
    }

    $subtotal = 0;

    foreach ($cart_items as $item) {
        $quantity = (int) $item['quantity'];
        $price = (float) $item['value'];

        if ($quantity < 1 || $price < 0) {
            throw new Exception("Invalid cart item.");
        }

        $subtotal += $price * $quantity;
    }

    $payment_status = 'pending';
    $order_status = 'pending';

    $order_sql = "
        INSERT INTO tbl_orders (
            user_id,
            customer_name,
            email,
            phone,
            billing_address1,
            billing_address2,
            billing_city,
            billing_state,
            billing_country,
            shipping_address1,
            shipping_address2,
            shipping_city,
            shipping_state,
            subtotal,
            grand_total,
            payment_method,
            payment_status,
            order_status,
            order_note
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $order_stmt = $conn->prepare($order_sql);

    $order_stmt->bind_param(
        "issssssssssssddssss",
        $user_id,
        $customer_name,
        $email,
        $phone,
        $billing_address1,
        $billing_address2,
        $billing_city,
        $billing_state,
        $billing_country,
        $shipping_address1,
        $shipping_address2,
        $shipping_city,
        $shipping_state,
        $subtotal,
        $subtotal,
        $payment_method,
        $payment_status,
        $order_status,
        $order_note
    );

    $order_stmt->execute();
    $order_id = $conn->insert_id;
    $order_stmt->close();

    $item_sql = "
        INSERT INTO tbl_order_items (
            order_id,
            sku_id,
            quantity,
            price
        ) VALUES (?, ?, ?, ?)
    ";

    $item_stmt = $conn->prepare($item_sql);

    foreach ($cart_items as $item) {
        $sku_id = (int) $item['sku_id'];
        $quantity = (int) $item['quantity'];
        $price = (float) $item['value'];

        $item_stmt->bind_param(
            "iiid",
            $order_id,
            $sku_id,
            $quantity,
            $price
        );

        $item_stmt->execute();
    }

    $item_stmt->close();

    $payment_sql = "
        INSERT INTO tbl_payments (
            order_id,
            payment_method,
            amount,
            payment_status
        ) VALUES (?, ?, ?, ?)
    ";

    $payment_stmt = $conn->prepare($payment_sql);

    $payment_stmt->bind_param(
        "isds",
        $order_id,
        $payment_method,
        $subtotal,
        $payment_status
    );

    $payment_stmt->execute();
    $payment_stmt->close();

    if ($payment_method === 'cod') {
        $delete_stmt = $conn->prepare(
            "DELETE FROM cart_items WHERE user_id = ?"
        );

        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    $conn->commit();
    $conn->close();

    if ($payment_method === 'cod') {
        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    }

    header("Location: payment.php?order_id=" . $order_id);
    exit;

} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollback_error) {
    }

    error_log("Place order error: " . $e->getMessage());

    if ($conn->connect_errno === 0) {
        $conn->close();
    }

    http_response_code(400);
    exit(
        "Order could not be placed: " .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
    );
}
