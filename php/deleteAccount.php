<?php
session_start();

if (!isset($_SESSION['apikey'])) {
    header("Location: ../php/login.php");
    exit();
}

$currentPage = 'delete account';

$error = '';
$success = '';

function getUserEmail($apikey)
{
    $userInfo = array(
        'type' => 'GetUser',
        'apikey' => $apikey
    );

    $userData = json_encode($userInfo);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return array('status' => 'error', 'message' => 'Curl error: ' . $error_msg);
    }

    curl_close($ch);

    $responseData = json_decode($response, true);
    if ($responseData['status'] === 'success') {
        return array('status' => 'success', 'email' => $responseData['data'][0]['email']);
    } else {
        return array('status' => 'error', 'message' => $responseData['data']);
    }
}

function deleteUser($email)
{
    $data = array(
        'type' => 'DeleteUser',
        'email' => $email
    );

    $json_data = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);

    if ($response === false) {
        return array('status' => 'error', 'message' => 'Curl error: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm-delete'])) {
    // Get the user's email
    $userEmailResponse = getUserEmail($_SESSION['apikey']);

    if ($userEmailResponse['status'] === 'success') {
        $email = $userEmailResponse['email'];

        // Perform the delete operation
        $deleteResponse = deleteUser($email);

        if ($deleteResponse && isset($deleteResponse['status']) && $deleteResponse['status'] === 'success') {
            session_unset();
            session_destroy();
            $success = 'Account successfully deleted. Redirecting to launch page...';
            header("refresh:2;url=../html/launch.html"); // Redirect after 2 seconds
            exit();
        } else {
            $error = 'Failed to delete account. Response: ' . json_encode($deleteResponse);
        }
    } else {
        $error = 'Failed to fetch user email. Response: ' . json_encode($userEmailResponse);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
         body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('../img/Background-dark.png') no-repeat;
            background-size: cover;
            background-position: center;
            color: white;
        }

        .container {
            max-width: 600px;
            text-align: center;
            padding: 20px;
            background-color: transparent;
            border-radius: 10px; /* Rounded corners */
        }

        h1 {
            margin-top: 0; /* Remove default margin */
        }

        p {
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            background-color: #ff0000;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #cc0000;
        }

        a {
            color: white;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        .container .par {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><b>Delete Account</b></h1>
    <h3>Are you sure you want to delete your account?</h3>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php elseif ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="../php/deleteAccount.php" method="post">
        <button type="submit" name="confirm-delete">Yes, Delete My Account</button>
    </form>

    <p>or</p>

    <a href="../php/homePage.php" class="hello">Cancel</a>
</div>
</body>
</html>
