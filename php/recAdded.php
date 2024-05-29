<?php

$data = array(
   'type' => 'GetAllSeries',
   'limit' => '100',
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
curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
// Return response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);
// Close cURL resource
curl_close($ch);
//echo $response;
// Decode the JSON response
$series = json_decode($response, true);
// Check if the login was successful
if ($series['status'] === 'success') {
   $shows = $series['data'];
} else {
   $error = $series['data']; // Display the error message returned by the API        
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../css/movies.css" id="light-mode">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
   <link rel="icon" href="../img/4.png" type="image/x-icon">
   <title>CineTech</title>
</head>

<body>
   <!--Header-->
   <header>
      <!-- convert this image to a webm so it actually plays  -->
      <video src="../video/GrandTour.mp4" autoplay loop="true"></video>
      <nav>
         <div class="logo_ul">
            <img src="../img/4.png" alt="">
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
         <div class="search_user">
            <input type="text" placeholder="Search..." id="search_input">
            <img src="../img/UserPFP.jpeg" alt="">
            <div class="search" id="search_results"></div>
         </div>
      </nav>

      <!-- dropdown menu for the genre -->
      <div class="dropdown">
         <h1>Recently Added</h1>
    </div>

      <div class="content">
         <h1 id="title">The Grand Tour</h1>
         <p>Jeremy, Richard and James embark on an adventure across the globe, driving new and exciting automobiles from manufacturers all over the world.</p>
         <div class="details">
            <h6>A CineTech Original</h6>
            <h5 id="gen">Comedy, Sport, Talk-Show</h5>
            <h4>2016</h4>
            <h3 id="rate"><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>8.7</h3>
         </div>
         <div class="btns">
            <a href="#" id="play">Watch <i class="fa fa-play" aria-hidden="true"></i></a>
         </div>
      </div>

      <div class="cards">
         <!-- add more of these to check scroll featur -->
         <?php
         if (isset($shows)) {
            foreach ($shows as $show) {
               $title = urlencode($show['Name']);
               // individual card for each movie
               echo '<a href="viewMore.php?name=' . $title . '" class="card">';
               echo '<img src=" ' . $show['PosterURL'] . '" alt="">';

               echo '<div class="rest_card">';

               echo '<div class="cont">';
               echo '<div class="sub">';
               echo '<h4>' . $show['Name'] . '</h4>';
               echo '<p>' . $show['Genre'] . " " . $show['Release_Year'] . '</p>';
               echo '<h3><span>CINETECH</span><i class="fa fa-star" aria-hidden="true"></i>' . $show['IMDB_score'] . '</h3>';
               echo '</div>';
               echo '</div>';
               echo '</div>';
               echo '</a>';
            }
         }
         ?>


      </div>
      </section>
   </header>