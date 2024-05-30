<?php
//header("Access-Control-Allow-Origin: http://localhost");
//header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start(); // Start session to store user login status

$currentPage = 'movies'; 

// Check if the user is not logged in, redirect to login page

$movies = array();
if (isset($_POST['selected_genre'])) {
    $selectedGenre = $_POST['selected_genre'];

    // Prepare the data for JSON request
    $data3 = array(
        'type' => 'GetAllSeries',
        'limit' => 100,
        'search' => array(
            'genre' => $selectedGenre
        ),
        'return' => 'all'
    );

    // Convert data to JSON format
    $json_data = json_encode($data3);// Check if the login form is submitted

    // Create a new cURL resource
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Set the Content-Type header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120'); // Replace with your actual credentials

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
    
// Prepare the data for JSON request
} else {
    $data = array(
        'type' => 'GetAllSeries',
        'limit' => 100,
        'return' => 'all'
    );

    // Convert data to JSON format
    $json_data = json_encode($data);

    // Create a new cURL resource
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Set the Content-Type header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120'); // Replace with your actual credentials

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

}
// Check if the login form is submitted
// Prepare the data for JSON request
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/movies.css" id="light-mode">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="../img/4.png" type="image/x-icon">
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
    <title>CineTech</title>
</head>

<body>
    <!--Header-->
    <header>

        <video src="../video/JJKaisen.mp4" autoplay loop="true"></video>
        <nav>
            <div class="logo_ul">
                <img src="../img/4.png" alt="" />
                <ul>
                    <li>
                        <a href="../php/homePage.php">Home</a>
                    </li>
                    <li>
                        <a href="../php/movies.php">Movies</a>
                    </li>
                    <li>
                        <a href="../php/series.php">Series</a>
                    </li>
                    <li>
                        <a href="../php/recAdded.php">Recently Added</a>
                    </li>
                    <li>
                        <a href="../php/favourites.php">My List</a>
                    </li>
                </ul>
            </div>
            <!-- <div class="search_user">
                <input type="text" placeholder="Search..." id="search_input">
                <img src="../img/UserPFP.jpeg" alt="">
                <div class="search" id="search_results"></div>
            </div> -->
        </nav>

        <!-- dropdown menu for the genre -->
        <div class="dropdown">
            <h1>Series</h1>
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Genre
            </button>
            <ul class="dropdown-menu">
                <form class="dropdown-menu" id="genreForm" method="POST" action="" value="">
                <input type="hidden" id="selected_genre" name="selected_genre" value="">
                <li><button class="dropdown-item" type="button" onclick="selectGenre('action')" value="action">Action</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('adventure')" value="adventure">Adventure</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('anime')" value="anime">Anime</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('animation')" value="animation">Animation</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('biographical')" value="biographical">Biographical</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('family')" value="family">Family</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('children')" value="children">Children</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('comedy')" value="action">Comedy</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('documentary')" value="documentary">Documentary</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('drama')" value="drama">Drama</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('european')" value="european">European</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('family')" value="family">Family</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('fantasy')" value="fantasy">Fantasy</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('history')" value="history">History</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('horror')" value="horror">Horror</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('music')" value="music">Music</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('mystery')" value="mystery">Mystery</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('political')" value="political">Political</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('psychological')" value="psychological">Psychological</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('reality')" value="reality">Reality</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('romance')" value="romance">Romance</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('satire')" value="satire">Satire</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('scifi')" value="scifi">Sci-fi</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('sport')" value="sport">Sport</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('spy')" value="spy">Spy</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('superhero')" value="superhero">Superhero</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('supernatural')" value="supernatural">Supernatural</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('teen')" value="teen">Teen</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('thriller')" value="thriller">Thriller</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('war')" value="war">War</button></li>
                <li><button class="dropdown-item" type="button" onclick="selectGenre('western')" value="western">Western</button></li>
                </form>
            </ul>
        </div>
        <script>
        function selectGenre(genre) {
            document.getElementById('selected_genre').value = genre;
            document.getElementById('genreForm').submit();
        }
        </script>

        <!-- Contnent/details of video playing  -->
        <div class="content">
            <h1 id="title">Jujutsu Kaisen </h1>
            <p>A boy swallows a cursed talisman - the finger of a demon - and becomes cursed himself. He enters a 
                shaman's school to be able to locate the demon's other body parts and thus exorcise himself.</p>
            <div class="details">
                <h6>A CineTech Original</h6>
                <h5 id="gen">Action, Adventure, Anime</h5>
                <h4>2020</h4>
                <h3 id="rate"><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>8.6</h3>
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
                    $title = urlencode($movie['Name']);
                    // individual card for each movie
                    echo '<a href="viewMore.php?name=' . $title . '" class="card">';

                    // image of the poster
                    echo '<img src=' . $movie['PosterURL'] . ' alt="" class="poster">';

                    // the rest of the section of the card 
                    echo '<div class="rest_card">';
                    echo '<div class="cont">';
                    echo '<div class="sub">';
                    echo '<h4>' . $movie['Name'] . '</h4>';

                    // content of the card
                    echo '<p>' . $movie['Genre']. " " . $movie['Release_Year'] . '</p>';
                    echo '<h3><span>CINETECH</span><i class="fa fa-star" aria-hidden="true"></i>' . $movie['IMDB_score'] . '</h3>';
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