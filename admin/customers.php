
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

$retrieveusers = [];

$sql = "SELECT
            u.id,
            u.username,
            u.lastname,
            u.email,
            u.status,
            COUNT(DISTINCT o.order_id) AS total_orders,
            COALESCE(SUM(o.grand_total), 0) AS total_spent
        FROM tbl_almuzah_register u
        LEFT JOIN tbl_orders o ON u.id = o.user_id
        WHERE u.role = 'customer'
        GROUP BY
            u.id,
            u.username,
            u.lastname,
            u.email,
            u.status
        ORDER BY u.id DESC";

$activeuser = $conn->query($sql);

while ($row = $activeuser->fetch_assoc()) {
    $retrieveusers[] = $row;
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
    


    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <aside class="col-lg-3 col-xl-2 sidebar p-3" >
                <div class="upper-content">
                    <div class="brand mb-4">
                        <i class="fa-solid fa-bag-shopping" style="color: rgb(255, 255, 255);"></i> Almuzna
                    </div>
                    <a href="admindashboard.php">
                        <i class="fa-solid fa-gauge me-2"></i>
                        Dashboard
                    </a>
                
                    <a href="orders.php">
                        <i class="bi bi-box-seam me-2" style="color: rgb(255, 255, 255);"></i>
                        Orders
                    </a>
                    <a href="addproducts.php">
                       <i class="fa-solid fa-box me-2"></i>
                        Products
                    </a>
                    <a href="../logout.php">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>
                        Logout
                    </a>
                </div>

                <div class="lower-content">
                    <a href="customers.php">
                        <i class="fa-solid fa-users me-2"></i>
                        Customers
                    </a>
                    <a href="../logout.php">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>
                        Logout
                    </a>
                </div>
            </aside>
            <div class="col-xl-10 col-lg-9">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search for.." class="ptdr">
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div>
                            <i class="bi bi-moon-fill"></i>
                            <i class="bi bi-calendar2-x-fill"></i>
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="inline-container">
                            <i class="fa-solid fa-circle-user"></i>
                            <div class="d-flex flex-column">
                                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                                <p class="text-secondary m-0"> Manager</small>
                            </span>
                        </div>
                        <i class="bi bi-chevron-down"></i>

                    </div>
                </div>
               
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div>
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover align-middle">
                                    <thead>
                                       <tr>
                                            <th>Customer name</th>
                                            <th>Status</th>
                                            <th>Total Orders</th>
                                            <th>Total Spent</th>
                                            <th>Actions</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                   <?php foreach ($retrieveusers as $user) { ?>
                                        <tr>
                                            <td >
                                                <strong>
                                                    <?= htmlspecialchars($user['username'] . ' ' . $user['lastname']) ?>
                                                </strong>
                                                <br>
                                                <small class="text-secondary">
                                                   <?= htmlspecialchars($user['email']) ?>
                                                </small>
                                            </td >

                                            <td>
                                                <?= htmlspecialchars($user['status']) ?>
                                            </td>

                                            <td>
                                                <?= (int) $user['total_orders'] ?>
                                            </td>

                                            <td>
                                                AED <?= number_format((float) $user['total_spent'], 2) ?>
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-outline-secondary"
                                                    type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusModal<?= $user['id'] ?>"
                                                 >
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="statusModal<?= $user['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                <form action="update-status.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Customer Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        <input
                                                            type="hidden"
                                                            name="id"
                                                            value="<?= (int) $user['id'] ?>"
                                                        > 

                                                        <label class="form-label">Status</label>

                                                        <select name="status" class="form-select" required>
                                                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>
                                                            Active
                                                            </option>

                                                            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>
                                                            Inactive
                                                            </option>
                                                            <option value="onhold" <?= $user['status'] === 'onhold' ? 'selected' : '' ?>>
                                                            On hold
                                                            </option>
                                                        </select>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Cancel
                                                        </button>

                                                        <button type="submit" class="btn btn-primary">
                                                        Save Changes
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php } ?>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





















    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>