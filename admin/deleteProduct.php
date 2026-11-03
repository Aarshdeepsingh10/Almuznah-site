
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

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admindashboard.php');
    exit();
}

$product_ids = $_POST['product_ids'] ?? [];

if (!is_array($product_ids) || empty($product_ids)) {
    header('Location: admindashboard.php');
    exit();
}

$product_ids = array_unique($product_ids);

foreach ($product_ids as $id) {
    if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0) {
        header('Location: admindashboard.php');
        exit();
    }
}

$product_ids = array_map('intval', $product_ids);

try {
    $conn->begin_transaction();

    $skuStmt = $conn->prepare(
        'DELETE FROM product_skus WHERE product_id = ?'
    );

    $productStmt = $conn->prepare(
        'DELETE FROM tbl_product WHERE id = ?'
    );

    
    $cartStmt = $conn->prepare(
    'DELETE FROM cart_items
     WHERE sku_id IN (
         SELECT sku_id
         FROM product_skus
         WHERE product_id = ?
     )'
    );

    foreach ($product_ids as $product_id) {
        $cartStmt->bind_param('i',$product_id);
        $cartStmt->execute();

        $skuStmt->bind_param('i', $product_id);
        $skuStmt->execute();

        $productStmt->bind_param('i', $product_id);
        $productStmt->execute();

        
    }
    $cartStmt->close();
    $skuStmt->close();
    $productStmt->close();
    

    $conn->commit();

    header('Location: admindashboard.php');
    exit();

} catch (Throwable $e) {
    $conn->rollback();
    error_log($e->getMessage());

    http_response_code(500);
    exit('Could not delete the selected products.');
}
