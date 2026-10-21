
<?php
session_start();

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// $isLoggedIn=!empty($_SESSION['logged_in']) &&  
// !empty($_SESSION['user_id']);



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$user_id = (int) $_SESSION['user_id'];

$sql = "
    SELECT
        ci.quantity,
        c.color_name,
        s.size_name,
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
$conn->close();

if (empty($cart_items)) {
    header("Location: shopcart.php");
    exit;
}

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
    <title>Checkout - Almuznah</title>

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

<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="shop.php">Almuznah</a>

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

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
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

<div class="container py-5">
    <form action="place_order.php" method="POST" id="checkoutForm">
        <div class="row">
            <div class="col-lg-7">
                <h3 class="mb-4">Checkout</h3>

                <div class="alert alert-info new-customer-inner">
                    <strong>New customer?</strong>
                    Enter your details to continue with your order.
                </div>

                <h4>Billing Address</h4>

                <div class="mb-3">
                    <label for="text" class="form-label">Enter your name:</label>
                    <input type="text" id="text" name="text" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="phone_no" class="form-label">Enter your contact number:</label>
                    <input
                        type="tel"
                        name="phoneno"
                        id="phone_no"
                        class="form-control"
                        maxlength="13"
                        pattern="[0-9+]{9,13}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="address1" class="form-label">Address Line 1:</label>
                    <input type="text" id="address1" name="address1" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="address2" class="form-label">Address Line 2:</label>
                    <input type="text" id="address2" name="address2" class="form-control" required>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <div class="flex-grow-1">
                        <label for="city" class="form-label">City:</label>
                        <input type="text" id="city" name="city" class="form-control" required>
                    </div>

                    <div class="flex-grow-1">
                        <label for="state" class="form-label">State:</label>
                        <input type="text" id="state" name="state" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="country" class="form-label">Country:</label>
                    <input type="text" id="country" name="country" class="form-control" required>
                </div>

                <hr>

                <h5>Shipping Address</h5>

                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        id="sameasbilling"
                        name="same_as_billing"
                        class="form-check-input"
                        checked
                    >
                    <label class="form-check-label" for="sameasbilling">
                        Same as billing address
                    </label>
                </div>

                <div class="shippingFields">
                    <div class="mb-3">
                        <label for="shipping_address_line1" class="form-label">Address Line 1:</label>
                        <input
                            type="text"
                            id="shipping_address_line1"
                            name="shipping_address_line1"
                            class="form-control"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address_line2" class="form-label">Address Line 2:</label>
                        <input
                            type="text"
                            id="shipping_address_line2"
                            name="shipping_address_line2"
                            class="form-control"
                        >
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-grow-1">
                            <label for="shipping_city" class="form-label">City:</label>
                            <input
                                type="text"
                                id="shipping_city"
                                name="shipping_city"
                                class="form-control"
                            >
                        </div>

                        <div class="flex-grow-1">
                            <label for="shipping_state" class="form-label">State:</label>
                            <input
                                type="text"
                                id="shipping_state"
                                name="shipping_state"
                                class="form-control"
                            >
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="order_note" class="form-label">Order Notes (Optional)</label>
                    <textarea
                        id="order_note"
                        name="order_note"
                        class="form-control"
                        placeholder="Add delivery instructions or notes"
                    ></textarea>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="wrap-summary">
                    <h3>Order Summary</h3>
                    <hr>

                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item gap-2">
                            <div class="image-wrapper">
                                <img
                                    src="images/<?php echo htmlspecialchars($item['cover'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="Product"
                                >
                            </div>

                            <div class="sddvv">
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
                    <?php endforeach; ?>

                    <div class="inner-summ mb-2">
                        <p>Subtotal</p>
                        <p>AED <?php echo number_format($subtotal, 2); ?></p>
                    </div>

                    <hr>

                    <div class="inner-summ mb-2">
                        <p>Grand total</p>
                        <p>AED <?php echo number_format($subtotal, 2); ?></p>
                    </div>

                    <hr>

                    <h6>Payment Method</h6>

                    <div class="mb-3">
                        <label class="form-check">
                            <input
                                type="radio"
                                name="payment_method"
                                value="cod"
                                class="form-check-input"
                                required
                            >
                            Cash on delivery
                        </label>

                        <label class="form-check">
                            <input
                                type="radio"
                                name="payment_method"
                                value="razorpay"
                                class="form-check-input"
                                required
                            >
                            Online payment (Razorpay)
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="confirmOrder"
                            name="confirm_order"
                            value="1"
                            required
                        >
                        <label for="confirmOrder" class="form-check-label">
                            I confirm that all information is correct and I want to place this order.
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-light"
                        name="confirm_order"
                        value="1"
                    >
                        Proceed to payment
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const checkbox = document.getElementById("sameasbilling");
    const shippingFields = document.querySelector(".shippingFields");

    const shippingInputs = shippingFields.querySelectorAll("input");

    function toggleShippingFields() {
        const sameAsBilling = checkbox.checked;

        shippingFields.style.display = sameAsBilling ? "none" : "block";

        shippingInputs.forEach(function (input) {
            input.required = !sameAsBilling;
        });
    }

    checkbox.addEventListener("change", toggleShippingFields);
    toggleShippingFields();
</script>

</body>
</html>
