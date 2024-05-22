<?php
// Start session to store user login status
header("Access-Control-Allow-Origin: *");
session_start();

// Set current page variable for header navigation
$currentPage = 'login';

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email and password from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $admin = isset($_POST['admin']) ? "true" : "false";

    // Validate the email and password (You can add more validation as needed)
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // Prepare the data for JSON request
        $data = array(
            'type' => 'Login',
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

        // Check for cURL errors
        if ($response === false) {
            $error = 'Curl error: ' . curl_error($ch);
        } else {
            file_put_contents('response.log', $response);

            // Decode the JSON response
            $login_response = json_decode($response, true);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = 'JSON decode error: ' . json_last_error_msg();
            } else {
                // Check if the login was successful
                file_put_contents('decoded_response.log', print_r($login_response, true));

                if (isset($login_response['status']) && $login_response['status'] === 'success') {
                    $_SESSION['apikey'] = json_decode($login_response['data'], true)['apikey'];
                    header('Location: https://cinetechwatch.000webhostapp.com/html/homePage.html'); // Redirect to home page after successful login
                    exit();
                } else {
                    $error = isset($login_response['data']) ? $login_response['data'] : 'Login failed';
                }
            }
        }

        // Close cURL resource
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineTech</title>
    <link rel="stylesheet" href="https://cinetechwatch.000webhostapp.com/css/login-dark.css" id="dark-mode">
    <link rel="icon" type="image/x-icon" href="https://cinetechwatch.000webhostapp.com/img/4.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!--Header-->
    <div class="wrapper">

        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form id="loginForm" method="POST">
            <h1>Login</h1>
            <!-- email box -->
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </div>
            <!-- password box -->
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fa fa-lock" aria-hidden="true"></i>
            </div>
            <!-- admin or not checkbox -->
            <div class="remember-forgot">
                <label><input type="checkbox" name="admin">Admin</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="btn">Login</button>
            <!-- register link -->
            <div class="register-link">
                <p>Don't have an account? <a href="https://cinetechwatch.000webhostapp.com/html/register.php">Register</a></p>
            </div>
        </form>
    </div>
    <!--Footer-->
</body>

</html>