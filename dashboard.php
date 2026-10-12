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
$sql = "SELECT * FROM tbl_products WHERE status = 1";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

?>



<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>




<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css"
    integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
>




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


      

        <div class="col-lg-3 d-none d-lg-block bg-dark vh-100 sticky-top text-center ">


           

            <div class=" image-con2 d-flex ms-2 user-img py-3">

                <img
                    class="img-fluid"
                    src="images/userfordash.png"
                    alt="user image"
                >

            </div>


            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-solid fa-bars"></i>
                </span>

                <p class="m-0">
                    Menu
                </p>

            </div>


           

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-solid fa-house"></i>
                </span>

                <p class="m-0">
                    Dashboard
                </p>

            </div>


            

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-regular fa-user"></i>
                </span>

                <p class="m-0">
                    Account
                </p>

            </div>


           

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-solid fa-location-dot"></i>
                </span>

                <p class="m-0">
                    Location
                </p>

            </div>


           

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-regular fa-message"></i>
                </span>

                <p class="m-0">
                    Chat
                </p>

            </div>


          

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-regular fa-star"></i>
                </span>

                <p class="m-0">
                    Favourite
                </p>

            </div>


          

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-solid fa-gear"></i>
                </span>

                <p class="m-0">
                    Gears
                </p>

            </div>


            

            <div
                class="d-flex gap-4 mt-5 ms-2 justify-content-start align-items-center text-white"
            >

                <span>
                    <i class="fa-solid fa-lock"></i>
                </span>

                <p class="m-0">
                    Privacy
                </p>

            </div>


            

            <div class="d-flex mt-3 ms-2 ">

                <a
                    href="logout.php"
                    class="btn btn-danger mt-3"
                >
                    Logout
                </a>

            </div>


        </div>


       
        <div class="col-lg-9 col-12">


          

            <nav class="navbar navbar-expand-lg bg-body-tertiary">


                <div class="container-fluid">


                   

                    <a
                        class="navbar-brand"
                        href="#"
                    >
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

                                <a
                                    class="nav-link active"
                                    aria-current="page"
                                    href="#"
                                >

                                    <i class="fa-solid fa-house"></i>

                                </a>

                            </li>


                           

                            <li class="nav-item dropdown">

                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    Dropdown
                                </a>


                                <ul class="dropdown-menu">

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="#"
                                        >
                                            Action
                                        </a>

                                    </li>


                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="#"
                                        >
                                            Another action
                                        </a>

                                    </li>


                                    <li>

                                        <hr class="dropdown-divider">

                                    </li>


                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="#"
                                        >
                                            Something else here
                                        </a>

                                    </li>

                                </ul>

                            </li>


                        </ul>


                        

                        <form
                            class="d-flex"
                            role="search"
                        >

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


                       

                        <ul
                            class="navbar-nav ms-auto mb-2 mb-lg-0"
                        >


                           

                            <li class="nav-item">

                                <a
                                    class="nav-link active"
                                    href="#"
                                >

                                    <i class="fa-solid fa-cart-shopping"></i>

                                </a>

                            </li>


                            

                            <li class="nav-item">

                                <a
                                    class="nav-link active"
                                    href="#"
                                >

                                    <i class="fa-solid fa-heart"></i>

                                </a>

                            </li>


                           

                            <li class="nav-item">

                                <a
                                    class="nav-link active"
                                    href="#"
                                >

                                    <i class="fa-regular fa-user"></i>

                                </a>

                            </li>


                        </ul>


                    </div>


                </div>


            </nav>


            

            

<div class="container mt-4">
    <div class="row">

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div class="col-lg-4 mx-auto my-4">
                <div class="card">
                    <div class="image-con">
                        <img
                            src="<?php echo htmlspecialchars($row['product_image']); ?>"
                            class="card-img-top img-fluid"
                            alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                        >
                    </div>
                    

                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($row['product_name']); ?>
                        </h5>
                        <p class="card-text-secondary">
                            <?php echo htmlspecialchars($row['product_description']); ?>
                        </p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center card-footer">
                        <div>
                            <i class="fa-solid fa-dollar-sign"></i>
                            <strong><?php echo number_format($row['price'], 0); ?></strong>
                        </div>
                        <div>
                            <form action="shopcart.php" method="POST">
                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?php echo $row["product_id"]; ?>"
                                >
                                
                                <button type="submit" class="btn btn-primary">
                                    Add to Cart
                                </button>
                                
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        <?php } ?>

    </div>
</div>


        </div>


    </div>

</div>




<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"
></script>

</body>

</html>

