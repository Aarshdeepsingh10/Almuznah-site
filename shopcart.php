<?php

session_start();

if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] !== true) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "user", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = (int) $_SESSION["user_id"];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['product_id'])) {

    $product_id = (int) $_POST['product_id'];

    $sql = "SELECT cart_id
            FROM tbl_cart
            WHERE user_id = ? AND product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $message = "Product is already in cart";

    } else {

        $sql = "INSERT INTO tbl_cart (user_id, product_id, quantity)
                VALUES (?, ?, 1)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();

        $message = "Product added to cart";
       

    }

    $stmt->close();
}

$sql = "SELECT c.cart_id,
               c.product_id,
               c.quantity,
               p.product_name,
               p.product_image,
               p.price
        FROM tbl_cart c
        JOIN tbl_products p
        ON c.product_id = p.product_id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$cart_items = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Cart</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >

    <link
        rel="stylesheet"
        href="___style_123__.css"
    >
    

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-0">
                <nav class="navbar navbar-expand-lg navbar-light bg-dark ">

    <div class="container-fluid">

        <a class="navbar-brand text-white" href="#">Navbar</a>

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
                    <a class="nav-link active " href="#">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Cart</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Brands</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Customize</a>
                </li>

            </ul>

            <form class="d-flex" role="search">

                <input
                    class="form-control me-2"
                    type="search"
                    placeholder="Search"
                    aria-label="Search"
                >

                <button
                    class="btn btn-outline-success"
                    type="submit"
                >
                    Search
                </button>

            </form>

        </div>

    </div>

</nav>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-8">
                <div class="d-flex align-items-between">
                    <h3>Your Items </h3>
                    <a  href="#" class="text-dark" >Continue shoping</a>
                </div>
            </div>
            
        </div>
    </div>
        <?php if ($message !== "") { ?>

            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <div class="row">

            <?php while ($row = $cart_items->fetch_assoc()) { ?>

                <div class="col-md-4 mb-4">

                    <div class="card">

                        <div class="image-con">

                            <img
                                src="<?php echo htmlspecialchars($row['product_image']); ?>"
                                alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                                class="card-img-top"
                            >

                        </div>

                        <div class="card-body">

                            <h5>
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </h5>

                            <p>
                                Price:
                                <?php echo number_format($row['price'], 0); ?>
                            </p>

                            <p>
                                Quantity:
                                <?php echo (int) $row['quantity']; ?>
                            </p>

                            <form
                                method="POST"
                                action="remove_from_cart.php"
                            >

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?php echo (int) $row['cart_id']; ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Remove item
                                </button>

                            </form>

                        </div>

                    </div>
                    

                </div>
                
            <?php } ?>
                
                <a href="dashboard.php" class="btn btn-light">
                        <button type="button">Go to Dashboard</button>
                </a>

             
            
      

        </div>

    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"
    ></script>

</body>

</html>