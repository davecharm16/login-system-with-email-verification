<?php
    session_start();
    $page_title = "Register";
    include('includes/header.php'); 
    include('includes/navbar.php'); 
    include('includes/is_logged.php');

 ?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="alert">
<?php                       if(isset($_SESSION['status'])){
                             echo "<h4>".$_SESSION['status']."</h4>";
                            }
                            unset($_SESSION['status']);
                            unset($_SESSION['error_count']);
?>
                    </div>
                    <div class="card-header">
                        <h5>Registration Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="code.php" method="POST" id="reg_form">
                            <div class="form-group mb-3">
<?php                       if(isset($_SESSION['name_error'])){ 
?>
                                <h4> <?=$_SESSION['name_error'];?></h4>
<?php                        }
                            unset($_SESSION['name_error']);
?>
                                <label for="name">Name</label>
                                <input type="text" name= "name" class="form-control">
                            </div>
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
                            <div class="form-group mb-3">
<?php                       if(isset($_SESSION['confirm_password_error'])){ 
?>
                                <h4> <?=$_SESSION['confirm_password_error'];?></h4>
<?php                        }
                            unset($_SESSION['confirm_password_error']);
?>
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" name="register_btn" class="btn btn-primary">Register Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php  include('includes/footer.php'); ?>