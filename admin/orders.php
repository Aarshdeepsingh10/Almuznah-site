
<?php
session_start();

if (
    empty($_SESSION['logged-in']) ||
    empty($_SESSION['user_id'])
) {
    header("Location: ../login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Access denied.");
}

require_once __DIR__ . '/../config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$orders = [];
$errorMessage = '';

try {
    $sql = "
        SELECT
            order_id,
            user_id,
            customer_name,
            email,
            phone,
            grand_total,
            payment_method,
            payment_status,
            order_status,
            created_at
        FROM tbl_orders
        ORDER BY order_id DESC
    ";

    $result = $conn->query($sql);
    $orders = $result->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    error_log("Orders page error: " . $e->getMessage());
    $errorMessage = "Unable to load orders. Please check the database connection.";
}

function escape($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$totalOrders = count($orders);
$pendingOrders = 0;
$deliveredOrders = 0;
$totalRevenue = 0;

foreach ($orders as $order) {
    if (strtolower($order['order_status']) === 'pending') {
        $pendingOrders++;
    }

    if (strtolower($order['order_status']) === 'delivered') {
        $deliveredOrders++;
    }

    $totalRevenue += (float) $order['grand_total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Orders - Almuznah Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f6fa;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: #20232e;
            color: white;
            min-height: 100vh;
        }

        .brand {
            font-size: 25px;
            font-weight: bold;
            padding: 20px 10px;
        }

        .sidebar a {
            display: block;
            color: #d6d7df;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 5px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #373b4b;
            color: white;
        }

        .main-content {
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stat-number {
            font-size: 25px;
            font-weight: bold;
        }

        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
        }

        .table th {
            white-space: nowrap;
            background: #f8f9fc;
        }

        .table td {
            vertical-align: middle;
        }

        .customer-email {
            font-size: 12px;
            color: #777;
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

            <div class="brand">
                <i class="fa-solid fa-store"></i> E-con
            </div>

            <a href="admindashboard.php">
                <i class="fa-solid fa-gauge me-2"></i>
                Dashboard
            </a>

            <a href="addproducts.php">
                <i class="fa-solid fa-box me-2"></i>
                Products
            </a>

            <a href="orders.php" class="active">
                <i class="fa-solid fa-cart-shopping me-2"></i>
                Orders
            </a>

            <a href="customers.php">
                <i class="fa-solid fa-users me-2"></i>
                Customers
            </a>

            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                Logout
            </a>

        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="mb-4">
                <h2 class="fw-bold">Orders</h2>
                <p class="text-muted">
                    View and manage customer orders.
                </p>
            </div>

            <?php if ($errorMessage !== ''): ?>

                <div class="alert alert-danger">
                    <?= escape($errorMessage) ?>
                </div>

            <?php else: ?>

                <div class="row g-3 mb-4">

                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <p class="text-muted mb-2">Total Orders</p>
                                <div class="stat-number">
                                    <?= $totalOrders ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <p class="text-muted mb-2">Pending Orders</p>
                                <div class="stat-number text-warning">
                                    <?= $pendingOrders ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <p class="text-muted mb-2">Delivered Orders</p>
                                <div class="stat-number text-success">
                                    <?= $deliveredOrders ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <p class="text-muted mb-2">Order Value</p>
                                <div class="stat-number">
                                    AED <?= number_format($totalRevenue, 2) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card table-card">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">All Orders</h5>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                        <th>Order Status</th>
                                        <th>Date</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php if (empty($orders)): ?>

                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fa-solid fa-box-open fa-2x text-muted mb-3"></i>
                                            <p class="mb-0">No orders found.</p>
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($orders as $order): ?>

                                        <?php
                                        $orderStatus = strtolower($order['order_status']);
                                        $paymentStatus = strtolower($order['payment_status']);

                                        $orderBadge = match ($orderStatus) {
                                            'delivered' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger',
                                            'shipped' => 'info',
                                            'processing' => 'primary',
                                            default => 'secondary'
                                        };

                                        $paymentBadge = match ($paymentStatus) {
                                            'paid', 'completed', 'success' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'info',
                                            default => 'secondary'
                                        };
                                        ?>

                                        <tr>
                                            <td>
                                                #<?= (int) $order['order_id'] ?>
                                            </td>

                                            <td>
                                                <div class="fw-semibold">
                                                    <?= escape($order['customer_name']) ?>
                                                </div>
                                                <div class="customer-email">
                                                    <?= escape($order['email']) ?>
                                                </div>
                                            </td>

                                            <td class="fw-semibold">
                                                AED <?= number_format((float) $order['grand_total'], 2) ?>
                                            </td>

                                            <td>
                                                <?= escape($order['payment_method']) ?>
                                            </td>

                                            <td>
                                                <span class="badge text-bg-<?= $paymentBadge ?>">
                                                    <?= escape(ucfirst($paymentStatus)) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge text-bg-<?= $orderBadge ?>">
                                                    <?= escape(ucfirst($orderStatus)) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= escape(date('d M Y', strtotime($order['created_at']))) ?>
                                            </td>

                                            <td>
                                                <a
                                                    class="btn btn-sm btn-outline-primary"
                                                    href="order-details.php?id=<?= (int) $order['order_id'] ?>"
                                                >
                                                    View
                                                </a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
