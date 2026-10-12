<?php

   if($_SERVER['REQUEST_METHOD']==="GET"){
    $conn= new mysqli("localhost","root","","user",3307);
    if($conn->connect_error){
        die("connection died" .$conn->connection_error);
    }
    $id = $_GET['id'] ?? $_GET['product_id'] ?? null;

   if ($id === null || !is_numeric($id)) {
        die("Invalid product ID");
}
    $sql="select *from tbl_product where id=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result=$stmt->get_result();
    if ($result->num_rows === 1) {

    $product = $result->fetch_assoc();

   

    

    

    }
   }
?> 
<?php

$product_id = (int) $id;

$sql = "SELECT DISTINCT color
        FROM product_skus
        WHERE product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();

$colors = $stmt->get_result();

?>
<?php
$sql = "SELECT DISTINCT size
        FROM product_skus
        WHERE product_id = ?
        ORDER BY FIELD(size, 'S', 'M', 'L', 'XL')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$sizes = $stmt->get_result();
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
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
        integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >
    <link rel="stylesheet" href="style2.css"> 

</head>
<body>
    <div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">

            <nav class="navbar navbar-expand-lg bg-dark">
                <div class="container-fluid">

                    <a class="navbar-brand" href="#">Almuznah</a>

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
                            <li class="nav-item"><a class="nav-link" aria-current="page" href="#">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Category</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">About-us</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Place Order</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Track order</a></li>
                        </ul>

                        <ul class="navbar-nav mx-auto me-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><i class="fa-regular fa-heart"></i></a></li>
                            <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-cart-shopping"></i></a></li>
                            <li class="nav-item"><a class="nav-link" href="#"><i class="fa-regular fa-user"></i></a></li>
                        </ul>

                    </div>
                </div>
            </nav>

        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-xl-2 text-center mt-5">
            <div>
                <button class="ups-d"><i class="fa-solid fa-caret-up"></i></button>
            </div>
            <div class="img-dvv2"><?php
                echo '<img src="images/' . htmlspecialchars($product['cover']) . '" alt="Product">';
                ?>
            </div>
        </div>
        <div class="col-lg-5 col-xl-5 mt-5">
            <div class="img-dvv3"> <?php
                echo '<img src="images/' . htmlspecialchars($product['cover']) . '" alt="Product">';
                ?>
            </div>
        </div>
        <div class="col-lg-6 col-xl-5 mt-5">
            
            <h4 class="mb-2"><?php echo "Name: " .$product['name']; ?></h4>
            
            <h4 class="mb-3 style="font">Coords, Smart Casual Attire, Party / Formal Attire, New in</h4>
            <h4 class="mb-2"><?php echo "$" .$product['value']; ?></h4>
            <p class="text-secondary mb-2"><strong>200 items left in stock</strong></p>
            <p class="mb-3">Product Details:</p>
            
            <form method="POST" action="add-to-cart.php">
                <input type="hidden" name="product_id" value="<?php echo (int)$product['id']?>">
                <input type="hidden" name="color">
                <input type="hidden" name="size" >
                <input type="hidden" name="color_id">
                <input type="hidden" name="size_id">
                <p>
                    <strong> Color:</strong>
                </p>
                <p>
                    <strong>Color:</strong>
                </p>

                <div class="d-flex gap-2">

                    <?php while ($row = $colors->fetch_assoc()) { ?>

                        <span
                            class="dot"
                            id="<?php echo htmlspecialchars($row['color']); ?>">
                        </span>

                    <?php } ?>
                </div>
                <p class="mt-3">
                    <strong>Size guide:</strong>
                </P>
                
                <div class="d-flex gap-2">
                    <?php while ($row = $sizes->fetch_assoc()) { ?>
                        <button
                            type="button"
                            class="my-btn btn btn-secondary"
                            id="<?php echo htmlspecialchars($row['size']); ?>">
                            <?php echo htmlspecialchars($row['size']); ?>
                      </button>
                    <?php } ?>
                </div>
                <div class="adddf mt-3">
                    <a href="#">
                        <img src="images/product-fashion.png">
                    </a>
                    <div class="d-inline-flex align-items-center ms-2 py-2 px-2 border">
                        <div id="minus" class="minus">-</div>
                        <input id="input" type="text" class="qty-input" value="1" min="1">
                        <div id="plus" class="plus">+</div>
                    </div>
                    <button type="submit" class="btn-carts">
                        <span> Add to carts</span>
                        <i class="fa-solid fa-bag-shopping"></i>
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
   <script src="productdetails.js"></script>
</body>
</html>