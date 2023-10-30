<?php
require_once "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
$name = $age = $dob = $contact = $username = $password = $confirm_password = "";
$name_err = $age_err = $dob_err = $contact_err = $username_err = $password_err = $confirm_password_err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if name is empty
    if (empty(trim($_POST["name"]))) {
        $name_err = "Name cannot be blank";
    } else {
        $name = trim($_POST["name"]);
    }

    // Check if age is empty
    if (empty(trim($_POST["age"]))) {
        $age_err = "Age cannot be blank";
    } else {
        $age = trim($_POST["age"]);
    }

    // Check if dob is empty
    if (empty(trim($_POST["dob"]))) {
        $dob_err = "Date of birth cannot be blank";
    } else {
        $dob = trim($_POST["dob"]);
    }

    // Check if contact is empty
    if (empty(trim($_POST["contact"]))) {
      $contact_err = "Contact cannot be blank";
    } elseif (strlen(trim($_POST["contact"])) > 10) {
        $contact_err = "Contact number is too long (maximum 10 characters)";
    } else {
        $contact = trim($_POST["contact"]);
    }
  

    // Check if username is empty
    if (empty(trim($_POST["username"]))){
        $username_err = "Username cannot be blank";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST['username']);

            if (mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken"; 
                } else {
                    $username = trim($_POST['username']);
                }
            } else {
                echo "Something went wrong";
            }
        }
    }
    mysqli_stmt_close($stmt);

    // Check for password
    if (empty(trim($_POST['password']))){
        $password_err = "Password cannot be blank";
    } elseif (strlen(trim($_POST['password'])) < 5) {
        $password_err = "Password cannot be less than 5 characters";
    } else {
        $password = trim($_POST['password']);
    }

    // Check for confirm password field
    if (trim($_POST['password']) != trim($_POST['confirm_password'])){
        $password_err = "Passwords should match";
    }

    // Handle image upload
    $image = $_FILES['image'];
    if ($image['error'] === 0) {
        // Check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
          $image_path = "upload/" . basename($image['name']);
            move_uploaded_file($image['tmp_name'], $image_path);
        } else {
            echo "Invalid file format. Please upload a JPG, JPEG, or PNG image.";
        }
    }

    // If there were no errors, go ahead and insert into the database
    if (empty($name_err) && empty($age_err) && empty($dob_err) && empty($contact_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (name, age, username, dob, contact, password, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssss", $param_name, $param_age, $param_username, $param_dob, $param_contact, $param_password, $image_path);
            $param_name = $name;
            $param_age = $age;
            $param_username = $username;
            $param_dob = $dob;
            $param_contact = $contact;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $image_path = isset($image_path) ? $image_path : ""; // Set image path to empty string if no image was uploaded

            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
            } else {
                echo "Something went wrong... cannot redirect!";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href ="navbar.css">
    <link rel="stylesheet" href ="style.css">
    <title>PHP login system!</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id ="navbar">
    <a class="navbar-brand" href="#">Php Register System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="login.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php">Register</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container mt-4" style="max-width: 50vw">
    <h3>Please Register Here:</h3>
    <hr>
    <form action="register.php" method="post" enctype="multipart/form-data"  class ="cons">
    
        <div class="form-group" style=" margin-bottom: 5px">
          <label for="name">Name</label>
          <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
        </div>
      <div class="form-row">
        <div class="form-group col-md-6" style=" margin-bottom: 5px">
          <label for="username">Email</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
        </div>
        <div class="form-group col-md-6" style=" margin-bottom: 5px">
          <label for="age">Age</label>
          <input type="number" class="form-control" name="age" id="age" placeholder="Age" required>
        </div>
      </div>
      <div class="form-group" style=" margin-bottom: 5px">
        <label for="dob">Date of Birth</label>
        <input type="date" class="form-control" name="dob" id="dob" required>
      </div>
      <div class="form-group" style=" margin-bottom: 5px">
        <label for="contact">Contact Number</label>
        <input type="tel" class="form-control" name="contact" id="contact" placeholder="Contact Number" required>
      </div>
      <div class="form-group " style=" margin-bottom: 5px">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
        </div>
      
      <div class="form-group" style=" margin-bottom: 5px">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
      </div>
      <div class="form-group" style=" margin-bottom: 5px">
        <label for="image">Profile Image</label>
        <input type="file" class="form-control-file" name="image" id="image" accept=".jpg, .jpeg, .png" required>
      </div>
    
      <button type="submit" class="btn btn-primary">Sign in</button>
    </form>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
