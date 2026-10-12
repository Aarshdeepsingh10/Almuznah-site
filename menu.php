<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand"><img src="images/Mask group.svg" class="img-fluid"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
   

























    <div class="container-fluid">
        <div class="row   bg-body-tertiary text-center ">
            <div class="col-lg-6">

                <h3 class="text-danger my-2"> LARGEST FASHION SHOW</h3>
                <h2 class="my-4">Offers powered<br>by Designers<br> Around the world</h2>
                <p class=" fs-5 my-3"> Dont have account?</p>
                <span><ul> create account <i class="fa-solid fa-arrow-right"></i></ul></span>
                <div class="card  bg-body-tertiary">
                    <div class="card-body text-center">  
                    <form class="form-group" action="register.php" method="POST">
                    
                    <label for="username">Enter your Name:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your Name..." id="username" name="username">
                    <label for="lastname">Enter your LastName:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your LastName..." id="lastname" name="lastname">
                    <label for="contact">Enter your contact:</label>
                    <input class="form-control text-center" type="text" placeholder="Enter your contact no..." id="contact" name="contact">

                    <label for="email">Enter your Email:</label>
                    <input class="form-control text-center" type="email" placeholder="Enter your email..." id="email" name="email">
                    <label for="password">Enter your Passowrd:</label>
                    <input class="form-control text-center" type="password" placeholder="Enter your password ..." id="password" name="password">
                    <label for="confirm_password">Re-Enter your Passowrd:</label>
                    <input class="form-control text-center" type="password" placeholder="Re-Enter your password ..." id="confirm_password" name="confirm_password">
                    
                    <button class="btn btn-primary" type="submit">Sign-In</button>
                </form></div>
                </div>
                
               
            </div>
             
            <div class="col-lg-6  d-flex align-items-center ">

                
                <div class="card  bg-body-tertiary">
                    <div class="card text-bg-dark ">
                        <img src="images/fashion.jpg" class="card-img" alt="...">
                    <div class="card-img-overlay">
                        <div class="card-body py-5">
                            <h5 class="card-title">Login in your account</h5>
                            <form class="form-group" action="login.php" method="POST">
                            <label for="email">Enter your Email:</label>
                           <input class="form-control" type="email" placeholder="Enter your email" id="email" name="email">
                           <label for="password">Enter your Passowrd:</label>
                           <input class="form-control" type="password" placeholder="Enter your password" id="password" name="password">
                          <button class="btn btn-primary" type="submit">Log-In</button>
                           </form>
                        </div>
                        
                   </div>
                    </div>
                </div>
                
               
            </div>
           






        </div>

    </div>









    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    

    
    
</body>
</html>