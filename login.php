<?php  
    session_start();
    $page_title = "Login";
    include('includes/header.php');  
    include('includes/navbar.php'); 
    include('includes/is_logged.php');
 ?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

<?php 
                if(isset($_SESSION['status'])){
?>
                    <div class="alert alert-success">
                        <h5> <?= $_SESSION['status']; ?></h5>
                    </div>
<?php               
                    unset($_SESSION['status']);
                    unset($_SESSION['errors']);
                } 
?>
                <div class="card">
                    <div class="card-header">
                        <h5>Login Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="login-validate.php" method="POST" id="login-form">
                            <div class="form-group mb-3">
<?php                       if(isset($_SESSION['email_error'])){ 
?>
                                <h4> <?=$_SESSION['email_error'];?></h4>
<?php                        }
                            unset($_SESSION['email_error']);
?>
                                <label for="email">Email Address</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="form-group mb-3">
<?php                       if(isset($_SESSION['password_error'])){ 
?>
                                <h4> <?=$_SESSION['password_error'];?></h4>
<?php                        }
                            unset($_SESSION['password_error']);
?>
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="login_btn">Login</button>
                                <a href="/acad/forgot.php">Forgot Your Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php  include('includes/footer.php'); ?>

        
   