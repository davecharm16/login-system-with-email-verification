<?php  
    ob_start();
    session_start();
    $page_title = "Change Password";
    include('includes/header.php');  
    include('includes/navbar.php'); 
    include('includes/is_logged.php');

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;

    require_once 'vendor/autoload.php';


    if(isset($_GET['token'])){
        $token = $_GET['token'];

        $key = "my_secret_key";
        JWT::$leeway = 60;

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            $conn = new mysqli("localhost", "root", "", "mei");
            $stmt = $conn->prepare("SELECT * FROM users WHERE forgot_token = ? LIMIT 1");

            // bind parameters to the statement
            $stmt->bind_param("s", $tok);

            // set parameters and execute the statement
            $tok = $token;
            $stmt->execute();

            // get the result set as a mysqli_result object
            $result = $stmt->get_result();
            if (!$result->num_rows > 0){
                $_SESSION['status'] = "Expired or Used Link.";
                header('Location: forgot.php');
                $stmt->close();
                $conn->close();
                exit(0);
            }
            $stmt->close();
            $conn->close();
            // echo "JWT is valid.";
        }
        catch (ExpiredException $e) {
            $_SESSION['status'] = 'Your Link has Expired!.';
            header('Location: forgot.php');
            exit(0);
            // echo "Token has expired";
        } catch (Exception $e) {
            $_SESSION['status'] = 'Invalid Password Reset Link.';
            header('Location: forgot.php');
            // echo "Error: " . $e->getMessage();
            exit(0);
        }
    }
    ob_end_flush();
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
                        <h5>Password Reset Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="v-forgot.php" method="POST" id="login-form">
                            <input type="hidden" name="token" value="<?=$token?>">
                            <div class="form-group mb-3">
<?php                       if(isset($_SESSION['password_error'])){ 
?>
                                <h4> <?=$_SESSION['password_error'];?></h4>
<?php                        }
                            unset($_SESSION['password_error']);
?>
                                <label for="password">Enter New Password</label>
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
                                <button type="submit" class="btn btn-primary" name="verify-forgot-btn">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php  include('includes/footer.php'); ?>

        
   