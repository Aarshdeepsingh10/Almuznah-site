
<?php
session_start();



try {
    require_once __DIR__ . '/config.php';
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Something went wrong. Please try again later.");
}


$id = $_GET['id'] ?? $_GET['product_id'] ?? null;

if ($id === null || !ctype_digit((string) $id) || (int) $id < 1) {
    http_response_code(400);
    exit("Invalid product ID.");
}

$id = (int) $id;


$stmt = $conn->prepare("
    SELECT *
    FROM tbl_product
    WHERE id = ?
      AND deleted_at IS NULL
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    exit("Product not found.");
}

$product = $result->fetch_assoc();
$stmt->close();



$stmt = $conn->prepare("
    SELECT DISTINCT
        c.color_id,
        c.color_name
    FROM product_skus ps
    JOIN tbl_color c
        ON ps.color_id = c.color_id
    WHERE ps.product_id = ?
      AND ps.stock_quantity > 0
    ORDER BY c.color_id
");

$stmt->bind_param("i", $id);
$stmt->execute();

$colors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();



$stmt = $conn->prepare("
    SELECT DISTINCT
        s.size_id,
        s.size_name
    FROM product_skus ps
    JOIN tbl_size s
        ON ps.size_id = s.size_id
    WHERE ps.product_id = ?
      AND ps.stock_quantity > 0
    ORDER BY FIELD(s.size_name, 'S', 'M', 'L', 'XL')
");

$stmt->bind_param("i", $id);
$stmt->execute();

$sizes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Fetch SKU combinations using IDs only.
$stmt = $conn->prepare("
    SELECT
        sku_id,
        color_id,
        size_id,
        stock_quantity
    FROM product_skus
    WHERE product_id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$sku_options = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$sku_json = json_encode(
    $sku_options,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product Details</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
    >

    <link rel="stylesheet" href="style2.css">
</head>

<body>

<!-- Navigation -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">

            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">

                    <a class="navbar-brand" href="shop.php">
                        Almuznah
                    </a>

                    <button
                        class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div
                        class="collapse navbar-collapse"
                        id="navbarSupportedContent"
                    >

                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="shop.php">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Category</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">About-us</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Place Order</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Track order</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fa-regular fa-heart"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="shopcart.php">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fa-regular fa-user"></i>
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>
            </nav>

        </div>
    </div>
</div>


<!-- Product Details -->
<div class="container-fluid">
    <div class="row">

        <!-- Small Product Image -->
        <div class="col-lg-2 col-xl-2 text-center mt-5">

            <div>
                <button class="ups-d" type="button">
                    <i class="fa-solid fa-caret-up"></i>
                </button>
            </div>

            <div class="img-dvv2">
                <img
                    src="images/<?php echo htmlspecialchars(
                        $product['cover'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    alt="Product"
                >
            </div>

        </div>


        <!-- Main Product Image -->
        <div class="col-lg-5 col-xl-5 mt-5">

            <div class="img-dvv3">
                <img
                    src="images/<?php echo htmlspecialchars(
                        $product['cover'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    alt="<?php echo htmlspecialchars(
                        $product['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                >
            </div>

        </div>


        <!-- Product Information -->
        <div class="col-lg-5 col-xl-5 mt-5">

            <h4 class="mb-2">
                Name:
                <?php echo htmlspecialchars(
                    $product['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </h4>

            <h4 class="mb-3">
                Coords, Smart Casual Attire, Party / Formal Attire, New in
            </h4>

            <h4 class="mb-2">
                $<?php echo htmlspecialchars(
                    (string) $product['value'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </h4>

            <p class="text-secondary mb-2">
                <strong>
                    Select a color and size to check availability.
                </strong>
            </p>

            <p class="mb-3">Product Details:</p>


            <!-- Add to Cart Form -->
            <form
                id="cart-form"
                action="add-to-cart.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo (int) $product['id']; ?>"
                >

                <input
                    type="hidden"
                    name="sku_id"
                    id="sku_id"
                    value=""
                >

                <input
                    type="hidden"
                    name="quantity"
                    id="quantity"
                    value="1"
                >


                <!-- Colors -->
                <p><strong>Color:</strong></p>

                <div class="d-flex gap-2 flex-wrap">

                    <?php foreach ($colors as $row) { ?>

                        <span
                            class="dot"
                            data-color-id="<?php echo (int) $row['color_id']; ?>"
                            role="button"
                            tabindex="0"
                            aria-label="<?php echo htmlspecialchars(
                                $row['color_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            title="<?php echo htmlspecialchars(
                                $row['color_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        ></span>

                    <?php } ?>

                    <?php if (empty($colors)) { ?>
                        <p class="text-danger">
                            This product is currently out of stock.
                        </p>
                    <?php } ?>

                </div>


                <!-- Sizes -->
                <p class="mt-3">
                    <strong>Size guide:</strong>
                </p>

                <div class="d-flex gap-2 flex-wrap">

                    <?php foreach ($sizes as $row) { ?>

                        <button
                            type="button"
                            class="my-btn btn btn-secondary"
                            data-size-id="<?php echo (int) $row['size_id']; ?>"
                        >
                            <?php echo htmlspecialchars(
                                $row['size_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </button>

                    <?php } ?>

                </div>


                <!-- Quantity and Add to Cart -->
                <div class="adddf mt-3">

                    <!-- <a href="#">
                        <img
                            src="images/product-fashion.png"
                            alt="Product"
                        >
                    </a> -->
                    <a href="wishlist_action.php?product_id=<?php echo (int) $product['id']; ?>">
                        <img
                            src="images/product-fashion.png"
                            alt="Product"
                        >
                    </a>
                    <div
                        class="d-inline-flex align-items-center ms-2 py-2 px-2 border"
                    >

                        <button
                            type="button"
                            id="minus"
                            class=" btn minus"
                        >
                            -
                        </button>

                        <input
                            id="input"
                            type="number"
                            class="qty-input"
                            value="1"
                            min="1"
                            required
                        >

                        <button
                            type="button"
                            id="plus"
                            class="btn plus"
                        >
                            +
                        </button>

                    </div>

                    <button
                        type="submit"
                        class="btn-carts"
                        id="add-to-cart-button"
                        disabled
                    >
                        <span>Add to cart</span>
                        <i class="fa-solid fa-bag-shopping"></i>
                    </button>

                </div>

                <p id="sku-message" class="mt-2 text-secondary"></p>

            </form>

        </div>

    </div>
</div>


<!-- Pass SKU data to JavaScript -->
<script>
    window.skuOptions = <?php echo $sku_json ?: '[]'; ?>;
</script>

<script src="productdetails.js?v=2"></script>

</body>
</html>
