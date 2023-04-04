<?php   
    ob_start();
    session_start();
    $page_title = "Forgot";
    include('includes/header.php');  
    include('includes/navbar.php'); 
    include('includes/is_logged.php');
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require_once 'vendor/autoload.php';

    function sendemail_verify($name, $email, $verify_token){
        $mail = new PHPMailer(true);
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        
            //Server settingss
        $mail->isSMTP();       
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                                             //Send using SMTP
        $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
        $mail->Username   = 'meimitsui123@gmail.com';                     //SMTP username
        $mail->Password   = 'xKfAzCNjn4Xkt2wc';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('meimitsui123@gmail.com', $name);
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);      
        $mail_template = "<h2>You Requested Password Reset for your Account</h2>
                            <h5>Here is the link for your password Reset.</h5>
                            <br/>
                            <a href='http://localhost/acad/verify-forgot.php?token=$verify_token'>Click Me</a>";                           
                            //Set email format to HTML

        $mail->Subject = 'Email Verification';
        $mail->Body    = $mail_template;
        $mail->send();
        // echo 'Message has been sent';
    }


    if(isset($_POST['forgot_btn'])){

        //sanitize data
        $email = htmlspecialchars($_POST['email']);
        if(!strlen($email) > 0){
            $_SESSION['email_error'] = 'Email is required';
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['email_error'] = 'Not a valid Email';
        }
        
        if(!isset($_SESSION['email_error'])){
            //get the user
            // connect to the database
            $conn = new mysqli("localhost", "root", "", "mei");

            // prepare the SQL statement
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");

            // bind parameters to the statement
            $stmt->bind_param("s", $email);

            // set parameters and execute the statement
            $email = $email;
            $stmt->execute();

            // get the result set as a mysqli_result object
            $result = $stmt->get_result();

            // check if there are any rows in the result set
            if ($result->num_rows > 0) {
                $user = [];
                // fetch the data using mysqli_fetch_assoc()
                while ($row = mysqli_fetch_assoc($result)) {
                    $user = $row;
                }
                //validate user password
                if($user['verify_status'] == "0"){
                    $_SESSION['status'] = "Please verify your account first";
                    header('Location: forgot.php');
                    exit(0);
                }
                else if($user['forgot_token'] == "" ||$user['forgot_token'] == "null" ){

                    $payload = [
                        'email' => $user['email'],
                        'exp' => time() + 1800,
                        'nbf' => time() + -30
                    ];

                    // Set the secret key
                    $secret_key = "my_secret_key";

                    // Create the JWT token
                    $jwt = JWT::encode($payload, $secret_key, 'HS256');
                    
                    $stmt = $conn->prepare("UPDATE users SET forgot_token=? WHERE email=?");
                    // Bind parameters to the statement
                    $stmt->bind_param("ss",$jwt, $email);

                    $email = $user['email'];
                    $jwt = $jwt;

                    $stmt->execute();

                    sendemail_verify($user['name'],$user['email'],$jwt);
                    $_SESSION['status'] = 'Password Reset Email Sent';
                }
                else{
                    // echo $user['forgot_token'];
                    $key = "my_secret_key";
                    JWT::$leeway = 60;

                    try {
                        $decoded = JWT::decode($user['forgot_token'], new Key($key, 'HS256'));
                        $decoded_array = (array) $decoded;
                        $current_time = time();
                        $expiration_time = $decoded->exp;
                        if ($expiration_time < $current_time) {
                            echo "The token is expired.";
                        } else {
                            $time_difference = $expiration_time - $current_time; // Calculate the time difference in seconds
                            $time_difference_minutes = round($time_difference / 60); // Calculate the time difference in minutes and round to 2 decimal places
                            $_SESSION['status'] = "You can send another email in " . $time_difference_minutes . "  minutes";
                        }
                        // echo "JWT is valid.";
                    }
                    catch (ExpiredException $e) {
                        $payload = [
                            'email' => $user['email'],
                            'exp' => time() + 1800,
                            'nbf' => time() + -30
                        ];
    
                        // Set the secret key
                        $secret_key = "my_secret_key";
    
                        // Create the JWT token
                        $jwt = JWT::encode($payload, $secret_key, 'HS256');
                        
                        $stmt = $conn->prepare("UPDATE users SET forgot_token=? WHERE email=?");
                        // Bind parameters to the statement
                        $stmt->bind_param("ss",$jwt, $email);
    
                        $email = $user['email'];
                        $jwt = $jwt;
    
                        $stmt->execute();
    
                        sendemail_verify($user['name'],$user['email'],$jwt);
                        $_SESSION['status'] = 'Password Reset Email Sent';

                        // echo "Token has expired";
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
            } else {
                $_SESSION['status'] = "No User is found!";
                header('Location: forgot.php');
                exit(0);
            }

            // close the statement and connection
            $stmt->close();
            $conn->close();
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
                        <h5>Forgot Your Password?</h5>
                    </div>
                    <div class="card-body">
                        <form action="forgot.php" method="POST" id="login-form">
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
                            <div class="form-group">
                                <label>Send Password Reset Link</label>
                                <button type="submit" class="btn btn-primary" name="forgot_btn">Send!</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php  include('includes/footer.php'); ?>

        
   