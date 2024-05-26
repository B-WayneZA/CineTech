<?php
if(isset($_GET['name'])) {
   $name = urldecode($_GET['name']);
   $data = array(
      "type" => "GetAllSeries",
      "limit" => 1,
      "search" => array(
          "Name" => $name
      ),
      "return" => "all"
  );
} else {
   $title = urldecode($_GET['title']);
   $data = array(
      "type" => "GetAllMovies",
      "limit" => 1,
      "search" => array(
         "Title" => $title
      ),
      "return" => "all"
   );
}

//header("Access-Control-Allow-Origin: http://localhost");
//header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start(); // Start session to store user login status

$currentPage = 'view';

// Check if the user is not logged in, redirect to login page

$movies = array();
// Check if the login form is submitted
// Prepare the data for JSON request

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
    $movies = $responseData['data'][0];

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
    <link rel="stylesheet" href="/css/viewMore.css" id="light-mode">
    <!-- <link rel="stylesheet" href="/css/homePage-dark.css" id="dark-mode"> -->
    <link rel="icon" href="/img/4.png" type="image/x-icon">
    <!-- the icons used in the website -->
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
    <title>CineTech</title>
</head>

<body>
    <!--Header-->
    <header>
        <!-- convert this image to a webm so it actually plays  -->
        <!-- <video src="/video/JohnWickTrailer.mp4" autoplay muted loop="true"></video> -->
        <nav>
            <div class="logo_ul">
                <img src="/img/4.png" alt="">
                <ul>
                    <li>
                        <a href="/html/homePage.html">Home</a>
                    </li>
                    <li>
                        <a href="/html/movies.html">Movies</a>
                    </li>
                    <li>
                        <a href="/html/series.html">Series</a>
                    </li>
                    <li>
                        <a href="/html/recAdded.html">Recently Added</a>
                    </li>
                    <li>
                        <a href="/html/favourites.html">My List</a>
                    </li>
                </ul>
            </div>
            <div class="search_user">
                <input type="text" placeholder="Search..." id="search_input">
                <!-- User image -->
                <img src="/img/UserPFP.jpeg" alt="">                
                <!-- Add a button for notifications -->
            </div>
        </nav>

    <div class = "ViewDetails">
        <div class="content">
            <div class = "content-image">
                <img class="movieImg" src="<?php echo $movies['PosterURL']?>" alt="">
            </div>
        </div>

        <div class = "content-details">
            <!-- Create a div for the description as well as the other-->
            <div class = "description">
                <h2>Description: </h2> 
                <h3><?php echo $movies['Description'] ?></h3><br>
            </div>

            <div class = "Genres">
                <h3>Genre: <?php echo $movies['Genre']?> </h3><br>
            </div>
            
           <div class = "yearRelease">
            <h3>Year Of Release:  <?php echo $movies['Release_Year']?></h3><br>
           </div>
            
           <div class = "movieRating">
            <h3>CineTech Rating:  <?php echo $movies['CineTech_Rating']?></h3>
           </div>
                
            
            <div class = "userRating">
            <h3>Ratings: </h3>
             <div class = "rating">
                <input type = "radio" name = "rating" id = "r1">
                <label for=" r1"> </label>

                <input type = "radio" name = "rating" id = "r2">
                <label for=" r2"> </label>

                <input type = "radio" name = "rating" id = "r3">
                <label for=" r3"> </label>

                <input type = "radio" name = "rating" id = "r4">
                <label for=" r4"> </label>

                <input type = "radio" name = "rating" id = "r5">
                <label for=" r5"> </label>
             </div>
            </div>
            
            <div class = "actors">
                <h3>Actors: Gabrielle Union, Mark June and Tyler Perry  </h3><br>
            </div>
            
            
              <button class = "trailer" >
                <a href=" <?php echo ' ' ?> " >Trailer</a><br>
              </button>
              
              <button class="btn">Share</button>
              <button class = "watchList">Add to MyList</button>
        </div>
    </div>


    <section>
        <h4>People Also Like</h4>
        <div class="cards">
            <!-- add more of these to check scroll featur -->
            <a href="#" class="card">
                <img src="/img/JohnWick.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/Dune.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/EverythingEverywhereAllAtOnceMoviePoster.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/Fall.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/Howl.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/Inception.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/LalaLand.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/Sightless.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/StepItUp.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card">
                <img src="/img/TheGreatestShowman.jpeg" alt="" class="poster">
                <div class="rest_card">
                    <img src="/img/JohnWickVisual.jpeg" alt="">
                    <div class="cont">
                        <h4>John Wick</h4>
                        <div class="sub">
                            <p>Action, 2024</p>
                            <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                        </div>
                    </div>
                </div>
            </a>

        </div>
 <!--This is to make the buttons of the stars to work -->
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const ratings = document.querySelectorAll('.rating input');
        
        ratings.forEach((rating) => {
            rating.addEventListener('change', () => {
                const selectedValue = rating.id.replace('r', '');
                ratings.forEach((input, index) => {
                    const label = input.nextElementSibling;
                    if (index < selectedValue) {
                        label.style.color = '#f9bf3b';
                    } else {
                        label.style.color = '#444';
                    }
                });
            });
        });
    });
</script>

</section>
</header>

</body>
</html>