<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
  <title>Applicant</title>
	<style>
		body {
			background-color: #f0f0f0;
			color: #333;
			font-family: Arial, sans-serif;
			transition: background-color 0.5s ease;
		}

		.dark-mode {
			background-color: #333;
			color: #f0f0f0;
		}
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background-color: #1E90FF; 
  color: white;
  height: 10px;
}

button {
  background-color: #1E90FF; 
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.5s ease;
}

button:hover {
  background-color: #1E90FF; 
}

.dark-mode button {
  background-color: #f0f0f0;
  color: #333;
}

.dark-mode button:hover {
  color: #ccc;
}

.dark-mode h1 {
  color: #ccc;
}

.buttons{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.buttons > button{
    margin: 0 10px;
}

    main form {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    font-family: Arial, sans-serif;
  }

  h1 {
      margin-top: 5;
      text-align: center;
      color: #000;
    }

  label {
    display: inline-block;
    width: 360px;
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  input[type=text], input[type=file] {
    width: 100%;
    padding: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 16px;
  }
  
  input[type="text"] {
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
      font-size: 16px;
      margin-bottom: 20px;
    }
    input[type="submit"] {
      background-color: #1E90FF;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    input[type="submit"]:hover {
      background-color: #1E90FF;
    }
	</style>
</head>
<body>
	<nav>
		<h1>HRD Applicant Verification System - Applicant Page</h1>
    <div class="buttons">
      <button id="mode-toggle" onclick="toggleMode()">Dark Mode</button>
      <form action="logout.php" method="POST">
          <button name="logout-btn" type="submit">Logout</button>
      </form>
    </div>
	</nav>
  <?php
session_start(); //declare you are starting a session
$_SESSION['id'] = uniqid(); //Assign a value to the id session
include('./includes/auth_session.php');

try {
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $applicantNumber = $_SESSION['id']; // generate unique applicant number
        $target_dir = "resumes/";
        $target_file = $target_dir . basename($_FILES["resume"]["name"]);
        $uploadOk = 1;
        $pdfFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $target_dir = "resumes/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir);
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            throw new Exception("Sorry, file already exists.");
        }

        // Check file size
        if ($_FILES["resume"]["size"] > 10000000) {
            throw new Exception("Sorry, your file is too large.");
        }

        // Allow only PDF files
        if($pdfFileType != "pdf") {
            throw new Exception("Sorry, only PDF files are allowed.");
        }

        if ($uploadOk == 0) {
            throw new Exception("Sorry, your file was not uploaded.");
        }

        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars(basename( $_FILES["resume"]["name"])). " has been uploaded.";
            // save contact information to text file
            $surname = $_POST['last_name']; // get surname from last name field
            $contact = $_POST['contact_number']; // get contact number
            $address = $_POST['address']; // get address
            $filename = $surname . '_' . $applicantNumber . '.txt'; // create filename for contact information text file
            $file = fopen($filename, 'w');
            fwrite($file, "Status: Not Reviewed\n"); // add initial status
            fwrite($file, "Name: {$_POST['first_name']} {$_POST['last_name']}\n"); // concatenate first and last names and add to text file
            fwrite($file, "Contact: {$contact}\n");
            fwrite($file, "Address: {$address}\n");
            fclose($file);

            echo "\nResume uploaded successfully. Your applicant number is {$applicantNumber}.";
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    } else {
        throw new Exception("No file uploaded or an error occurred while uploading the file.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
	<main>
  <form method="POST" enctype="multipart/form-data">
  <label for="first_name">First Name:</label>
  <input type="text" name="first_name" id="first_name" required>
  <br>
  <label for="last_name">Last Name:</label>
  <input type="text" name="last_name" id="last_name" required>
  <br>
  <label for="address">Address:</label>
  <input type="text" name="address" id="address" required>
  <br>
  <label for="contact_number">Contact Number:</label>
  <input type="text" name="contact_number" id="contact_number" required>
  <br>
  <label for="resume">Upload your resume/CV (PDF only, max 10MB):</label>
  <input type="file" name="resume" id="resume" accept="application/pdf" max-size="10485760">
  <br>
  <input type="submit" name="submit" value="Submit">
</form>
	</main>

	<script>
		var modeToggle = document.getElementById("mode-toggle");

		// Check if there is a saved mode in local storage
		var savedMode = localStorage.getItem("mode");
		if (savedMode === "dark") {
			// Set the dark mode class on the body
			document.body.classList.add("dark-mode");
			// Update the button text
			modeToggle.textContent = "Light Mode";
		}

		function toggleMode() {
			var body = document.body;
			var mode = body.classList.contains("dark-mode") ? "light" : "dark";
			body.classList.toggle("dark-mode");
			modeToggle.textContent = mode === "dark" ? "Light Mode" : "Dark Mode";
			// Save the mode in local storage
			localStorage.setItem("mode", mode);
		}
	</script>
</body>
</html>