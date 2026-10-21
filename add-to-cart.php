
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function showError($message)
{
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add to Cart Error</title>
    </head>
    <body>
        <h2>Add to Cart Failed</h2>
        <pre>' .
        htmlspecialchars($message, ENT_QUOTES, 'UTF-8') .
        '</pre>
        <p><a href="javascript:history.back()">Go back</a></p>
    </body>
    </html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: shop.php");
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$sku_id = filter_input(INPUT_POST, 'sku_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (
    !$product_id ||
    !$sku_id ||
    !$quantity ||
    $product_id < 1 ||
    $sku_id < 1 ||
    $quantity < 1
) {
    showError("Invalid product, SKU, or quantity.");
}

$conn = null;
$transactionStarted = false;

try {
    $conn = new mysqli("localhost", "root", "", "user", 3307);
    $conn->set_charset("utf8mb4");

    $isLoggedIn =
        !empty($_SESSION['logged-in']) &&
        !empty($_SESSION['user_id']);

    /*
     * GUEST CART: Store items in the PHP session.
     */
    if (!$isLoggedIn) {
        $stmt = $conn->prepare(
            "SELECT sku_id, stock_quantity
             FROM product_skus
             WHERE sku_id = ? AND product_id = ?"
        );

        $stmt->bind_param("ii", $sku_id, $product_id);
        $stmt->execute();

        $sku = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$sku) {
            throw new Exception(
                "The selected SKU does not belong to this product."
            );
        }

        $stock = (int) $sku['stock_quantity'];

        if ($stock < 1) {
            throw new Exception("This variation is out of stock.");
        }

        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [];
        }

        $cartKey = (string) $sku_id;

        $existingQuantity =
            isset($_SESSION['guest_cart'][$cartKey])
            ? (int) $_SESSION['guest_cart'][$cartKey]['quantity']
            : 0;

        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > $stock) {
            throw new Exception(
                "Not enough stock. Available: $stock. " .
                "Already in your cart: $existingQuantity."
            );
        }

        $_SESSION['guest_cart'][$cartKey] = [
            'product_id' => (int) $product_id,
            'sku_id' => (int) $sku_id,
            'quantity' => $newQuantity
        ];

        $conn->close();

        header("Location: shopcart.php");
        exit;
    }

    /*
     * LOGGED-IN CART: Keep using the database.
     */
    $user_id = (int) $_SESSION['user_id'];

    $conn->begin_transaction();
    $transactionStarted = true;

    $stmt = $conn->prepare(
        "SELECT sku_id, stock_quantity
         FROM product_skus
         WHERE sku_id = ? AND product_id = ?
         FOR UPDATE"
    );

    $stmt->bind_param("ii", $sku_id, $product_id);
    $stmt->execute();

    $sku = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$sku) {
        throw new Exception(
            "The selected SKU does not belong to this product."
        );
    }

    $stock = (int) $sku['stock_quantity'];

    if ($stock < 1) {
        throw new Exception("This variation is out of stock.");
    }

    $stmt = $conn->prepare(
        "SELECT cart_id, quantity
         FROM cart_items
         WHERE user_id = ? AND sku_id = ?
         FOR UPDATE"
    );

    $stmt->bind_param("ii", $user_id, $sku_id);
    $stmt->execute();

    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $cart_id = (int) $existing['cart_id'];
        $new_quantity = (int) $existing['quantity'] + $quantity;

        if ($new_quantity > $stock) {
            throw new Exception(
                "Not enough stock. Available: $stock. " .
                "Already in cart: " . (int) $existing['quantity'] . "."
            );
        }

        $stmt = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE cart_id = ? AND user_id = ?"
        );

        $stmt->bind_param(
            "iii",
            $new_quantity,
            $cart_id,
            $user_id
        );

        $stmt->execute();
        $stmt->close();

    } else {
        if ($quantity > $stock) {
            throw new Exception(
                "Requested quantity exceeds available stock. " .
                "Available: $stock."
            );
        }

        $stmt = $conn->prepare(
            "INSERT INTO cart_items
                (user_id, sku_id, quantity, created_at)
             VALUES (?, ?, ?, CURRENT_TIMESTAMP)"
        );

        $stmt->bind_param(
            "iii",
            $user_id,
            $sku_id,
            $quantity
        );

        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    $transactionStarted = false;

    $conn->close();

    header("Location: shopcart.php");
    exit;

} catch (Throwable $e) {
    if ($conn instanceof mysqli) {
        if ($transactionStarted) {
            try {
                $conn->rollback();
            } catch (Throwable $ignored) {
            }
        }

        $conn->close();
    }

    showError("Could not add the product to your cart: " . $e->getMessage());
}
?>
