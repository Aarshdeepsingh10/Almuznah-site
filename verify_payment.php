
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
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_POST['order_id']) ||
    empty($_POST['razorpay_order_id']) ||
    empty($_POST['razorpay_payment_id']) ||
    empty($_POST['razorpay_signature']) ||
    !ctype_digit((string) $_POST['order_id'])
) {
    exit("Invalid payment information.");
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/razorpay_config.php';

use Razorpay\Api\Api;

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$user_id = (int) $_SESSION['user_id'];
$order_id = (int) $_POST['order_id'];

$razorpay_order_id = (string) $_POST['razorpay_order_id'];
$razorpay_payment_id = (string) $_POST['razorpay_payment_id'];
$razorpay_signature = (string) $_POST['razorpay_signature'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "SELECT o.order_id, o.grand_total, o.payment_status,
                p.payment_id, p.razorpay_order_id, p.payment_status AS record_status
         FROM tbl_orders o
         JOIN tbl_payments p ON p.order_id = o.order_id
         WHERE o.order_id = ?
           AND o.user_id = ?
           AND o.payment_method = 'razorpay'
           AND p.payment_method = 'razorpay'
         FOR UPDATE"
    );

    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$record) {
        throw new Exception("Order or payment record not found.");
    }

    if ($record['payment_status'] === 'paid' ||
        $record['record_status'] === 'paid') {
        $conn->commit();
        $conn->close();
        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    }

    if (
        $record['payment_status'] !== 'pending' ||
        $record['record_status'] !== 'pending' ||
        empty($record['razorpay_order_id']) ||
        !hash_equals($record['razorpay_order_id'], $razorpay_order_id)
    ) {
        throw new Exception("Payment does not match this pending order.");
    }

    $api = new Api($keyId, $keySecret);

    $api->utility->verifyPaymentSignature([
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ]);

    $payment = $api->payment->fetch($razorpay_payment_id);

    if (
        $payment['order_id'] !== $razorpay_order_id ||
        $payment['status'] !== 'captured'
    ) {
        throw new Exception("Payment has not been captured.");
    }

    $expectedAmount = (int) round((float) $record['grand_total'] * 100);

    if (
        (int) $payment['amount'] !== $expectedAmount ||
        $payment['currency'] !== 'AED'
    ) {
        throw new Exception("Payment amount or currency does not match.");
    }

    $stmt = $conn->prepare(
        "UPDATE tbl_payments
         SET transaction_id = ?,
             razorpay_signature = ?,
             payment_status = 'paid'
         WHERE payment_id = ?
           AND payment_status = 'pending'"
    );

    $stmt->bind_param(
        "ssi",
        $razorpay_payment_id,
        $razorpay_signature,
        $record['payment_id']
    );
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
        $stmt->close();
        throw new Exception("Payment record could not be updated.");
    }

    $stmt->close();

    $stmt = $conn->prepare(
        "UPDATE tbl_orders
         SET payment_status = 'paid',
             order_status = 'processing'
         WHERE order_id = ?
           AND user_id = ?
           AND payment_status = 'pending'"
    );

    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
        $stmt->close();
        throw new Exception("Order status could not be updated.");
    }

    $stmt->close();

    $stmt = $conn->prepare(
        "DELETE FROM cart_items WHERE user_id = ?"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $conn->close();

    header("Location: order_success.php?order_id=" . $order_id);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    $conn->close();

    error_log("Razorpay verification failed for order {$order_id}: " . $e->getMessage());

    http_response_code(400);
    exit("Payment could not be verified. Your order has not been marked as paid. Please contact support if money was deducted.");
}
