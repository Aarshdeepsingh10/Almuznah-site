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

            
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="http://localhost/Almuzna/">
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

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/Almuzna/">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Category
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        About-us
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Track Order
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="whishlist.php">
                        <i class="fa-regular fa-heart"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/Almuzna/shopcart.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                </li>

                <li class="nav-item user-menu">
                    <a class="nav-link" href="#" aria-label="User menu">
                        <i class="fa-regular fa-user"></i>
                    </a>

                    <div class="user-dropdown">
                        <a href="sign-in.php">Sign-in</a>
                        <a href="sign-up.php">Sign-up</a>
                    </div>
                </li>

            </ul>

        </div>
    </div>
</nav>


        </div>
    </div>
</div>

<div class="container">
    <div class="row">

        <div class="col-lg-12 text-center mt-5 mb-3">
            <h3>shop</h3>
        </div>

        <div class="col-lg-3 border">
            <form method="GET" action="productdetails.php">
                <div class="d-flex flex-column">

                <div class="d-flex justify-content-between align-items-center my-2">
                    <p class="fs-5 m-0">filters</p>
                    <span class="fs-5">reset</span>
                </div>

                <div class="d-flex justify-content-start align-items-center my-2">
                    <span class="fs-5">search</span>
                </div>

                <div class="d-flex justify-content-start align-items-center my-2">
                    <input type="text" placeholder="Search Product of...">
                </div>

                <div class="d-flex justify-content-start align-items-center my-2">
                    <span class="fs-5">All Categories</span>
                </div>

                <div class="d-flex justify-content-start align-items-center custom-select-wrapper my-2">
                    <select class="custom-select">
                        <option value="" disabled selected>All categories</option>
                        <option value="1">Option One</option>
                        <option value="2">Option Two</option>
                        <option value="3">Option Three</option>
                    </select>
                </div>

                <div class="d-flex justify-content-start align-items-center my-2">
                    <span class="fs-5">colors</span>
                </div>

                <div class="color-filter">
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-navy"></div>
                        <span>Navy Blue</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-red"></div>
                        <span>Red</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-green"></div>
                        <span>Green</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-gold"></div>
                        <span>Gold</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-red"></div>
                        <span>Red</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-green"></div>
                        <span>Green</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-gold"></div>
                        <span>Gold</span>
                    </a><a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-brightred"></div>
                        <span>Bright Red</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-orange"></div>
                        <span>Orange</span>
                    </a>
                    <a href="#" class="color-item d-flex align-items-center gap-1 text-decoration-none text-black">
                        <div class="c-grey"></div>
                        <span>Grey</span>
                    </a>

                </div>
                <div class="mb-3">
                    <label class="form-label small h-500">Size</label>
                    <select name="size" class="form-select" onchange=this.form.submit() >
                        <option value> All Sizes</option>
                        <option value="1">s</option>
                        <option value="2">m</option>
                        <option value="3">l</option>
                        <option value="4">xl</option>
                        <option value></option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small h-500">Sort by price</label>
                    <select name="size" class="form-select" onchange=this.form.submit() >
                        <option value>Default</option>
                        <option value="low">low</option>
                        <option value="high">high</option>
                        
                    </select>
                </div>
                <button class="btn btn-dark"> Apply this filter</button>
                <button class="btn btn-danger w-50 mt-4 "> <a href="logout.php" class="text-white text-decoration-none">Log out</a></button>




                </div>
            </form>
        </div>

        
        <div class="col-lg-9">
            <div class="row g-3">

                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">

                           <a href="productdetails.php?id=1"> <img src="images/womenfashion1.jpg"> </a>
                        </div>
                        <a>
                            <h5 class="text-center">LEF220670 - SHORT SET </h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $67
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">
                            <a href="productdetails.php?id=2"><img src="images/womenfashion3.jpg"></a>
                        </div>
                        <a>
                            <h5 class="text-center">LEF31685 - SKIRT PANTS</h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $47
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">
                            <a href="productdetails.php?id=3"><img src="images/womenfashion2.jpg"></a>
                        </div>
                        <a>
                            <h5 class="text-center">HBM22 - SET </h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $67
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">
                            <a href="productdetails.php?id=4"><img src="images/womenfashion4.jpg"></a>
                        </div>
                        <a>
                            <h5 class="text-center">HAY3101- FORMAL JACKET</h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $99
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">
                            <a href="productdetails.php?id=5"><img src="images/womenfashion5.jpg"></a>
                        </div>
                        <a>
                            <h5 class="text-center">HE5369 - DRESS</h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $43
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="img-dvv">
                            <a href="productdetails.php?id=6"><img src="images/womenfashion6.jpg"></a>
                        </div>
                        <a>
                            <h5 class="text-center">HBM7 - LS SET</h5>
                        </a>
                        <div class="color-dots">
                            <span class="dot" id="white"></span>
                            <span class="dot" id="light-blue"></span>
                            <span class="dot" id="black"></span>
                            <span class="dot" id="yellow"></span>
                            <span class="dot" id="brown"></span>
                        </div>
                        <div class="text-center">
                            $27
                        </div>
                    </div>

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