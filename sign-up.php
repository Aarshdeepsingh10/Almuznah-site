<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="container-fluid">
    <div class="row">
        <div class="col-12   p-0">
            <nav class="navbar navbar-expand-lg bg-light">
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
                                <a class="nav-link" href="http://localhost/Almuzna/">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Category</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">About-us</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="shop.php">Place Order</a>
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

    <div class="container-fluid">
        <div class="row   bg-body-tertiary text-center ">
            <div class="col-lg-12">
                <div class="signin-card">
                    <h3 class="text-black my-2">Sign-in</h3>
                    <p class=" fs-5 my-3"> Sign in to explore exclusive designs and timeless craftsmanship from Al Muznah Fashion</p>
                 
                    <form class="form-group" action="register.php" method="POST">
                    
                    <label for="username">Name:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your Name..." id="username" name="username">
                    <label for="lastname">LastName:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your LastName..." id="lastname" name="lastname">
                    <label for="contact">contact:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your contact no..." id="contact" name="contact">

                    <label for="email">Email:</label>
                    <input class="form-control text-center" type="email" placeholder="Enter your email..." id="email" name="email">
                    <label for="password">Passowrd:</label>
                    <input class="form-control text-center" type="password" placeholder="Enter your password ..." id="password" name="password">
                    <label for="confirm_password">Re-Enter your Passowrd:</label>
                    <input class="form-control text-center" type="password" placeholder="Re-Enter your password ..." id="confirm_password" name="confirm_password">
                    <button class="btn my-btn" type="submit">Sign-Up</button>
                    <hr>
                    <p>Already have account <a href="sign-in.php">Sign-in</a></p>
                </form>
                </div>
            
            </div>
        </div>
    </div>









    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    

    
    
</body>
</html>