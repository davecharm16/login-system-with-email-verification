<?php 
    session_start();

    function validate_fields($email, $password){
        $_SESSION['errors'] = [];
        //validate form
        if(!strlen($email) > 0){
            $_SESSION['email_error'] = 'Email is required';
            $_SESSION['errors'] = "email error";
        }

        if(!strlen($password) > 0){
            $_SESSION['password_error'] = 'Password is required';
            $_SESSION['errors'] = "password error";
        }

        if(count($_SESSION['errors']) > 0){
            header('Location: login.php');
            exit(0);
        }
    }

    if(isset($_POST['login_btn'])){
        //sanitize and get the data
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        validate_fields($email, $password);

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
            if($user['password'] != md5($password)){
                $_SESSION['status'] = "Wrong Email or Password.";
                header('Location: login.php');
                exit(0);
            }
            else{
                header('Location: index.php');
                $_SESSION['logged_in'] = true;
                exit(0);
            }
        } else {
            $_SESSION['status'] = "No User is found!";
            header('Location: login.php');
            exit(0);
        }

        // close the statement and connection
        $stmt->close();
        $conn->close();

    }
    else{
        header('Location: login.php');
    }
?>