<?php
  session_start();

    $currentPage = 'homePage';

    $noti = array();



  if(isset($_SESSION['apikey'])) 
  {
      $apikey = $_SESSION['apikey'];
    $dataArr = array(
        'type' => 'GetShared',
        'apikey' => $apikey,
        
    );

    $json_arr = json_encode($dataArr);

    $ch_var = curl_init();

    curl_setopt($ch_var, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch_var, CURLOPT_POST, 1); 

    // Set the request data as JSON
    curl_setopt($ch_var, CURLOPT_POSTFIELDS, $json_arr);

    // Set the Content-Type header
    curl_setopt($ch_var, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch_var, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_var, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch_var, CURLOPT_RETURNTRANSFER, true);

    $response_var = curl_exec($ch_var);

    if (curl_errno($ch_var)) {
        $error_msg_var = curl_error($ch_var);
    }

    curl_close($ch_var);

    if (isset($error_msg_var)) {
        // Handle CURL error
        // echo "CURL Error: $error_msg";
    } else {
        $responseData_var = json_decode($response_var, true);

        if ($responseData_var['status'] === 'success') {
            // echo 'successful...' ;
            $noti = $responseData_var['data'];
        } else {
            $errorText = $responseData_var['data'];
        }
    }


    $userInfo = array(
      'type' => "GetUser",
      'apikey' => $apikey
    );

    $userData = json_encode($userInfo);
    

    $ch_var2 = curl_init();

    curl_setopt($ch_var2, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch_var2, CURLOPT_POST, 1); 

    // Set the request data as JSON
    curl_setopt($ch_var2, CURLOPT_POSTFIELDS, $userData);

    // Set the Content-Type header
    curl_setopt($ch_var2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch_var2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_var2, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch_var2, CURLOPT_RETURNTRANSFER, true);

    $response_user = curl_exec($ch_var2);

    if (curl_errno($ch_var2)) {
        $error_msg_var2 = curl_error($ch_var2);
    }

    curl_close($ch_var2);

    if (isset($error_msg_var2)) {
        // Handle CURL error
        // echo "CURL Error: $error_msg";
    } else {
        $responseData_user = json_decode($response_user, true);

        if ($responseData_user['status'] === 'success') {
            // echo 'successful...' ;
            $user = $responseData_user['data'][0];
        } else {
            $errorText2 = $responseData_user['data'];
        }
    }

  } 
  else{
    header("Location: ../php/login.php");
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="../css/homePage-light.css"
      id="light-mode"
    />
    <link
      rel="icon"
      href="../img/4.png"
      type="image/x-icon"
    />
    <!-- the icons used in the website -->
    <link
      rel="stylesheet"
      href="../font-awesome-4.7.0/css/font-awesome.min.css"
    />
    <title>CineTech</title>
  </head>

  <body>
    <!--Header-->
    <header>
      <!-- convert this image to a webm so it actually plays  -->
      <video
        src="../video/JohnWickTrailer.mp4"
        autoplay
        muted
        loop="true"
      ></video>
      <!-- navigation bar over the video -->
      <nav>
        <div class="logo_ul">
          <img src="../img/4.png" alt="" />
          <ul>
            <li>
              <a
                href="../php/homePage.php"
                >Home</a
              >
            </li>
            <li>
              <a href="../php/movies.php"
                >Movies</a
              >
            </li>
            <li>
              <a href="../php/series.php"
                >Series</a
              >
            </li>
            <li>
              <a
                href="../php/recAdded.php"
                >Recently Added</a
              >
            </li>

            <li>
              <a
                href="../php/favourites.php"
                >My List</a
              >
            </li>
          </ul>
        </div>
        <!-- these are the div associated with the user icon, notification and search bar -->
        <div class="search_user">

          <?php 

          function makeApiRequest($data)
          {
              // Create a new cURL resource
              $ch = curl_init();

              // Set the URL
              curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

              // Set the request method to POST
              curl_setopt($ch, CURLOPT_POST, 1);

              // Set the request data as JSON
              curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

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
              return json_decode($response, true);
            }

            if(isset($_POST['Search'])) {
              $searchTerm = $_POST['search_input'];
              $searches = array(
                'type' => "Search",
                'search' => $searchTerm
              );

              $searchData = makeApiRequest($searches);
              var_dump($searchData);

              if($searchData['status'] === 'success') {
                $searchResults = $searchData['data'];
              }
            }
          
          ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET); ?>">
              <input class="search_user" type="text" placeholder="Search..." id="search_input" />
                  <button type="submit" class="search" name="Search">Search</button>
              </form>
            
          <!-- User image -->
          <img
            src="../img/UserPFP.jpeg"
            alt=""
          />
          <!-- this is the div responsible for the styling of the searhc -->
          <div class="search">
            <!-- add more of these to test search feature -->
            <!-- First search option -->
            <?php 
              if(isset($searchResults)) {
                foreach($searchResults as $res) {
                  if($res['type'] === 'film') {
                    $titleRes = urlencode($res['name']);
                    echo '<a href="viewMore.php?title=' .  $titleRes . '" class="card">';
                  } else {
                    echo '<a href="viewMore.php?name=' .  $titleRes . '">';
                  }

                  echo '<img src="'. $res['PosterURL'].'" alt="" class="poster">';
                  echo '<div class="cont">';
                  echo '<h3>'. $res['name'].'</h3>';
                  echo '<p>'.$res['genre'] ." ". $res['Release_Year'] . '<span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>'. $res['rating'] .'</p>';
                  
                  echo '</div>';
                  echo '</a>';
                }
              }
            
            ?>
            </a>
          </div>
          <!-- User panel -->
          <div class="user-panel">
            <div class="user-info">
              <!-- User details go here -->
              <img
                src="../img/UserPFP.jpeg"
                alt=""
              />
              <?php 
                if(isset($user)) {
                  echo "<h3>".$user['username']."</h3>";
                  echo "<p>Email: ".$user['email']."</p>";
                } else {
                  echo "<h3>User not logged in :( </h3>";
                }
              ?>

            </div>
            <div class="user-actions">
              <!-- Additional user actions -->
              <!-- <button id="change-password-btn">Change Password</button>
              <button id="change-username-btn">Change Username</button> -->
              <!-- Logout and delete buttons -->
              <form action="../php/logout.php" method="post">
              <button type="submit" id="logout-btn">Logout</button>
              </form>

              <button onclick="window.location.href='../php/deleteAccount.php'" id="delete-btn">Delete Account</button>
              <!-- Toggle between light and dark mode -->
              <!-- <label class="switch">
                <input type="checkbox" id="mode-switch" />
                <span class="slider round"></span>
              </label> -->
            </div>
          </div> 
          <!-- Add a button for notifications -->
          <div class="notifications">
            <img
              src="../img/Notifications.png"
              alt=""
            />
            <div class="notifications-popup">
              <!-- Content of notifications popup goes here -->
<?php 
            if(isset($noti))
            {
              foreach($noti as $movie)
              {
                $title = urlencode($movie['title']);
               
                echo '<div class = "toast" role="alert" aria-live="assertive" aria-atomic="true"> ';     //<!-- first div under notifications-->

                echo '<div class="toast-header"> ';
                echo '<img src="'. $movie['poster_url'].'" class="rounded me-2" alt="..."/>'; 
                // echo '<strong class="me-auto">CineTech</strong>';
                echo '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close">';
                echo '<i class="fa fa-times" aria-hidden="true"></i>';
                echo '</button>';
                echo '</div>';    //class="toast-header">
                echo '<div class="toast-body">';
                echo  $movie['sender_username']. ' sent you : '.'<a href="viewMore.php?title=' . $title .'">'.$movie['title'] . '</a>';
                echo '</div>' ;   // closing  toast body-->
                echo '</div>';  // closing  <!-- first div under notifications-->
              }
            }
?>

            </div> <!--  closing div for notifications-->
          </div>
        </div>
      </nav>

      <!-- container for the information about the current video playing -->
      <div class="content">
        <h1 id="title">John Wick: Chapter 4</h1>
        <p>
          John Wick uncovers a path to defeating The High Table. But before he
          can earn his freedom, Wick must face off against a new enemy with
          powerful alliances across the globe and forces that turn old friends
          into foes.
        </p>
        <div class="details">
          <h6>A CineTech Original</h6>
          <h5 id="gen">Action, Crime, Thriller</h5>
          <h4>2023</h4>
          <h3 id="rate">
            <span>CineTech</span
            ><i class="fa fa-star" aria-hidden="true"></i>7.7
          </h3>
        </div>
        <div class="btns">
          <a href="#" id="play"
            >Watch <i class="fa fa-play" aria-hidden="true"></i
          ></a>
        </div>
      </div>
    </header>

    <!-- Popup containers -->
    <!-- popup for the change of password -->
    <div class="popup password-popup">
      <div class="popup-header">Change Password</div>
      <div class="popup-content">
        <label for="new-password">New Password:</label>
        <input type="password" id="new-password" />
      </div>
      <div class="popup-footer">
        <button id="cancel-password">Cancel</button>
        <button id="save-password">Save</button>
      </div>
    </div>

    <!-- popup for the change of username -->
    <div class="popup username-popup">
      <div class="popup-header">Change Username</div>
      <div class="popup-content">
        <label for="new-username">New Username:</label>
        <input type="text" id="new-username" />
      </div>
      <div class="popup-footer">
        <button id="cancel-username">Cancel</button>
        <button id="save-username">Save</button>
      </div>
    </div>

<?php
    $currentPage = 'homePage';

    $movies = array();

    $data = array(
        'type' => 'GetAllMovies',
        'limit' => 50,
        'return' => 'all'
    );

    $json_data = json_encode($data);

    $ch = curl_init();

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

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    if (isset($error_msg)) {
        // Handle CURL error
        // echo "CURL Error: $error_msg";
    } else {
        $responseData = json_decode($response, true);

        if ($responseData['status'] === 'success') {
            $movies = $responseData['data'];
        } else {
            $error = $responseData['data'];
        }
    }
?>
    <!-- main page container -->
    <!-- this is the container resonsible for the ful layout of the page after the video header -->
    <div class="main-page">
      <div class="popular">
        
        <section>
        <h4>Popular</h4>  
         <div class="cards">
            <!-- remove this to populate . everything under section -->
            <?php 
            if(isset($movies))
            {
              foreach($movies as $movie)
              {
                $title = urlencode($movie['Title']);
                // individual card for each movie
                echo '<a href="viewMore.php?title=' . $title . '" class="card">';

                echo '<img src="'. $movie['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie['Title'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub

                echo '<p>'. $movie['Genre'].'</p>';  // assuming 'description' key exists in the array
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie['IMDB_score'];  // assuming 'rating' key exists in the array
               echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>

</div>
        </section>
<!-- =========================================================== -->
      </div>
      <!-- this section is responsible for the recommended part of the page -->

<?php

    $currentPage2 = 'homePage';

    $movies2 = array();

    $data2 = array(
        'type' => 'GetAllSeries',
        'limit' => 50,
        'return' => 'all'
    );

    $json_data2 = json_encode($data2);

    $ch2 = curl_init();

    curl_setopt($ch2, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch2, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $json_data2);

    // Set the Content-Type header
    curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch2, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    $response2 = curl_exec($ch2);

    if (curl_errno($ch2)) {
        $error_msg2 = curl_error($ch2);
    }

    curl_close($ch2);

    if (isset($error_msg2)) {
        // Handle CURL error
        echo "CURL Error: $error_msg2";
    } else {
        $responseData2 = json_decode($response2, true);

        if ($responseData2['status'] === 'success') {
            $movies2 = $responseData2['data'];
        } else {
            $error2 = $responseData2['data'];
        }
    }
?>

<div class="recommended">
        <section>
          <h4>Recommended</h4>

          <div class="cards">
            <!-- remove this to populate . everything under section -->
            <?php 
            if(isset($movies2))
            {
              foreach($movies2 as $movie2)
              {
                $title2 = urlencode($movie2['Name']);
                // individual card for each movie
                echo '<a href="viewMore.php?name=' . $title2 . '" class="card">';

                echo '<img src="'. $movie2['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie2['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie2['Name'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub

                echo '<p>'. $movie2['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                echo '<p>Seasons: '. $movie2['Seasons'].'</p>'; 
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie2['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>

</div>

<?php

    $currentPage3 = 'homePage';

    $movies3 = array();

    $data3 = array(
        'type' => 'GetAllMovies',
        'limit' => 20,
        "search" => array(
          "genre"=> "Action"
        ),
        'return' => 'all'
    );

    $json_data3 = json_encode($data3);

    $ch3 = curl_init();

    curl_setopt($ch3, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch3, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch3, CURLOPT_POSTFIELDS, $json_data3);

    // Set the Content-Type header
    curl_setopt($ch3, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch3, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch3, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

    $response3 = curl_exec($ch3);

    if (curl_errno($ch3)) {
        $error_msg3 = curl_error($ch3);
    }

    curl_close($ch3);

    if (isset($error_msg3)) {
        // Handle CURL error
        echo "CURL Error: $error_msg3";
    } else {
        $responseData3 = json_decode($response3, true);

        if ($responseData3['status'] === 'success') {
            $movies3 = $responseData3['data'];
        } else {
            $error3 = $responseData3['data'];
        }
    }
?>

        </section>
      </div>


      <!-- this section is responsible for the binge-watch part of the page -->
<div class="binge-watch">
        <section>
          <h4>Binge Watch Now</h4>
          <div class="cards">
          <?php 
            if(isset($movies3))
            {
              foreach($movies3 as $movie3)
              {
                $title3 = urlencode($movie3['Title']);
                // individual card for each movie
                echo '<a href="viewMore.php?title=' . $title3 . '" class="card">';

                echo '<img src="'. $movie3['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie3['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie3['Title'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub

                echo '<p>'. $movie3['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie3['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>
          <!-- </div> -->
        </section>
</div>

<?php

    $currentPage4 = 'homePage';

    $movies4 = array();

    $data4 = array(
        'type' => 'GetAllMovies',
        'limit' => 50,
        "search" => array(
          "genre"=> "Thriller"
        ),
        'return' => 'all'
    );

    $json_data4 = json_encode($data4);

    $ch4 = curl_init();

    curl_setopt($ch4, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch4, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch4, CURLOPT_POSTFIELDS, $json_data4);

    // Set the Content-Type header
    curl_setopt($ch4, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch4, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch4, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);

    $response4 = curl_exec($ch4);

    if (curl_errno($ch4)) {
        $error_msg4 = curl_error($ch4);
    }

    curl_close($ch4);

    if (isset($error_msg4)) {
        // Handle CURL error
        echo "CURL Error: $error_msg4";
    } else {
        $responseData4 = json_decode($response4, true);

        if ($responseData4['status'] === 'success') {
            $movies4 = $responseData4['data'];
        } else {
            $error4 = $responseData4['data'];
        }
    }
?>
      <!-- this section is responsible for the latest movies part of the page -->
<div class="latest-movie">
        <section>
          <h4>New Movies</h4>
          <div class="cards">
            <!-- add more of these to check scroll featur -->
            <?php 
            if(isset($movies4))
            {
              foreach($movies4 as $movie4)
              {
                $title4 = urlencode($movie4['Title']);
                // individual card for each movie
                echo '<a href="viewMore.php?title=' . $title4 . '" class="card">';

                echo '<img src="'. $movie4['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie4['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie4['Title'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub

                echo '<p>'. $movie4['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie4['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>
        </section>
</div>


<?php

    $currentPage5 = 'homePage';

    $movies5 = array();

    $data5 = array(
        'type' => 'GetAllSeries',
        'limit' => 20,
        "search" => array(
          "genre"=> "Action"
        ),
        'return' => 'all'
    );

    $json_data5 = json_encode($data5);

    $ch5 = curl_init();

    curl_setopt($ch5, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch5, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch5, CURLOPT_POSTFIELDS, $json_data5);

    // Set the Content-Type header
    curl_setopt($ch5, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch5, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch5, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);

    $response5 = curl_exec($ch5);

    if (curl_errno($ch5)) {
        $error_msg5 = curl_error($ch5);
    }

    curl_close($ch5);

    if (isset($error_msg5)) {
        // Handle CURL error
        echo "CURL Error: $error_msg5";
    } else {
        $responseData5 = json_decode($response5, true);

        if ($responseData5['status'] === 'success') {
            $movies5 = $responseData5['data'];
        } else {
            $error5 = $responseData5['data'];
        }
    }
?>
      <!-- this section is responsible for the lastest series part of the page -->
      <div class="latest-series">
        <section>
          <h4>Catch Up Series</h4>
          <div class="cards">
            <!-- add more of these to check scroll featur -->
            <?php 
            if(isset($movies5))
            {
              foreach($movies5 as $movie5)
              { 
                $title5 = urlencode($movie5['Name']);
                // individual card for each movie
                echo '<a href="viewMore.php?name=' . $title5 . '" class="card">';

                echo '<img src="'. $movie5['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie5['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie5['Name'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub
               

                echo '<p>'. $movie5['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                 echo '<p>Seasons: '. $movie5['Seasons'].'</p>';  // assuming 'description' key exists in the array
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie5['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>
        </section>
      </div>


<?php

    $currentPage6 = 'homePage';

    $movies6 = array();

    $data6 = array(
        'type' => 'GetAllSeries',
        'limit' => 20,
        "search" => array(
          "genre"=> "Thriller"
        ),
        'return' => 'all'
    );

    $json_data6 = json_encode($data6);

    $ch6 = curl_init();

    curl_setopt($ch6, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch6, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch6, CURLOPT_POSTFIELDS, $json_data6);

    // Set the Content-Type header
    curl_setopt($ch6, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch6, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch6, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch6, CURLOPT_RETURNTRANSFER, true);

    $response6 = curl_exec($ch6);

    if (curl_errno($ch6)) {
        $error_msg6 = curl_error($ch6);
    }

    curl_close($ch6);

    if (isset($error_msg6)) {
        // Handle CURL error
        echo "CURL Error: $error_msg6";
    } else {
        $responseData6 = json_decode($response6, true);

        if ($responseData6['status'] === 'success') {
            $movies6 = $responseData6['data'];
        } else {
            $error6 = $responseData6['data'];
        }
    }
?>
      <!-- this section is responsible for the international part of the page -->
<div class="international">
        <section>
          <h4>International</h4>
          <div class="cards">
            <!-- add more of these to check scroll featur -->

            <?php 
            if(isset($movies6))
            {
              foreach($movies6 as $movie6)
              {
                $title6 = urlencode($movie6['Name']);
                // individual card for each movie
                echo '<a href="viewMore.php?name=' . $title6 . '" class="card">';

                echo '<img src="'. $movie6['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie6['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie6['Name'] .'</h4>';  // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub
               

                echo '<p>'. $movie6['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                echo '<p>Seasons: '. $movie6['Seasons'].'</p>';  // assuming 'description' key exists in the array
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie6['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>
        </section>
</div>

<?php

    $currentPage7 = 'homePage';

    $movies7 = array();

    $data7 = array(
        'type' => 'GetAllMovies',
        'limit' => 20,
        "search" => array(
          "genre"=> "Action"
        ),
        'return' => 'all'
    );

    $json_data7 = json_encode($data7);

    $ch7 = curl_init();

    curl_setopt($ch7, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch7, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch7, CURLOPT_POSTFIELDS, $json_data7);

    // Set the Content-Type header
    curl_setopt($ch7, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch7, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch7, CURLOPT_USERPWD, 'u23535246:Toponepercent120');

    // Return response instead of outputting it
    curl_setopt($ch7, CURLOPT_RETURNTRANSFER, true);

    $response7 = curl_exec($ch7);

    if (curl_errno($ch7)) {
        $error_msg7 = curl_error($ch7);
    }

    curl_close($ch7);

    if (isset($error_msg7)) {
        // Handle CURL error
        echo "CURL Error: $error_msg7";
    } else {
        $responseData7 = json_decode($response7, true);

        if ($responseData7['status'] === 'success') {
            $movies7 = $responseData7['data'];
        } else {
            $error7 = $responseData7['data'];
        }
    }
?>

      <!-- this section is responsible for the our Top picks part of the page -->
<div class="top-picks">
        <section>
          <h4>CineTech Top Picks</h4>
          <div class="cards">
            <!-- add more of these to check scroll featur -->
            <?php 
            if(isset($movies7))
            {
              foreach($movies7 as $movie7)
              {
                $title7 = urlencode($movie7['Title']);
                // individual card for each movie
                echo '<a href="viewMore.php?title=' . $title7 . '" class="card">';

                echo '<img src="'. $movie7['PosterURL'].'" alt="" class="poster">';

                echo '<div class="rest_card">';  // div for rest_card

                echo '<img src= "'. $movie7['PosterURL'] .'" alt="">';

                echo '<div class="cont">';  // div for cont
                echo '<h4>'. $movie7['Title'] .'</h4>'; // assuming 'title' key exists in the array
                echo '<div class="sub">';  // div for sub
               

                echo '<p>'. $movie7['Genre'].'</p>';  // assuming 'description' key exists in the array
                // echo '<br>'; 
                //  echo '<p>'. $movie7['Seasons'].'</p>';
                echo '<h3>';
                echo '<span>CineTech</span>';
                echo '<i class="fa fa-star" aria-hidden="true"></i>'. $movie7['IMDB_score'];  // assuming 'rating' key exists in the array
                echo  '</h3>';
                echo '</div>';  // closing div for sub
                echo '</div>';  // closing div for cont
                echo '</div>';  // closing div for rest_card

                echo '</a>';  // closing a tag
              }
            }
            ?>
        </section>
</div>
    </div>



    <!-- this section is for the js of the page -->
    <script src="../js/homePage.js"></script>
    <!--Footer-->
  </body>
</html>
