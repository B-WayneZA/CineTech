<?php
session_start();

// check if user is logged in or not if not redirect
if (!isset($_SESSION['apikey'])) {
    header('Location: ../php/login.php');
    exit();
}

$currentPage = 'favourites';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = array(
        'type' => 'GetAllFavourites',
        'apikey' => $_SESSION['apikey'],

    );

    $json_data = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php ');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        $message = 'Curl error: ' . curl_error($ch);
    } else {
        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] === 'success') {
            $message = $add ? 'Gotten all favorites successfully.' : 'Removed from favorites successfully.';
        } else {
            $message = 'Failed to update favorites: ' . (isset($result['data']) ? $result['data'] : 'Unknown error');
        }
    }


    curl_close($ch);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/favourites.css" id="light-mode">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- <link rel="stylesheet" href="/css/homePage-dark.css" id="dark-mode"> -->
    <link rel="icon" href="../img/4.png" type="image/x-icon">
    <!-- the icons used in the website -->
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
    <title>CineTech</title>
</head>

<body>
    <!--Header-->
    <header>
        <!-- convert this image to a webm so it actually plays  -->
        <nav>   
        <div class="logo_ul">
                <img src="/img/4.png" alt="">
                <ul>
                    <li>
                        <a href="../html/homePage.html">Home</a>
                    </li>
                    <li>
                        <a href="../html/movies.html">Movies</a>
                    </li>
                    <li>
                        <a href="../html/series.html">Series</a>
                    </li>
                    <li>
                        <a href="../html/recAdded.html">Recently Added</a>
                    </li>
                    <li>
                        <a href="../html/favourites.html">My List</a>
                    </li>
                </ul>
            </div>
        </nav>
</header>

        <!--This is code for the adding divs-->
        <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

<?php if (!empty($favorites)) : ?>
    <?php foreach ($favorites as $favorite) : ?>
        <div class="favBox">
            <div class="fav-image">
                <img class="movieImg" src="<?php echo htmlspecialchars($favorite['image_url']); ?>" alt="">
            </div>
            <div class="fav-info">
                <p class="description"><?php echo htmlspecialchars($favorite['description']); ?></p>
                <p class="genres"><?php echo htmlspecialchars($favorite['genres']); ?></p>
                <form method="POST" action="">
                    <input type="hidden" name="film_id" value="<?php echo htmlspecialchars($favorite['film_id']); ?>">
                    <input type="hidden" name="add" value="false">
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No favorites found.</p>
<?php endif; ?>
</div>
</body>
</html>
           