

<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (
    empty($_SESSION['logged-in']) ||
    empty($_SESSION['user_id'])
) {
    header("Location: login.php");
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'GET' ||
    !isset($_GET['order_id']) ||
    !ctype_digit($_GET['order_id'])
) {
    header("Location: checkout.php");
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/razorpay_config.php';

use Razorpay\Api\Api;

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$user_id = (int) $_SESSION['user_id'];
$order_id = (int) $_GET['order_id'];

$stmt = $conn->prepare(
    "SELECT order_id, grand_total
     FROM tbl_orders
     WHERE order_id = ? AND user_id = ? AND payment_method = 'razorpay'
     AND payment_status = 'pending'"
);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $conn->close();
    exit("Order not found or payment is not pending.");
}

$amount = (float) $order['grand_total'];

if ($amount <= 0) {
    $conn->close();
    exit("Invalid order amount.");
}

/*
Set this to the currency supported by your Razorpay account.
Your displayed prices are currently in AED.
*/
$currency = 'AED';

try {
    $api = new Api($keyId, $keySecret);

    $razorpayOrder = $api->order->create([
        'receipt' => 'order_' . $order_id,
        'amount' => (int) round($amount * 100),
        'currency' => $currency,
        'notes' => [
            'local_order_id' => (string) $order_id,
            'user_id' => (string) $user_id
        ]
    ]);

    $razorpay_order_id = $razorpayOrder['id'];

    $stmt = $conn->prepare(
        "UPDATE tbl_payments
         SET razorpay_order_id = ?
         WHERE order_id = ?
         AND payment_method = 'razorpay'
         AND payment_status = 'pending'"
    );
    $stmt->bind_param("si", $razorpay_order_id, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows < 1) {
        $stmt->close();
        throw new Exception("Payment record could not be updated.");
    }

    $stmt->close();
} catch (Throwable $e) {
    $conn->close();
    exit("Unable to start payment. Check your Razorpay currency and API configuration.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<h2>Complete Your Payment</h2>
<p>Order ID: <?= htmlspecialchars((string) $order_id) ?></p>
<p>Amount: <?= htmlspecialchars(number_format($amount, 2)) ?> <?= htmlspecialchars($currency) ?></p>

<button id="pay-button" type="button">Pay Now</button>

<script>
const options = {
    key: <?= json_encode($keyId) ?>,
    amount: <?= (int) round($amount * 100) ?>,
    currency: <?= json_encode($currency) ?>,
    name: "Almuzna",
    description: "Order #<?= (int) $order_id ?>",
    order_id: <?= json_encode($razorpay_order_id) ?>,

    handler: function (response) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "verify_payment.php";

        const fields = {
            order_id: <?= (int) $order_id ?>,
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature
        };

        for (const name in fields) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = name;
            input.value = fields[name];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    },

    modal: {
        ondismiss: function () {
            alert("Payment was cancelled. Your order is still pending.");
        }
    },

    theme: {
        color: "#3399cc"
    }
};

const razorpay = new Razorpay(options);

document.getElementById("pay-button").addEventListener("click", function () {
    razorpay.open();
});
</script>

</body>
</html>

