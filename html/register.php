<?php
// Start session to store user login status
header("Access-Control-Allow-Origin: *");
session_start();

// Set current page variable for header navigation
$currentPage = 'register';

if (isset($_SESSION['user_id'])) {
    header('Location: https://cinetechwatch.000webhostapp.com/html/login.html'); // Redirect to login page if already logged in
    exit();
}

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from the login form
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $admin = isset($_POST['admin']) ? "true" : "false";

    // Validate the email and password (You can add more validation as needed)
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // Prepare the data for JSON request
        $data = array(
            'type' => 'Register',
            'name' => $name,
            'username' => $username,
            'surname' => $surname,
            'email' => $email,
            'password' => $password,
            'admin' => $admin
        );

        // Convert data to JSON format
        $json_data = json_encode($data);

        // Create a new cURL resource
        $ch = curl_init();

        // Set the URL
        curl_setopt($ch, CURLOPT_URL, 'https://cinetechwatch.000webhostapp.com/php/api.php');

        // Set the request method to POST
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the request data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // Set the Content-Type header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Set basic authentication credentials
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'cinetechwatch:Cinetechwatch120%');
        // Return response instead of outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        // Close cURL resource
        curl_close($ch);

        // Decode the JSON response
        $login_response = json_decode($response, true);
        // Check if the login was successful

        if (isset($login_response) && $login_response['status'] === 'success') {

            $_SESSION['username'] = $email;
            header('Location:https://cinetechwatch.000webhostapp.com/html/login.html'); // Replace 'index.php' with the path to your home page
            exit();
        } else {
            $error = $responseData['data']; // Display the error message returned by the API        
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>CineTech</title>
    <link rel="stylesheet" href="https://cinetechwatch.000webhostapp.com/css/register-dark.css" id="dark-mode">
    <link rel="icon" type="image/x-icon" href="/img/4.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!--Header-->
    <div class="wrapper">

        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form id="registerForm" method="POST">
            <h1>Register</h1>
            <!-- name of the user -->
            <div class="input-box">
                <input type="text" name="name" placeholder="Name" required>
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <!-- surname of the user -->
            <div class="input-box">
                <input type="text" name="surname" placeholder="Surname" required>
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <!-- username of the user -->
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <!-- email of the user -->
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </div>
            <!-- password box -->
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fa fa-lock" aria-hidden="true"></i>
            </div>
            <!-- remember me checkbox -->
            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <label><input type="checkbox" name="admin">Admin</label>
            </div>
            <button type="submit" class="btn">Sign Up</button>
        </form>
    </div>
    <!--Footer-->
</body>

</html>