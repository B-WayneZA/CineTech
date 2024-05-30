<?php
session_start();

if (!isset($_SESSION['apikey'])) {
    header("Location: ../php/login.php");
    exit();
}


$currentPage = 'delete account';

function delete($apikey)
{
$Data = array(
    'type' => 'DeleteUser',
    'apikey' => $_SESSION['apikey']
);

$json_data = json_encode($Data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    return 'Curl error: ' . curl_error($ch);
} else {
    return json_decode($response, true);
}

}

session_unset();
session_destroy();
header("Location: ../html/launch.html");
exit();


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
            display: inline-block; /* Display as block element */
        }

       
        .container {
            max-width: 600px;
            text-align: center;
        }

        .container .par
        {
            background-color: white;
            color : black;
        
        }


    

    </style>
</head>
<body>
<div class="container">
    <h1><b>Delete Account</b></h1>
   

    <p class="par" >Are you sure you want to delete your account?</p>
    <form action="../php/deleteAccount.php" method="post">
        <button type="submit" name="confirm-delete">Yes, Delete My Account</button>
    </form>
    <br>
    <a href="../php/homePage.php" class="hello">Cancel</a>
    </div>

</body>
</html>