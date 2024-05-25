<?php
//header("Access-Control-Allow-Origin: http://localhost");
//header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start(); // Start session to store user login status

$currentPage = 'movies';

// Check if the user is not logged in, redirect to login page

$movies = array();
// Check if the login form is submitted
// Prepare the data for JSON request
$data = array(
    'type' => 'GetAllMovies',
    'limit' => 10,
    'return' => 'all'
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
curl_setopt($ch, CURLOPT_USERPWD, 'cinetechwatch:Cinetechwatch120%'); // Replace with your actual credentials

// Return response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Close cURL resource
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the request was successful
if ($responseData['status'] === 'success') {
    // Process the listings data and display on the page
    $movies = $responseData['data'];
} else {
    // Handle error response
    $error = $responseData['data'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cinetechwatch.000webhostapp.com/css/movies.css" id="light-mode">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="icon" href="https://cinetechwatch.000webhostapp.com/img/4.png" type="image/x-icon">
    <!-- <link rel="stylesheet" href="https://cinetechwatch.000webhostapp.com/font-awesome-4.7.0/css/font-awesome.min.css"> -->
    <title>CineTech</title>
</head>

<body>  
    <!--Header-->
    <header>

        <video src="https://cinetechwatch.000webhostapp.com/video/JohnWickTrailer.mp4" autoplay muted></video>
        <nav>
            <div class="logo_ul">
                <img src="https://cinetechwatch.000webhostapp.com/img/4.png" alt="">
                <ul>
                    <li><a href="https://cinetechwatch.000webhostapp.com/html/homePage.html">Home</a></li>
                    <li><a href="https://cinetechwatch.000webhostapp.com/html/movies.html">Movies</a></li>
                    <li><a href="/html/series.html">Series</a></li>
                    <li><a href="/html/recAdded.html">Recently Added</a></li>
                    <li><a href="https://cinetechwatch.000webhostapp.com/html/favourites.html">My List</a></li>
                </ul>
            </div>
            <div class="search_user">
                <input type="text" placeholder="Search..." id="search_input">
                <img src="https://cinetechwatch.000webhostapp.com/img/UserPFP.jpeg" alt="">
                <div class="search" id="search_results"></div>
            </div>
        </nav>
        <div class="dropdown">
            <h1>Movies</h1>
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Genre
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Anime</a></li>
                <li><a class="dropdown-item" href="#">Animation</a></li>
                <li><a class="dropdown-item" href="#">Adventure</a></li>
                <li><a class="dropdown-item" href="#">Comedy</a></li>
                <li><a class="dropdown-item" href="#">Drama</a></li>
                <li><a class="dropdown-item" href="#">Horror</a></li>
                <li><a class="dropdown-item" href="#">Sci-fi</a></li>
                <li><a class="dropdown-item" href="#">Fantasy</a></li>
                <li><a class="dropdown-item" href="#">Thriller</a></li>
                <li><a class="dropdown-item" href="#">Mystery</a></li>
                <li><a class="dropdown-item" href="#">Romance</a></li>
                <li><a class="dropdown-item" href="#">Documentation</a></li>
                <li><a class="dropdown-item" href="#">Music</a></li>
                <li><a class="dropdown-item" href="#">Family</a></li>
                <li><a class="dropdown-item" href="#">Western</a></li>
                <li><a class="dropdown-item" href="#">European</a></li>
                <li><a class="dropdown-item" href="#">Sport</a></li>
                <li><a class="dropdown-item" href="#">History</a></li>
                <li><a class="dropdown-item" href="#">Biographical</a></li>
                <li><a class="dropdown-item" href="#">Spy</a></li>
                <li><a class="dropdown-item" href="#">War</a></li>
                <li><a class="dropdown-item" href="#">Superhero</a></li>
                <li><a class="dropdown-item" href="#">Teen</a></li>
                <li><a class="dropdown-item" href="#">Political</a></li>
                <li><a class="dropdown-item" href="#">Satire</a></li>
                <li><a class="dropdown-item" href="#">Psychological</a></li>
                <li><a class="dropdown-item" href="#">Supernatural</a></li>
                <li><a class="dropdown-item" href="#">Family</a></li>
                <li><a class="dropdown-item" href="#">Reality</a></li>
                <li><a class="dropdown-item" href="#">Children</a></li>
            </ul>
        </div>

        <div class="content">
            <h1 id="title">John Wick 2014</h1>
            <p>John Wick, a retired hitman, is forced to return to his old ways after a group of Russian gangsters steal his car and kill a puppy gifted to him by his late wife.</p>
            <div class="details">
                <h6>A CineTech Original</h6>
                <h5 id="gen">Action, Crime, Thriller</h5>
                <h4>2014</h4>
                <h3 id="rate"><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>7.4</h3>
            </div>
            <div class="btns">
                <a href="#" id="play">Watch <i class="fa fa-play" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="cards" id="movie_cards">
            <!-- Movie cards will be dynamically added here -->
            <?php
            if (isset($movies)) {
                foreach ($movies as $movie) {
                    // main grid
                    // echo '<div class="cards">';

                    // individual card for each movie
                    echo '<a href="#" class="card">';

                    // image of the poster
                    echo '<img src='.$movie['PosterURL'].' alt="" class="poster">';

                    // the rest of the section of the card 
                    echo '<div class="rest_card">';
                    echo '<div class="cont">';
                    echo '<div class="sub">';
                    echo '<h4>' . $movie['Title'] . '</h4>';

                    // content of the card
                    echo '<p>' . $movie['Genre'] . $movie['Release_Year'] . '</p>';
                    echo '<h3><span>CINETECH</span><i class="fa-solid fa-bath"></i>' . $movie['IMDB_score'] . '</h3>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                }
            }
            ?>
        </div>
    </header>
</body>

</html>