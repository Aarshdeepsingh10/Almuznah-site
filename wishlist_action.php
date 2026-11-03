
<?php
session_start();

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

$product_id = $_GET['product_id'] ?? '';

if (!ctype_digit((string) $product_id) || (int) $product_id < 1) {
    header("Location: shop.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$product_id = (int) $product_id;



try {
    require_once __DIR__ . '/config.php';
    

    $stmt = $conn->prepare("
        INSERT INTO tbl_wishlist (user_id, product_id)
        SELECT ?, id
        FROM tbl_product
        WHERE id = ? AND deleted_at IS NULL
        ON DUPLICATE KEY UPDATE wishlist_id = wishlist_id
    ");

    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: whishlist.php");
    exit;

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Unable to update wishlist.");
}
