
<?php
session_start();

if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['logged-in'])
) {
    header('Location: ../sign-in.php');
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    require_once __DIR__ . '/../config.php';

    $sql = "SELECT
                p.id,
                p.name,
                p.cover,
                p.value,
                sku.sku_id,
                sku.stock_quantity,
                c.color_name,
                s.size_name
            FROM product_skus sku
            JOIN tbl_product p ON sku.product_id = p.id
            JOIN tbl_color c ON c.color_id = sku.color_id
            JOIN tbl_size s ON s.size_id = sku.size_id
            WHERE sku.stock_quantity <= 3";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $lowitems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Something went wrong.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Low Stock Items</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css">

    <link rel="stylesheet" href="style2.css">

    
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-2 col-lg-2 bg-dark text-white sidebar p-3">

            <div class="brand">
                Almuzna
            </div>

            <a href="admindashboard.php">
                <i class="fa-solid fa-gauge me-2"></i>
                Dashboard
            </a>

            <a href="addproducts.php">
                <i class="fa-solid fa-box me-2"></i>
                Products
            </a>

            <a href="orders.php">
                <i class="fa-solid fa-cart-shopping me-2"></i>
                Orders
            </a>

            <a href="customers.php">
                <i class="fa-solid fa-users me-2"></i>
                Customers
            </a>

            <a href="lowstock.php" class="active">
                <i class="fa-solid fa-boxes-stacked me-2"></i>
                Low Stock
            </a>

            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                Logout
            </a>

        </aside>

        <div class="col-md-10 col-lg-10">

            <div class="d-flex justify-content-between align-items-center mt-2 mb-3 px-2 fs-3">
                <i class="fa-solid fa-bars"></i>
                <i class="fa-solid fa-bell"></i>
            </div>

            <div class="d-flex align-items-center justify-content-between px-2">

                <div>
                    <h4>Low Stock Management</h4>
                    <p class="text-secondary">
                        Products with stock quantity of 3 or less
                    </p>
                </div>

                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control ps-5"
                        placeholder="Search products..."
                    >
                </div>

            </div>

            <?php if (isset($_GET['status'])): ?>

                <?php if ($_GET['status'] === 'success'): ?>
                    <div class="alert alert-success mx-2">
                        Stock updated successfully.
                    </div>

                <?php elseif ($_GET['status'] === 'failed'): ?>
                    <div class="alert alert-warning mx-2">
                        Stock was not updated. Check the available quantity.
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger mx-2">
                        Something went wrong with the stock update.
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <div class="row mt-4">

                <?php if (empty($lowitems)): ?>

                    <div class="col-12">
                        <div class="alert alert-info">
                            No low-stock products found.
                        </div>
                    </div>

                <?php else: ?>

                    <?php foreach ($lowitems as $item): ?>

                        <?php
                            $imagePath = $item['cover'];

                            if (strpos($imagePath, 'images/') !== 0) {
                                $imagePath = 'images/' . $imagePath;
                            }
                        ?>

                        <div class="col-md-6 col-lg-4 col-xl-3 mb-4 product-card">

                            <div class="card stock-card">

                                <div class="card-header">

                                    <div class="img-dvv">
                                        <img
                                            src="../<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                    </div>

                                </div>

                                <div class="card-body">

                                    <h5 class="fs-5 product-name">
                                        <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </h5>

                                    <p class="mb-1">
                                        SKU: <?= (int)$item['sku_id'] ?>
                                    </p>

                                    <p class="mb-1">
                                        Color:
                                        <?= htmlspecialchars($item['color_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>

                                    <p class="mb-2">
                                        Size:
                                        <?= htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>

                                    <div>
                                        Current stock:
                                        <span class="stock-number">
                                            <?= (int)$item['stock_quantity'] ?>
                                        </span>
                                        units
                                    </div>

                                </div>

                                <div class="card-footer">

                                    <form action="addstock.php" method="POST">

                                        <input
                                            type="hidden"
                                            name="sku_id"
                                            value="<?= (int)$item['sku_id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                                        >

                                        <div class="quantity-control">

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary minus"
                                            >
                                                −
                                            </button>

                                            <input
                                                type="number"
                                                name="quantity"
                                                class="form-control qty-input"
                                                value="1"
                                                min="1"
                                                max="1000"
                                                required
                                            >

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary plus"
                                            >
                                                +
                                            </button>

                                        </div>

                                        <div class="stock-actions">

                                            <button
                                                type="submit"
                                                name="action"
                                                value="increase"
                                                class="btn btn-dark"
                                            >
                                                Add stock
                                            </button>

                                            <button
                                                type="submit"
                                                name="action"
                                                value="decrease"
                                                class="btn btn-danger"
                                            >
                                                Remove
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<script>
document.querySelectorAll('.quantity-control').forEach(function(control) {

    const minus = control.querySelector('.minus');
    const plus = control.querySelector('.plus');
    const input = control.querySelector('.qty-input');

    plus.addEventListener('click', function() {
        let quantity = parseInt(input.value) || 1;

        if (quantity < 1000) {
            input.value = quantity + 1;
        }
    });

    minus.addEventListener('click', function() {
        let quantity = parseInt(input.value) || 1;

        if (quantity > 1) {
            input.value = quantity - 1;
        }
    });

});

document.querySelectorAll('.qty-input').forEach(function(input) {
    input.addEventListener('change', function() {
        let quantity = parseInt(this.value);

        if (isNaN(quantity) || quantity < 1) {
            this.value = 1;
        } else if (quantity > 1000) {
            this.value = 1000;
        }
    });
});

document.getElementById('searchInput').addEventListener('input', function() {

    const search = this.value.toLowerCase();

    document.querySelectorAll('.product-card').forEach(function(card) {
        const productName = card.querySelector('.product-name').textContent.toLowerCase();

        card.style.display = productName.includes(search) ? '' : 'none';
    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
