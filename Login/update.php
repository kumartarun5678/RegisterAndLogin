<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Define variables to store user information
$name = $age = $dob = $contact = "";
$name_err = $age_err = $dob_err = $contact_err = "";

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
    if (empty(trim($_POST["contact"]))){
        $contact_err = "Contact cannot be blank";
    } else {
        $contact = trim($_POST["contact"]);
    }

    // If there were no errors, update the user information in the database
    if (empty($name_err) && empty($age_err) && empty($dob_err) && empty($contact_err)) {
        $sql = "UPDATE users SET name = ?, age = ?, dob = ?, contact = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $age, $dob, $contact, $_SESSION['id']);

            if (mysqli_stmt_execute($stmt)) {
                header("location: welcome.php");
            } else {
                echo "Something went wrong... cannot update user information!";
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
    <title>Update User Information</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
    <a class="navbar-brand" href="#">Php Update System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="welcome.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="update.php">Update Info</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h3>Update Your Information:</h3>
    <hr>
    <form action="update.php" method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $name; ?>">
            <span class="text-danger"><?php echo $name_err; ?></span>
        </div>
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" class="form-control" name="age" id="age" placeholder="Age" value="<?php echo $age; ?>">
            <span class="text-danger"><?php echo $age_err; ?></span>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" class="form-control" name="dob" id="dob" value="<?php echo $dob; ?>">
            <span class="text-danger"><?php echo $dob_err; ?></span>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="number" class="form-control" name="contact" id="contact" placeholder="Contact Number" value="<?php echo $contact; ?>">
            <span class="text-danger"><?php echo $contact_err; ?></span>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
