<?php 
    session_start();
    ob_start();
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;

    require_once 'vendor/autoload.php';

    function update_password($password, $confirm_password, $token, $decoded_array){
        if(!strlen($password) > 0){
            $_SESSION['password_error'] = 'Password is required';
        }
        else{
            if(strlen($password) < 8 || strlen($password) > 16){
                $_SESSION['password_error'] = 'Password must be 8-16 characters long!';
            }
        }
        if(!strlen($confirm_password) > 0){
            $_SESSION['confirm_password_error'] = 'Confirm_Password is Required';
        }
        else{
            if($confirm_password != $password){
                $_SESSION['confirm_password_error'] = 'Passwords Do Not Match!';
            }
        }

        if(isset($_SESSION['password_error']) || isset($_SESSION['confirm_password_error'])){
            header('Location: verify-forgot.php?token=' . "$token");
            exit(0);
        }
        else{
            $conn = new mysqli("localhost", "root", "", "mei");

            //encrypt
            $password = md5($password);

            $stmt = $conn->prepare("UPDATE users SET password=?, forgot_token = ? WHERE email=? LIMIT 1");
            // Bind parameters to the statement
            $stmt->bind_param("sss",$pass, $tok, $email);

            $email = $decoded_array['email'];
            $pass = $password;
            $tok = "null";

            $stmt->execute();


            $stmt->close();
            $conn->close();
            $_SESSION['status'] = 'Password Reset Successful!';
            header('Location: login.php');
            exit(0);
        }
    }


    if(isset($_POST['verify-forgot-btn'])){
        $token = $_POST['token'];

        $key = "my_secret_key";
        JWT::$leeway = 60;

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $decoded_array = (array) $decoded;

            $password = htmlspecialchars($_POST['password']);
            $confirm_password = htmlspecialchars($_POST['confirm_password']);
            update_password($password, $confirm_password, $token, $decoded_array);

            //update password here

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