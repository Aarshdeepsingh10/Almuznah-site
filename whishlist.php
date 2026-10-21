
<?php
session_start();

if (empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user", 3307);
$conn->set_charset("utf8mb4");

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT w.wishlist_id, p.id AS product_id, p.name, p.cover, p.value
    FROM tbl_wishlist w
    JOIN tbl_product p ON w.product_id = p.id
    WHERE w.user_id = ? AND p.deleted_at IS NULL
    ORDER BY w.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Almuznah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="shop.php">Almuznah</a>
        <div class="ms-auto d-flex gap-4">
            <a class="nav-link text-light" href="shop.php">Shop</a>
            <a class="nav-link text-light" href="shopcart.php">
                <i class="fa-solid fa-cart-shopping"></i> Cart
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">
        <i class="fa-regular fa-heart text-danger"></i> My Wishlist
    </h2>

    <?php if (empty($wishlist)) { ?>
        <div class="text-center py-5">
            <h4>Your wishlist is empty</h4>
            <p class="text-secondary">Browse our collection and save your favourite products.</p>
            <a href="shop.php" class="btn btn-dark">Continue Shopping</a>
        </div>
    <?php } else { ?>
        <div class="row g-4">
            <?php foreach ($wishlist as $item) { ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card h-100">
                        <a href="productdetails.php?id=<?php echo (int) $item['product_id']; ?>">
                            <img
                                src="images/<?php echo htmlspecialchars($item['cover'], ENT_QUOTES, 'UTF-8'); ?>"
                                class="card-img-top"
                                alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </a>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h5>

                            <p class="fw-bold">
                                $<?php echo htmlspecialchars((string) $item['value'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <div class="mt-auto d-flex gap-2">
                                <a
                                    href="productdetails.php?id=<?php echo (int) $item['product_id']; ?>"
                                    class="btn btn-dark flex-grow-1"
                                >View Product</a>

                                <form action="remove_from_wishlist.php" method="POST">
                                    <input type="hidden" name="wishlist_id" value="<?php echo (int) $item['wishlist_id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger" aria-label="Remove from wishlist">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
