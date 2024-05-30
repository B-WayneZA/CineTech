<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Content-Type: application/json');

require_once 'config.php'; //include

class API
{
   // --------------------------------------- Basic config functions ------------------------------------ //


   public static function instance() {
      static $instance = null;
      if ($instance === null) $instance = new API();
      return $instance;
   }



   public function __construct() {
      $this->handleReq();
   }



   public function __destruct() {
      global $connection;
      if (isset($connection)) {
         $connection->close();
      }
   }



   private function errorResponse($time, $message) {
      return json_encode(["status" => "error", "timestamp" => $time, "data" => $message]);
   }



   private function successResponse($time, $data = []) {
      return json_encode(["status" => "success", "timestamp" => $time, "data" => $data]);
   }


   // --------------------------------------- Register and login stuff functions ------------------------------------ // 


   private function retSalt($email) {
      $stmt = $GLOBALS['connection']->prepare("SELECT salt FROM users WHERE email= ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      
      if ($result->num_rows > 0) {
         $row = $result->fetch_assoc();
         return $row["salt"];
      } else {
         return null;
      }
   }



   private function retSaltAdmin($email) {
      $stmt = $GLOBALS['connection']->prepare("SELECT salt FROM Admins WHERE email= ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
  
      if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          return $row["salt"];   
      } else {
          return null;
      }
   }



   private function getSalt() {
      // Generates a random string that will be used to on the password to add flavour (aka security)
      // Above 10 characters + Dynamic created

      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randomString = '';

      for ($i = 0; $i < 10; $i++) {
         $index = rand(0, strlen($characters) - 1);
         $randomString .= $characters[$index];
      }

      return $randomString;
   }



   private function HashPassword($psw, $salt) {
      // Adds salt to password before hashing
      $pswSalt = $psw . $salt;
      $hashedPassword = hash('sha256', $pswSalt);

      return $hashedPassword;
   }



   private function getApiKey() {
      $key = bin2hex(random_bytes(16));
      return $key;
   }



   public function registerUser($name, $surname, $email, $password, $username, $admin) {
      if (empty($name) || empty($surname) || empty($email) || empty($password)) {
         return json_encode(array("message" => "All fields are required"));
      }

      // Validate email format
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         return $this->errorResponse(time(), "Invalid email format");
      }

      // Validate password strength (e.g., minimum length, contain uppercase, lowercase, digit, symbol)
      if (strlen($password) < 8) {
         return json_encode(array('message' => 'Password must have at least 8 characters'));
      } else if (!preg_match('/[A-Z]/', $password)) {
         return json_encode(array('message' => 'Password should include at least one uppercase letter'));
      } else if (!preg_match('/[a-z]/', $password)) {
         return json_encode(array('message' => 'Password should include at least one lowercase letter'));
      } else if (!preg_match('/[0-9]/', $password)) {
         return json_encode(array('message' => 'Password should include at least one number'));
      }

      // Check if user already exists
      if ($admin === "true") {
         $stmt = $GLOBALS['connection']->prepare("SELECT admin_id FROM Admins WHERE email = ?");
      } else {
         $stmt = $GLOBALS['connection']->prepare("SELECT user_id FROM users WHERE email = ?");
      }

      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
         return $this->errorResponse(time(), "Person already exists");
      }
      // Hash password
      // Generate salt
      $salt = $this->getSalt();

      // Hash password with salt
      $hashed_password = $this->HashPassword($password, $salt);
      $date = new DateTime();
      $lastLogin =  $date->getTimestamp();


      $apiKey = $this->getApiKey();
      // Insert user into database
      if ($admin === "true") {
         $stmt = $GLOBALS['connection']->prepare("INSERT INTO Admins (name, surname, email, password,salt,apikey, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
      } else {
         $stmt = $GLOBALS['connection']->prepare("INSERT INTO users (name, surname, email, password,salt,apikey, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
      }
      $stmt->bind_param("sssssss", $name, $surname, $email, $hashed_password, $salt, $apiKey, $username);

      if ($stmt->execute()) {
         return $this->successResponse($lastLogin, ["apiKey" => $apiKey]);
      } else {
         // var_dump($stmt->error)
         return $this->errorResponse($lastLogin, $stmt->error . 500);
      }
   }



   public function login($email, $pass, $admin) {
      if (!$pass) {
         // Unable to login error checking
         return $this->errorResponse("Incorrect password", time());
      } else {
         if ($admin === 'true') {
            $stmt = $GLOBALS['connection']->prepare("SELECT admin_id, apikey FROM Admins WHERE email = ? AND password = ?");
         } else {
            $stmt = $GLOBALS['connection']->prepare("SELECT user_id, apikey FROM users WHERE email = ? AND password = ?");
         }
         // Creates a new API key and save it
         $stmt->bind_param("ss", $email, $pass);
         $stmt->execute();
         $result = $stmt->get_result();

         $row = $result->fetch_assoc();

         if ($result->num_rows > 0) {
            // Credentials are correct, return the user ID
            session_start();
            $_SESSION["apikey"] = $row["apikey"];
            $key = $row["apikey"];

            $api = array("apikey" => $key);
            return $this->successResponse(time(), json_encode($api));
         } else {
            return $this->errorResponse("Incorrect login details", time());
         }
      }
   }



   public function logout($apiKey) {
      session_start(); // Start the session
      $_SESSION = array(); // Unset all session variables
      session_destroy(); // Destroy

      $cookie_name = $apiKey;
      setcookie($cookie_name, "", time() - 3600, "/");
   }



   private function checkCredentials($email, $password) {
      if (empty($email) || empty($password)) {
         return "Email and password are required.";
      } else {
         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
         } else {
            try {
               // Hash the password
               $hashedPassword = $this->HashPassword($password, $this->retSalt($email));

               $stmt = $GLOBALS['connection']->prepare("SELECT id FROM users WHERE email = ? AND password = ?");
               $stmt->bind_param("ss", $email, $hashedPassword);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                  // Credentials are correct, return the user ID
                  $row = $result->fetch_assoc();
                  return true;
               } else {
                  return true;
               }
            } catch (Exception $e) {
               // Log or handle the exception appropriately
               return "An error occurred while checking credentials.";
            }
         }
      }
   }


   // ---------------------------------------- HomePage functions -------------------------------------- //


   
   private function searchBar($value) {
      global $connection;
      $searchValue = "%" . $value . "%";
      if($value === "") {
         return $this->errorResponse(time(), $value);
      }
      try {
         // Query to search both movies and shows with joins for rating and genre
         $query = "
              SELECT 
                  m.Title AS name, 
                  r.IMDB_score AS rating, 
                  g.Genre AS genre, 
                  m.PosterURL, 
                  m.Release_Year,
                  'film' as type
              FROM Films m
              JOIN Rating r ON m.Rating_ID = r.Rating_ID
              JOIN Genre g ON m.Genre_ID = g.Genre_ID
              WHERE m.Title LIKE ? OR g.Genre LIKE ?
              UNION
              SELECT 
                  s.Name AS title, 
                  r.IMDB_score AS rating, 
                  g.Genre AS genre, 
                  s.PosterURL, 
                  s.Release_Year,
                  'show' as type
              FROM Shows s
              JOIN Rating r ON s.RatingID = r.Rating_ID
              JOIN Genre g ON s.Genre_ID = g.Genre_ID
              WHERE s.Name LIKE ? OR g.Genre LIKE ?";

         $stmt = $connection->prepare($query);
         $stmt->bind_param("ssss", $searchValue, $searchValue, $searchValue, $searchValue);
         $stmt->execute();
         $result = $stmt->get_result();

         $results = [];
         while ($row = $result->fetch_assoc()) {
            $results[] = $row;
         }

         return $this->successResponse(time(), $results);
      } catch (Exception $e) {
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }



   public function getMovies($limit, $search, $return, $fuzzy) {
      $table = $GLOBALS['connection'];
      if ($return === "['*']") {
         $query = "SELECT * FROM films";
      } else {
         $query = "SELECT f.Title, f.Films_ID AS ID, f.PosterURL, g.Genre, r.IMDB_score, r.IMDB_votes, r.TMDB_popularity, r.TMDB_score, r.CineTech_Rating, f.Country, f.Description, f.Runtime, f.Release_Year FROM Films f JOIN Genre g ON f.Genre_ID = g.Genre_ID JOIN Rating r ON f.Rating_ID = r.Rating_ID";
      }

      // Add filters on table
      if (isset($search) && is_array($search) && count($search) > 0) {
         $filters = array();
         foreach ($search as $column => $value) {
            // Escape column and value to prevent SQL injection
            $column = $GLOBALS['connection']->real_escape_string($column);
            $value = $GLOBALS['connection']->real_escape_string($value);

            if ($column === 'year') {
               $year = (int) $value;
               $filters[] = "f.Release_Year >= $year";
            } else if ($column === 'rating') {
               $rating = (int) $value;
               $filters[] = "r.CineTech_Rating >= $rating";
            } else {
               // Add other filter conditions to the array
               if ($fuzzy === "true") {
                  $filters[] = "$column LIKE '%$value%'";
               } else {
                  $filters[] = "$column = '$value'";
               }
            }
         }

         // Combine all filter conditions with 'AND' and append to the query
         $query .= " WHERE " . implode(' AND ', $filters);
      }

      if (isset($limit)) {
         $query .= ' LIMIT ' . $limit;
      }

      $stmt = $table->prepare($query);
      // Execute the query
      $stmt->execute();

      // Get the result set
      $result = $stmt->get_result();

      // Check if any rows are returned
      if ($result->num_rows > 0) {
         // Fetch rows and store in array
         $listings = array();

         while ($row = $result->fetch_assoc()) {
            if ($return === "all") {
               $listings[] = (object) $row;
            } else {
               $listing = array();
               foreach ($return as $column) {
                  if (isset($row[$column])) {
                     $listing[$column] = $row[$column];
                  }
               }
               $listings[] = (object) $listing;
            }
         }

         return $this->successResponse(time(), $listings);
      } else {
         // Return error response if no listings found
         return $this->errorResponse(time(), "No movies found");
      }
   }



   public function getSeries($limit, $search, $return, $fuzzy) {
      $query = "SELECT s.Name, s.Show_id AS ID,s.PosterURL , s.Seasons , g.Genre, r.IMDB_score, r.IMDB_votes, r.TMDB_popularity, r.TMDB_score, r.CineTech_Rating, s.Country, s.Description, s.Runtime, s.Release_Year FROM Shows s JOIN Genre g ON s.Genre_ID = g.Genre_ID JOIN Rating r ON s.RatingID = r.Rating_ID";

      // Add search and filter conditions
      if (isset($search) && is_array($search) && count($search) > 0) { // Adds conditions based on search parameters
         $filters = array();
         foreach ($search as $column => $value) {
            // Escape column and value to prevent SQL injection
            $column = $GLOBALS['connection']->real_escape_string($column);
            $value = $GLOBALS['connection']->real_escape_string($value);

            if ($column === 'year') {
               $year = (int) $value;
               $filters[] = "s.Release_Year >= $year";
            } else if ($column === 'rating') {
               $rating = (int) $value;
               $filters[] = "r.CineTech_Rating >= $rating";
            } else {
               // Add other filter conditions to the array
               if ($fuzzy === "true") {
                  $filters[] = "$column LIKE '%$value%'";
               } else {
                  $filters[] = "$column = '$value'";
               }
            }

            $query .= " WHERE " . implode(' AND ', $filters);
         }
      }

      if (isset($limit)) {
         $query .= ' LIMIT ' . $limit;
      }

      // Prepare and execute the query using prepared statements
      $stmt = $GLOBALS["connection"]->prepare($query);
      $stmt->execute();
      $result = $stmt->get_result();

      // Fetch results as an associative array
      if ($result->num_rows > 0) {
         $listings = array();
         // Fetch rows and store in array
         // Fetch rows and store in array
         while ($row = $result->fetch_assoc()) {
            if ($return === "all") {
               $listings[] = (object) $row;
            } elseif (is_array($return)) {
               $listing = array();
               foreach ($return as $column) {
                  if (isset($row[$column])) {
                     $listing[$column] = $row[$column];
                  }
               }
               $listings[] = (object) $listing;
            }
         }         // Add the listing to the array

         return $this->successResponse(time(), $listings);
      } else {
         return $this->errorResponse(time(), "No shows found");
      }
   }



   public function getShared($apikey) {
      // Get the user ID from the API key
      $uIDQuery = "SELECT user_id FROM users WHERE apikey=?";
      $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
      $uIDStmt->bind_param('s', $apikey);
      $uIDStmt->execute();
      $uIDResult = $uIDStmt->get_result();

      if ($uIDResult->num_rows == 0) {
         // Handle case where API key does not correspond to any user
         return $this->errorResponse(time(), "User not found for API key: " . $apikey);
      }

      $uIDRow = $uIDResult->fetch_assoc();
      $userID = $uIDRow['user_id'];

      // Query to get shared movies
      $sharedMoviesQuery = "
            SELECT 
            fm.Title AS title,
            g.Genre AS genre,
            r.CineTech_Rating AS rating,
            fm.PosterURL AS poster_url,
            fm.Release_Year AS release_year,
            u.username AS sender_username,
            'movie' AS type
         FROM Shared_movies sm
         JOIN users u ON sm.Sender_ID = u.user_id
         JOIN Films fm ON sm.Film_shared = fm.Films_ID
         JOIN Genre g ON fm.Genre_ID = g.Genre_ID
         JOIN Rating r ON fm.Rating_ID = r.Rating_ID
         WHERE sm.Receiver_ID = ?
      ";

      $sharedMoviesStmt = $GLOBALS['connection']->prepare($sharedMoviesQuery);
      $sharedMoviesStmt->bind_param('i', $userID);
      $sharedMoviesStmt->execute();
      $sharedMoviesResult = $sharedMoviesStmt->get_result();

      $sharedContent = [];
      while ($row = $sharedMoviesResult->fetch_assoc()) {
         $sharedContent[] = $row;
     }
      // Query to get shared shows
      $sharedShowsQuery = "
            SELECT 
            fm.Title AS title,
            g.Genre AS genre,
            r.CineTech_Rating AS rating,
            fm.PosterURL AS poster_url,
            fm.Release_Year AS release_year,
            u.username AS sender_username,
            'movie' AS type
         FROM Shared_shows sm
         JOIN users u ON sm.Sender_ID = u.user_id
         JOIN Films fm ON sm.Show_ID = fm.Films_ID
         JOIN Genre g ON fm.Genre_ID = g.Genre_ID
         JOIN Rating r ON fm.Rating_ID = r.Rating_ID
         WHERE sm.Receiver_id = ?
      ";

      $sharedShowsStmt = $GLOBALS['connection']->prepare($sharedShowsQuery);
      $sharedShowsStmt->bind_param('i', $userID);
      $sharedShowsStmt->execute();
      $sharedShowsResult = $sharedShowsStmt->get_result();

      // while ($row = $sharedShowsResult->fetch_assoc()) {
      //    $sharedContent[] = $row;
      // }

      return $this->successResponse(time(), $sharedContent);
   }



   public function getUserRecommendations($apiKey) { // Not done

      $stmt = $GLOBALS['connection']->prepare("SELECT user_id FROM users WHERE apikey = ?");
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         return $this->errorResponse(time(), "User does not exists");
      } else {
         $stmt = $GLOBALS["connection"]->prepare("SELECT  ");
      }
   }



   private function inputRatings() {
      $query = "UPDATE Rating AS r
      JOIN (
         SELECT s.RatingID, AVG(cr.Rating) AS avg_rating
         FROM CineTech_Show_Rating AS cr
         JOIN Shows AS s ON cr.Show_ID = s.Show_id
         GROUP BY s.RatingID
      ) AS subquery ON r.Rating_ID = subquery.RatingID
      SET r.CineTech_Rating = subquery.avg_rating";

      $stmt = $GLOBALS["connection"]->prepare($query);

      $stmt->execute();

      $query = "UPDATE Rating AS r
      JOIN (
          SELECT f.Rating_ID, AVG(cr.Rating) AS avg_rating
          FROM CineTech_Film_Rating AS cr
          JOIN Films AS f ON cr.Films_ID = f.Films_ID
          GROUP BY f.Rating_ID
      ) AS subquery ON r.Rating_ID = subquery.Rating_ID
      SET r.CineTech_Rating = subquery.avg_rating";

      $stmt = $GLOBALS["connection"]->prepare($query);

      $stmt->execute();
   }



   public function addRatings($filmID, $showID, $rating) {
      // Inserts Cintech Rating
      if (isset($filmID)) {
         $query = "INSERT INTO CineTech_Film_Rating (Films_ID, Rating) VALUES (?,?)";
         $stmt = $GLOBALS["connection"]->prepare($query);
         $stmt->bind_param("ii", $filmID, $rating);

         if ($stmt->execute()) {
            $this->inputRatings();
            return $this->successResponse(time(), "CineTech Rating added successfully");
         } else {
            return $this->errorResponse(time(), "Failed to add CineTech Rating");
         }
      } else {
         $query = "INSERT INTO CineTech_Show_Rating (Show_ID, Rating) VALUES (?,?)";
         $stmt = $GLOBALS["connection"]->prepare($query);
         $stmt->bind_param("ii", $showID, $rating);

         if ($stmt->execute()) {
            $this->inputRatings();
            return $this->successResponse(time(), "CineTech Rating added successfully");
         } else {
            return $this->errorResponse(time(), "Failed to add CineTech Rating ");
         }
      }
   }



   // DEBUGGED
   public function getAllFavourites($apikey) { // DONE
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT user_id FROM users WHERE apikey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $apikey);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
               // Handle case where API key does not correspond to any user
               return $this->errorResponse(time(), "User not found for API key: " . $apikey);
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["user_id"];
         $userID = $userData["user_id"];

         // Query to fetch favorites from favourites table
         $query = "SELECT * FROM favourites WHERE user_id=?";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bind_param('i', $userID);
         $stmt->execute();
         $result = $stmt->get_result();

         // Check if any favorites are found
         if ($result->num_rows > 0) {
               $favorites = array();

               // Fetch each favorite and extract listing information from the films and shows tables
               $Query = "
                  SELECT 
                     f.Films_ID as id, 'film' as type, f.Title, f.PosterURL, f.Country, f.Description, f.Release_Year 
                  FROM 
                     favourites v 
                  JOIN 
                     Films f ON f.Films_ID = v.films_id 
                  WHERE 
                     v.user_id = ?
                  UNION
                  SELECT 
                     s.Show_id as id, 'show' as type, s.Name as Title, s.PosterURL,s.Country, NULL as Description, s.Release_Year 
                  FROM 
                     favourites v 
                  JOIN 
                     Shows s ON s.Show_id = v.shows_id 
                  WHERE 
                     v.user_id = ?";

               $Stmt = $GLOBALS['connection']->prepare($Query);
               $Stmt->bind_param('ii', $userID, $userID);
               $Stmt->execute();
               $Result = $Stmt->get_result();

               // Fetch all results
               while ($Data = $Result->fetch_assoc()) {
                  $favorites[] = $Data;
               }

               // Return success response with favorites data
               return $this->successResponse(time(), $favorites);
         } else {
               // Return error response if no favorites found
               return $this->errorResponse(time(), "No favorites found for user with API key: " . $apikey);
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }



   private function addFavourite($api, $filmID, $showID){ // Need to
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT user_id FROM users WHERE apikey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $api);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
            // Handle case where API key does not correspond to any user
            return $this->errorResponse(time(), "User not found for API key: " . $api);
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["user_id"];

         $insertQuery = "INSERT INTO favourites (user_id, shows_id, films_id) VALUES (?, ?, ?)";
         $insertStmt = $GLOBALS['connection']->prepare($insertQuery);

         if (isset($filmID)) {
            // Retrieve listing information based on listing ID
            $listingQuery = "SELECT * FROM Films WHERE Films_ID=?";
            $listingStmt = $GLOBALS['connection']->prepare($listingQuery);
            $listingStmt->bind_param('i', $filmID);
            $listingStmt->execute();
            $listingResult = $listingStmt->get_result();

            if ($listingResult->num_rows == 0) {
               // Handle case where listing ID does not exist
               return $this->errorResponse(time(), "Listing not found for ID: " . $filmID);
            }

            $Data = $listingResult->fetch_assoc();
            $insertStmt->bind_param('iii', $userID, $showID, $filmID);
         } else {
            $listingQuery = "SELECT * FROM Shows WHERE Show_ID=?";
            $listingStmt = $GLOBALS['connection']->prepare($listingQuery);
            $listingStmt->bind_param('i', $showID);
            $listingStmt->execute();
            $listingResult = $listingStmt->get_result();

            if ($listingResult->num_rows == 0) {
               // Handle case where listing ID does not exist
               return $this->errorResponse(time(), "Show not found for ID: " . $showID);
            }

            $Data = $listingResult->fetch_assoc();
            $insertStmt->bind_param('iii', $userID, $showID, $filmID);
         }

         // Insert favorite into database

         if ($insertStmt->execute()) {
            return $this->successResponse(time(), "Favorite added successfully.");
         } else {
            // Handle SQL execution error
            // Uncomment the following line for debugging
            // var_dump($insertStmt->error);
            return $this->errorResponse(time(), "Error adding favorite: " . $insertStmt->error);
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }



   private function deleteFavourite($api, $filmID, $showID) {
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT user_id FROM users WHERE apiKey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $api);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
            // Handle case where API key does not correspond to any user
            return $this->errorResponse(time(), "User not found for API key: " . $api);
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["user_id"];

         // Delete favorite from database
         if (isset($filmID)) {
            $deleteQuery = "DELETE FROM favourites WHERE user_id=? AND films_id=?";
            $deleteStmt = $GLOBALS['connection']->prepare($deleteQuery);
            $deleteStmt->bind_param('ii', $userID, $filmID);
         } else {
            $deleteQuery = "DELETE FROM favourites WHERE user_id=? AND shows_id=?";
            $deleteStmt = $GLOBALS['connection']->prepare($deleteQuery);
            $deleteStmt->bind_param('ii', $userID, $showID);
         }

         if ($deleteStmt->execute()) {
            return $this->successResponse(time(), "Removed from favorites.");
         } else {
            // Handle SQL execution error
            return $this->errorResponse(time(), "Error deleting favorite: " . $deleteStmt->error);
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }


   
   private function getRatingID($film) { // Check this
      $query = "SELECT Rating_ID FROM Rating ORDER BY Rating_ID DESC LIMIT 1";
      return $film;
   }



   private function getRatingAvgFilm($filmId) {
      $query = "SELECT r.CineTech_Rating,(SELECT AVG(CineTech_Rating) FROM Rating WHERE Rating_ID = f.Rating_ID) AS CineTech_R FROM Films f JOIN Rating r ON f.Rating_ID = r.Rating_ID WHERE f.Films_ID = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("i", $filmId);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row;
   }



   private function getRatingAvgShow($showId) {
      $query = "SELECT r.CineTech_Rating,(SELECT AVG(CineTech_Rating) FROM Rating WHERE Rating_ID = f.Rating_ID) AS CineTech_R FROM Films f JOIN Rating r ON f.Rating_ID = r.Rating_ID WHERE f.Films_ID = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("i", $showId);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row;
   }



   private function getUserID($apiKey) {
      $query = "SELECT user_id FROM users WHERE apikey = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         // Handle case where API key does not correspond to any user
         return $this->errorResponse(time(), "User not found for API key: " . $apiKey);
      }
      $row = $result->fetch_assoc();

      return $row['user_id'];
   }



   private function getUserIDusername($username) {
      $query = "SELECT user_id FROM users WHERE username = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         // Handle case where API key does not correspond to any user
         return $this->errorResponse(time(), "User not found for: " . $username);
      }
      $row = $result->fetch_assoc();

      return $row['user_id'];
   }



   private function shareMovie($apiKey, $username, $filmID) {
      $userID = $this->getUserID($apiKey);
      $receiverID = $this->getUserIDusername($username);

      $query = "INSERT INTO Shared_movies (Receiver_ID, Sender_ID, Film_shared) VALUES (?,?,?)";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("iii", $receiverID, $userID, $filmID);

      if ($stmt->execute()) {
         return $this->successResponse(time(), "Movie shared successfully");
      } else {
         return $this->errorResponse(time(), "Error sharing movie");
      }
   }



   private function shareShow($apiKey, $username, $showID) {
      $userID = $this->getUserID($apiKey);
      $receiverID = $this->getUserIDusername($username);

      $query = "INSERT INTO Shared_shows (Receiver_ID, Sender_ID, Show_ID) VALUES (?,?,?)";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("iii", $receiverID, $userID, $showID);
      $stmt->execute();

      if ($stmt->execute()) {
         return $this->successResponse(time(), "Show shared successfully");
      } else {
         return $this->errorResponse(time(), "Error sharing show");
      }
   }



   private function getPopularMovies() {
      // Get movies with a cinetech rating that is > 4
   }


   private function getNewMovies() { // Get movies from this year 3.
      try {
         $query = "SELECT * FROM Films WHERE YEAR(ReleaseDate) = YEAR(CURDATE()) ";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->execute();
         $result = $stmt->get_result();
         $results = [];
         while ($row = $result->fetch_assoc()) {
            $results[] = $row;
         }

         return $this->successResponse(time(), $results);
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }


   // ---------------------------------------- Admin functions ------------------------------------------ //


   // DEBUGGED FOR THE SECOND TIME
   public function deleteUser($email) {
      // Prepare the statement to find the user by email
      $stmt = $GLOBALS['connection']->prepare("SELECT user_id FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
  
      // Check if the user exists
      if ($result->num_rows == 0) {
          return $this->errorResponse(time(), "User does not exist");
      } else {
          // Fetch the user ID
          $row = $result->fetch_assoc();
          $user_id = $row['user_id'];
  
          // Prepare the statement to delete the user by user_id
          $stmt = $GLOBALS['connection']->prepare("DELETE FROM users WHERE user_id = ?");
          $stmt->bind_param("i", $user_id);
          if ($stmt->execute()) {
              return $this->successResponse(time(), "User successfully deleted");
          } else {
              return $this->errorResponse(time(), "An error occurred while deleting the user");
          }
      }
   }


   
   private function delete($title, $item) {
      // Check if the item parameter is valid
      if ($item !== "film" && $item !== "show") {
         return $this->errorResponse(time(), "Invalid item type specified.");
      }

      // Check if the title exists in the respective table
      if ($item === "film") {
         $checkStmt = $GLOBALS['connection']->prepare("SELECT Films_ID FROM Films WHERE Title = ?");
      } else if ($item === "show") {
         $checkStmt = $GLOBALS['connection']->prepare("SELECT Show_id FROM Shows WHERE Name = ?");
      }

      $checkStmt->bind_param("s", $title);
      $checkStmt->execute();
      $checkResult = $checkStmt->get_result();

      if ($checkResult->num_rows === 0) {
         return $this->errorResponse(time(), "Title not found in the database.");
      }

      // Perform deletion if title exists
      if ($item === "film") {
         $stmt = $GLOBALS['connection']->prepare("DELETE FROM Films WHERE Title = ?");
      } else if ($item === "show") {
         $stmt = $GLOBALS['connection']->prepare("DELETE FROM Shows WHERE Name = ?");
      }

      $stmt->bind_param("s", $title);

      if ($stmt->execute()) {
         return $this->successResponse(time(), ucfirst($item) . " deleted successfully.");
      } else {
         return $this->errorResponse(time(), $stmt->error);
      }
   }

   

   // DEBUGGED FOR SECOND TIME
   private function addMovie($title, $genreID, $ratingID, $country, $description, $runtime, $year, $PostURL, $VideoURL, $ScreenURL) {
      try {
         // Insert the film into the Films table
         $query = "INSERT INTO Films (Title, Genre_ID, Rating_ID, Country, Description, Runtime, Release_Year, PosterURL, TrailerURL, ScreenshotURL) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bind_param('siisssisss', $title, $genreID, $ratingID, $country, $description, $runtime, $year, $PostURL, $VideoURL, $ScreenURL);
         $stmt->execute();
   
         return $this->successResponse(time(), "Movie added successfully");
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }



   // DEBUGGED FOR SECOND TIME
   private function addSeries($title, $genreID, $ratingID, $country, $description, $runtime, $year, $seasons, $PostURL, $VideoURL, $ScreenURL) {
      try {
         // Insert the series into the Shows table
         $query = "INSERT INTO Shows (Name, Genre_ID, RatingID, Country, Description, Runtime, Release_Year, Seasons, PosterURL, TrailerURL, ScreenshotURL) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         $stmt = $GLOBALS['connection']->prepare($query);
         if (!$stmt) {
            return $this->errorResponse(time(), "Prepare failed: " . $GLOBALS['connection']->error);
         }
         $stmt->bind_param('siisssiisss', $title, $genreID, $ratingID, $country, $description, $runtime, $year, $seasons, $PostURL, $VideoURL, $ScreenURL);
         $stmt->execute();
   
         return $this->successResponse(time(), "Series added successfully");
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }



   // DEBUGGED FOR SECOND TIME
   private function editEntityByTitle($table, $titleColumn, $titleValue, $fields) { // DONE
      try {
         // Start building the query
         $query = "UPDATE $table SET ";
         $params = [];
         $types = '';

         // Dynamically append fields to the query
         foreach ($fields as $key => $value) {
               $query .= "$key = ?, ";
               $types .= $this->getBindType($key);
               $params[] = $value;
         }

         // Remove the last comma and space, and add the WHERE clause
         $query = rtrim($query, ', ') . " WHERE $titleColumn = ?";
         $types .= 's'; // titleColumn is a string
         $params[] = $titleValue;

         // Prepare the statement
         $stmt = $GLOBALS['connection']->prepare($query);

         // Bind parameters
         $stmt->bind_param($types, ...$params);

         // Execute the query
         if ($stmt->execute()) {
               return $this->successResponse(time(), ucfirst($table) . " updated successfully");
         } else {
               return $this->errorResponse(time(), "Failed to update $table");
         }
      } catch (Exception $e) {
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }
   
   private function getUserInfo($apikey) {
      $query = "SELECT user_id, username, email FROM users WHERE apikey =?";

      $smt = $GLOBALS['connection']->prepare($query);
      $smt->bind_param('s', $apikey);
      $smt->execute();
      $result = $smt->get_result();

      $sharedContent = [];
      while ($row = $result->fetch_assoc()) {
         $sharedContent[] = $row;
      }

      return $this->successResponse(time(), $sharedContent);
   }


   // Unified helper function for determining the bind type based on the field
   private function getBindType($key) {
      $typeMap = [
          // Common fields
          'Country' => 's',
          'Description' => 's',
          'PosterURL' => 's',
          'TrailerURL' => 's',
          'ScreenshotURL' => 's',
          'Runtime' => 'i',
          'Release_Year' => 'i',
          'Genre_ID' => 'i',
          // Movie-specific fields
          'Title' => 's',
          'Rating_ID' => 'i',
          // Series-specific fields
          'Name' => 's',
          'Seasons' => 'i',
          'RatingID' => 'i'
       ];
       return $typeMap[$key] ?? 's';
   }



   private function fixDB() {

      //populate films table, Genre_ID col
      //loop from 1 to 500
      //choose random number in range and populate
      //fill in genere_id in films table with a number between 1- 34, numbers can be repeated.
      try {
         // Loop from 1 to 500
         $GLOBALS['connection']->begin_transaction();
         for ($i = 1; $i <=  60; $i++) {
            // Choose a random number between 1 and 34
            $randomGenreID = rand(1, 33);

            // Prepare the update statement
            $query = "UPDATE Shows SET RatingID = ? WHERE Show_ID = ?";
            $stmt = $GLOBALS['connection']->prepare($query);
            $stmt->bind_param('ii', $randomGenreID, $i);

            // Execute the statement
            $stmt->execute();

            // Check for errors in the execution
            if ($stmt->error) {
               throw new Exception("Error updating film ID $i: " . $stmt->error);
            }
         }

         // Commit the transaction
         $GLOBALS['connection']->commit();
         echo "Database update successful.";
      } catch (Exception $e) {
         // Rollback the transaction in case of error
         $GLOBALS['connection']->rollback();
         echo "Database update failed: " . $e->getMessage();
      }
   }


   // ---------------------------------------- BIG MAIN FUNCTION ------------------------------------------ //
   

   private function handleReq() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         // Set the appropriate content type for JSON
         // header('Content-Type: application/json');

         // Decode the JSON data from the request body
         $requestData = json_decode(file_get_contents('php://input'), true);

         // Check if the JSON data is valid
         if ($requestData === null) {
            echo json_encode(array("message" => "Invalid JSON data " . http_response_code(400)));
            exit();
         }

         // $this->fixDB();

         if (isset($requestData['type']) && $requestData['type'] === "Register") { //========================
            // Process the request
            if (isset($requestData['name']) && isset($requestData['surname']) && isset($requestData['email']) && isset($requestData['password'])) {
               echo $this->registerUser($requestData['name'], $requestData['surname'], $requestData['email'], $requestData['password'], $requestData['username'], $requestData['admin']);
            } else {
               echo $this->errorResponse("User registration failed " .  http_response_code(400), time());
            }
         } elseif (isset($requestData["type"]) && $requestData["type"] === "Login") { //========================
            if (isset($requestData["email"]) && isset($requestData["password"])) {
               // Check user exists and pass correct, any API requests must use API key, store as cookie
               $email = $requestData["email"];

               // Get salt from database
               if($requestData['admin'] === "true") {
                  $salt = $this->retSaltAdmin($requestData['email']);
               } else {
                  $salt = $this->retSalt($requestData["email"]);
               }
               if (!$salt) {
                  echo $this->errorResponse("Email does not exist.", time());
               } else {
                  if (isset($_SESSION['api_key'])) {
                     // User is logged in
                     echo $this->errorResponse("Already registered", time());
                  } else {
                     $pass = $this->HashPassword($requestData["password"], $salt);
                     echo $this->login($email, $pass, $requestData['admin']);
                  }
                  //API must only accept valid requests.
               }
            } else {
               echo $this->errorResponse("Missing login information ", time());
            }
         } elseif (isset($requestData["type"]) && $requestData["type"] === "Logout") { //========================
            if (isset($requestData["email"]) && isset($requestData["password"])) {
               //clear  user session here
               //logout option  should be available only to logged in users.
               if (isset($_SESSION['user_id'])) {
                  // User is logged in
                  $this->logout($_SESSION['user_id']);
                  echo $this->successResponse(time(), "logged out");
               } else {
                  echo $this->errorResponse("You are logged in", time());
               }
            } else {
               echo $this->errorResponse("Failed to logout", time());
            }
         } else if(isset($requestData['type']) && $requestData['type'] === "GetUser") {
            if(isset($requestData['apikey'])) {
               echo $this->getUserInfo($requestData['apikey']);
            } else {
               echo $this->errorResponse(time(), "Missing apikey");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "GetAllMovies") { //========================
            if (isset($requestData['return'])) {
               if(isset($requestData['search'])) {
                  echo $this->getMovies($requestData['limit'],  $requestData['search'], $requestData['return'], $requestData['fuzzy'] = true);
               } else {
                  echo $this->getMovies($requestData['limit'],  null , $requestData['return'], $requestData['fuzzy'] = true);
               }
            } else {
               echo $this->errorResponse(time(), "Get movies failed");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "GetAllFavourites") { // =========================== CHECKED
            if (isset($requestData['apikey'])) {
               echo $this->getAllFavourites($requestData['apikey']);
            } else {
               echo $this->errorResponse(time(), "No API Key provided for favourites.");
            }
         } elseif (isset($requestData["type"]) && $requestData["type"] === "Favourite") { //========================
            if (isset($requestData["apikey"]) && isset($requestData["add"]) && (isset($requestData['show_id']) || isset($requestData['film_id']))) {
               if ($requestData["add"] === "true") {
                  if (isset($requestData['show_id'])) {
                     echo $this->addFavourite($requestData["apikey"], null, $requestData['show_id']);
                  } else {
                     echo $this->addFavourite($requestData["apikey"], $requestData['film_id'], null);
                  }
               } else {
                  if (isset($requestData['show_id'])) {
                     echo $this->deleteFavourite($requestData["apikey"], null, $requestData['show_id']);
                  } else {
                     echo $this->deleteFavourite($requestData["apikey"], $requestData['film_id'], null);
                  }
               }
            } else {
               echo $this->errorResponse(time(), "Could not access favourites");
            }

         } elseif (isset($requestData['type']) && $requestData['type'] === "AddMovies") { // =========================== CHECKED
            if (
                isset($requestData['title']) && 
                isset($requestData['genreID']) && 
                isset($requestData['ratingID']) && 
                isset($requestData['country']) &&
                isset($requestData['description']) && 
                isset($requestData['runtime']) && 
                isset($requestData['year']) &&
                isset($requestData['PostURL']) && 
                isset($requestData['VideoURL']) && 
                isset($requestData['ScreenURL'])
            ) {
                echo $this->addMovie(
                    $requestData['title'], 
                    $requestData['genreID'], 
                    $requestData['ratingID'], 
                    $requestData['country'], 
                    $requestData['description'], 
                    $requestData['runtime'], 
                    $requestData['year'], 
                    $requestData['PostURL'], 
                    $requestData['VideoURL'], 
                    $requestData['ScreenURL']
                );
            } else {
                echo $this->errorResponse(time(), "Missing values for adding movie.");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "Remove") { // =========================== CHECKED
            if (isset($requestData['item']) && isset($requestData['title'])) { 
                echo $this->delete($requestData['title'], $requestData['item']);
            } else {
                echo $this->errorResponse(time(), "Missing title or item type for deleting entity.");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "AddSeries") { // =========================== CHECKED
            if (
                isset($requestData['title']) && 
                isset($requestData['genreID']) && 
                isset($requestData['ratingID']) && 
                isset($requestData['country']) &&
                isset($requestData['description']) && 
                isset($requestData['runtime']) && 
                isset($requestData['year']) && 
                isset($requestData['seasons']) &&
                isset($requestData['PostURL']) && 
                isset($requestData['VideoURL']) && 
                isset($requestData['ScreenURL'])
            ) {
                echo $this->addSeries(
                    $requestData['title'], 
                    $requestData['genreID'], 
                    $requestData['ratingID'], 
                    $requestData['country'], 
                    $requestData['description'], 
                    $requestData['runtime'], 
                    $requestData['year'], 
                    $requestData['seasons'], 
                    $requestData['PostURL'], 
                    $requestData['VideoURL'], 
                    $requestData['ScreenURL']
                );
            } else {
                echo $this->errorResponse(time(), "Missing values for adding series.");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "ShareFilm") {
            if (isset($requestData['apikey']) && isset($requestData['username']) && isset($requestData['id'])) {
               echo $this->shareMovie($requestData['apikey'], $requestData['username'], $requestData['id']);
            } else {
               echo $this->errorResponse(time(), "Missing values for sharing film");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "AddRating") {
            if (isset($requestData['item'])) {
               if ($requestData['item'] === "movie" && isset($requestData['rating']) && isset($requestData['ID'])) {
                  echo $this->addRatings($requestData['ID'], null, $requestData['rating']);
               } else if (isset($requestData['ID']) && isset($requestData['rating'])) {
                  echo $this->addRatings(null, $requestData['ID'], $requestData['rating']);
               } else {
                  echo $this->errorResponse(time(), "Missing values for adding rating");
               }
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "GetAllSeries") {
            if (isset($requestData['return'])) {
               if(isset($requestData['search'])) {
                  echo $this->getSeries($requestData['limit'], $requestData['search'], $requestData['return'], $requestData['fuzzy'] = true);
               } else {
                  echo $this->getSeries($requestData['limit'],null,$requestData['return'], $requestData['fuzzy'] = true);

               }
            } else {
               echo $this->errorResponse("Get series failed", time());
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "ShareSeries") {
            if (isset($requestData['apikey']) && isset($requestData['username']) && isset($requestData['id'])) {
               echo $this->shareShow($requestData['apikey'], $requestData['username'], $requestData['id']);
            } else {
               echo $this->errorResponse(time(), "Missing values for sharing show");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "Search") {
            if (isset($requestData['search'])) {
               echo $this->searchBar($requestData['search']);
            } else {
               echo $this->errorResponse(time(), "Missing values for searching");
            }

         } elseif (isset($requestData['type']) && $requestData['type'] === "GetPopularMovies") {// Not done - not priority
         } elseif (isset($requestData['type']) && $requestData['type'] === "GetPopularSeries") {// Not done - not priority
         } elseif (isset($requestData['type']) && $requestData['type'] === "EditMovie") { // =========================== CHECKED
            if (isset($requestData['title']) && !empty($requestData['fields'])) {
                echo $this->editEntityByTitle('Films', 'Title', $requestData['title'], $requestData['fields']);
            } else {
                echo $this->errorResponse(time(), "Missing title or fields for editing movie.");
            }
         } elseif (isset($requestData['type']) && $requestData['type'] === "EditShow") { // =========================== CHECKED
            if (isset($requestData['name']) && !empty($requestData['fields'])) {
                echo $this->editEntityByTitle('Shows', 'Name', $requestData['name'], $requestData['fields']);
            } else {
                echo $this->errorResponse(time(), "Missing name or fields for editing series.");
            }

         } elseif (isset($requestData['type']) && $requestData['type'] === "GetShared") {
            if (isset($requestData['apikey'])) {
               echo $this->getShared($requestData['apikey']);
            } else {
               echo $this->errorResponse(time(), "Missing values for getting shared movies/shows.");
            }
      
         } elseif (isset($requestData['type']) && $requestData['type'] === "DeleteUser") {
            if (isset($requestData['email'])) {
                echo $this->deleteUser($requestData['email']);
            } else {
                echo $this->errorResponse(time(), "Email is required for deleting user");
            }
         } else {
            echo $this->errorResponse(time(), "Post parameters are missing ");
         }
      } else {
         echo json_encode(array("message" => "Method Not Allowed " . $_SERVER['REQUEST_METHOD'], "code" => http_response_code(405)));
      }
   }

}

$api = new API();
