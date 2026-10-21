
<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$isLoggedIn =
    !empty($_SESSION['logged-in']) &&
    !empty($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['remove_guest_item']) &&
    !$isLoggedIn
) {
    $remove_sku_id = filter_input(
        INPUT_POST,
        'sku_id',
        FILTER_VALIDATE_INT
    );

    if ($remove_sku_id && isset($_SESSION['guest_cart'][(string) $remove_sku_id])) {
        unset($_SESSION['guest_cart'][(string) $remove_sku_id]);
    }

    header("Location: shopcart.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$cart_items = [];

if ($isLoggedIn) {
    $user_id = (int) $_SESSION['user_id'];

    $sql = "
        SELECT
            ci.cart_id,
            ci.quantity,
            ps.sku_id,
            c.color_name,
            s.size_name,
            p.id AS product_id,
            p.name,
            p.cover,
            p.value
        FROM cart_items ci
        JOIN product_skus ps ON ci.sku_id = ps.sku_id
        JOIN tbl_product p ON ps.product_id = p.id
        JOIN tbl_color c ON ps.color_id = c.color_id
        JOIN tbl_size s ON ps.size_id = s.size_id
        WHERE ci.user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($cart_items as &$item) {
        $item['is_guest'] = false;
    }
    unset($item);

} else {
    $guest_cart = $_SESSION['guest_cart'] ?? [];

    foreach ($guest_cart as $cart_item) {
        $sku_id = (int) ($cart_item['sku_id'] ?? 0);
        $product_id = (int) ($cart_item['product_id'] ?? 0);
        $quantity = (int) ($cart_item['quantity'] ?? 0);

        if ($sku_id < 1 || $product_id < 1 || $quantity < 1) {
            continue;
        }

        $sql = "
            SELECT
                ps.sku_id,
                c.color_name,
                s.size_name,
                p.id AS product_id,
                p.name,
                p.cover,
                p.value
            FROM product_skus ps
            JOIN tbl_product p ON ps.product_id = p.id
            JOIN tbl_color c ON ps.color_id = c.color_id
            JOIN tbl_size s ON ps.size_id = s.size_id
            WHERE ps.sku_id = ?
              AND ps.product_id = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $sku_id, $product_id);
        $stmt->execute();

        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($item) {
            $item['quantity'] = $quantity;
            $item['cart_id'] = null;
            $item['is_guest'] = true;

            $cart_items[] = $item;
        }
    }
}

$conn->close();

$subtotal = 0;

foreach ($cart_items as $item) {
    $subtotal += (float) $item['value'] * (int) $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css">
    <link rel="stylesheet" href="style2.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="shop.php">Almuznah</a>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="shopcart.php">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8 cart-sdd">
            <h4 class="mb-3">Cart</h4>

            <?php if (empty($cart_items)) { ?>
                <p>Your cart is empty.</p>
                <a href="shop.php">Continue shopping</a>
            <?php } ?>

            <?php foreach ($cart_items as $item) { ?>
                <div class="d-flex gap-3 mb-3">
                    <div class="img-dvv4">
                        <img
                            src="images/<?php echo htmlspecialchars($item['cover'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>

                    <div>
                        <h5>
                            <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </h5>

                        <p class="mb-1">
                            <strong>Size:</strong>
                            <?php echo htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <p class="mb-1">
                            <strong>Color:</strong>
                            <?php echo htmlspecialchars($item['color_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Price:</strong>
                            AED <?php echo number_format((float) $item['value'], 2); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Quantity:</strong>
                            <?php echo (int) $item['quantity']; ?>
                        </p>

                        <?php if ($item['is_guest']) { ?>
                            <form method="POST" action="shopcart.php">
                                <input
                                    type="hidden"
                                    name="sku_id"
                                    value="<?php echo (int) $item['sku_id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="remove_guest_item"
                                    value="1"
                                    class="btn btn-link p-0"
                                    aria-label="Remove item"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        <?php } else { ?>
                            <a href="delete-cart-item.php?cart_id=<?php echo (int) $item['cart_id']; ?>">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        <?php } ?>
                    </div>

                    <div class="flex-grow-1 text-end">
                        AED <?php
                            echo number_format(
                                (float) $item['value'] * (int) $item['quantity'],
                                2
                            );
                        ?>
                    </div>
                </div>

                <hr>
            <?php } ?>
        </div>

        <div class="col-lg-4 cart-sdd">
            <div class="wrap-summary">
                <h4>Order Summary</h4>
                <hr>

                <div class="inner-summ">
                    <p>Subtotal</p>
                    <p>AED <?php echo number_format($subtotal, 2); ?></p>
                </div>

                <div class="inner-summ">
                    <p>Shipping</p>
                    <p>Calculated at checkout</p>
                </div>

                <hr>

                <div class="inner-summ">
                    <strong>Total</strong>
                    <strong>AED <?php echo number_format($subtotal, 2); ?></strong>
                </div>

                <div class="d-flex gap-2 mt-2 sddd">
                    <?php if (!empty($cart_items)) { ?>
                        <a href="checkout.php" class="payment-chekout">
                            Proceed to Checkout
                        </a>
                    <?php } ?>

                    <a href="shop.php" class="payment-chekout">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
