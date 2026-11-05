
<?php
session_start();

if (
    empty($_SESSION['logged-in']) ||
    empty($_SESSION['user_id'])
) {
    header("Location: ../login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Access denied.");
}

require_once __DIR__ . '/../config.php';

$productCount = 0;
$customerCount = 0;
$lowStockCount = 0;
$recentProducts = [];

$productResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM tbl_product
     WHERE deleted_at IS NULL"
);

$productCount = (int) $productResult->fetch_assoc()['total'];

$customerResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM tbl_almuzah_register
     WHERE role = 'customer'"
);

$customerCount = (int) $customerResult->fetch_assoc()['total'];

$stockResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM product_skus
     WHERE stock_quantity <= 3"
);

$lowStockCount = (int) $stockResult->fetch_assoc()['total'];

$recentResult = $conn->query(
    "SELECT id, name, value
     FROM tbl_product
     WHERE deleted_at IS NULL
     ORDER BY id DESC
     LIMIT 5"
);


while ($row = $recentResult->fetch_assoc()) {
    $recentProducts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Almuzna Admin Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f6fa;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: #20232e;
        }

        .sidebar a {
            display: block;
            color: #d6d8df;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #343849;
            color: #ffffff;
        }

        .brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .stat-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.04);
        }

        .stat-icon {
            font-size: 27px;
        }

        .main-content {
            padding: 28px;
        }

        .section-card {
            border: none;
            border-radius: 14px;
        }

        .table th {
            color: #6c757d;
            font-weight: 600;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 sidebar p-3">

            <div class="brand mb-4">
                <i class="bi bi-bag-heart"></i> Almuzna
            </div>

            <p class="text-uppercase text-secondary small">
                Main menu
            </p>

            <a href="admindashboard.php" class="active">
                <i class="bi bi-grid me-2"></i>
                Dashboard
            </a>

            <a href="orders.php">
                <i class="bi bi-box-seam me-2"></i>
                Orders
            </a>

            <a href="addproducts.php">
                <i class="bi bi-tags me-2"></i>
                Products
            </a>

            <a href="customers.php">
                <i class="bi bi-people me-2"></i>
                Customers
            </a>
            <a href="low-stock.php">
                <i class="bi bi-tag-fill me-2"></i>
                low-stock

            </a>

            <hr class="border-secondary">

            <a href="../shop.php">
                <i class="bi bi-shop me-2"></i>
                View Store
            </a>

            <a href="../logout.php">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </a>

        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3 mb-4">

                <div>
                    <h2 class="fw-bold mb-1">Dashboard</h2>
                    <p class="text-secondary mb-0">
                        Welcome to your Almuzna store administration.
                    </p>
                </div>

                <a href="../shop.php" class="btn btn-dark">
                    <i class="bi bi-shop me-1"></i>
                    Visit Store
                </a>

            </div>

            <div class="row g-4 mb-4">

                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-secondary mb-2">
                                        Active Products
                                    </p>
                                    <h2 class="fw-bold">
                                        <?= $productCount ?>
                                    </h2>
                                </div>

                                <i class="bi bi-bag stat-icon text-primary"></i>
                            </div>
                            <a href="products.php"
                               class="small text-decoration-none">
                                Manage products
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-secondary mb-2">
                                        Customers
                                    </p>
                                    <h2 class="fw-bold">
                                        <?= $customerCount ?>
                                    </h2>
                                </div>

                                <i class="bi bi-people stat-icon text-success"></i>
                            </div>
                            <a href="customers.php"
                               class="small text-decoration-none">
                                View customers
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-secondary mb-2">
                                        Low-stock Variations
                                    </p>
                                    <h2 class="fw-bold">
                                        <?= $lowStockCount ?>
                                    </h2>
                                </div>

                                <i class="bi bi-exclamation-triangle
                                          stat-icon text-danger"></i>
                            </div>
                            <p class="small text-secondary mb-0">
                                Variations with stock of 3 or less
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card section-card shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between
                                align-items-center mb-3">

                        <h5 class="fw-bold mb-0">Recently Added Products</h5>
                        <div class='d-flex justify-content-between align-items-center mb-3 gap-2'>
                            <a href=".php"
                                class="btn btn-outline-dark btn-sm">
                                View All
                            </a>
                            <a href="addproducts.php"
                                class="btn btn-outline-dark btn-sm">
                                Add products
                            </a>
                        </div>


                    </div>
                    <form method="POST" action="deleteProduct.php" id="productForm">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Delete item</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($recentProducts)): ?>

                                    <tr>
                                        <td colspan="3"
                                            class="text-center text-secondary py-4">
                                            No products found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($recentProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <?= (int) $product['id'] ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $product['name'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                AED
                                                <?= number_format(
                                                    (float) $product['value'],
                                                    2
                                                ) ?>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input product-checkbox" value="<?= $product['id']?>"  name="product_ids[]">
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="selectionButtons" class="d-flex gap-2 mt-3" hidden>
                        <button type="button" id="cancelSelection"
                            class="btn btn-outline-secondary">
                            Cancel
                        </button>

                        <button type="submit" 
                            class="btn btn-danger">
                            Confirm
                        </button>
                    </div>
                    </form>

                </div>
            </div>

            <p class="text-secondary small mt-4">
                Almuzna Admin Panel
            </p>

        </main>

    </div>
</div>
<script>
    
const productForm = document.getElementById('productForm');
const checkboxes = document.querySelectorAll('.product-checkbox');
const selectionButtons = document.getElementById('selectionButtons');
const cancelButton = document.getElementById('cancelSelection');

function updateButtons() {
    const selectedProducts = document.querySelectorAll(
        '.product-checkbox:checked'
    );

    selectionButtons.hidden = selectedProducts.length === 0;
}

checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', updateButtons);
});

cancelButton.addEventListener('click', function () {
    checkboxes.forEach(function (checkbox) {
        checkbox.checked = false;
    });

    updateButtons();
});

productForm.addEventListener('submit', function (event) {
    const selectedProducts = document.querySelectorAll(
        '.product-checkbox:checked'
    );

    if (selectedProducts.length === 0) {
        event.preventDefault();
        return;
    }

    if (!confirm('Are you sure you want to delete the selected products?')) {
        event.preventDefault();
    }
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
