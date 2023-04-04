<?php 
    include('dbcon.php');
    session_start();

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
 
    require 'vendor/autoload.php';

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
        $mail_template = "<h2>You have Registered on My Website</h2>
                            <h5>Verify your address to the given link below.</h5>
                            <br/>
                            <a href='http://localhost/acad/verify-email.php?token=$verify_token'>Click Me</a>";                           
                            //Set email format to HTML

        $mail->Subject = 'Email Verification';
        $mail->Body    = $mail_template;
        $mail->send();
        // echo 'Message has been sent';
    }

    function verify_fields($name, $password, $email, $confirm_password){
        $_SESSION['error_count'] = 0;
        if($name == ""){
            $_SESSION['name_error'] = 'Name is Required.';
            $_SESSION['error_count'] = 1;
        }else{
            if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
                $_SESSION['name_error'] = 'Not a valid Name.';
                $_SESSION['error_count'] = 1;
            }
        }

        if($email == ""){
            $_SESSION['email_error'] = 'Email is Required.';
            $_SESSION['error_count'] = 1;
        }else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['email_error'] = 'Not a valid Email';
                $_SESSION['error_count'] = 1;
            } 
        }

        if($password == ""){
            $_SESSION['password_error'] = 'Password is Required.';
            $_SESSION['error_count'] = 1;
        }else{
            if(strlen($password) < 8 || strlen($password) > 16){
                $_SESSION['password_error'] = 'Password should be 8 - 16 characters in length';
                $_SESSION['error_count'] = 1;
            }
        }

        if($confirm_password == ""){
            $_SESSION['confirm_password_error'] = 'Confirm Password is Required.';
            $_SESSION['error_count'] = 1;  
        }
        else{
            if ($password != $confirm_password){
                $_SESSION['confirm_password_error'] = 'Passwords do not Match';
                $_SESSION['error_count'] = 1;
            }
        }

        if($_SESSION['error_count'] == 1){
            return true;
        }
        else{
            return false;
        }
    }


    if(isset($_POST['register_btn'])){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $verify_token = md5(rand());

        //sanitize the data to prevent sql injection
        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $password = htmlspecialchars($password);
        $confirm_password = htmlspecialchars($confirm_password);

        if(verify_fields($name, $password, $email, $confirm_password)){
            header('Location: register.php');
            exit(0);
        }

        //check if Email Exists
        $check_email_query = "SELECT email from users WHERE email = '$email' LIMIT 1; ";
        $check_email_query_run = mysqli_query($con, $check_email_query);

        if(mysqli_num_rows($check_email_query_run) > 0) {
            $_SESSION['status'] = "Email Already Exist.";
            header("Location: register.php");
        }
        else{
            //encrypt Password
            $pass = md5($password);
            //query
            $query = "INSERT INTO users (`userId`, `name`, `email`, `password`, `verify_token`, `created_at`) VALUES (NULL, '$name', '$email', '$pass', '$verify_token', current_timestamp())";

            $query_run = mysqli_query($con,$query);

            if($query_run){
                sendemail_verify("$name", "$email", "$verify_token");
                $_SESSION['status'] = "Registration Success.! Please Verify Your Email Address.";
                header("Location: register.php");
            }
            else{
                $_SESSION['status'] = "Registration Failed!";
                header("Location: register.php");
            }
        }
    }
    else{
        header('Location: register.php');
    }
    
?>