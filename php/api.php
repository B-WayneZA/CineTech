<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Content-Type: application/json');

include 'config.php'; //include

class API
{

   public static function instance()
   {
      static $instance = null;
      if ($instance === null) $instance = new API();
      return $instance;
   }

   public function __construct()
   {
      $this->handleReq();
   }

   private function __destruct()
   {
      $GLOBALS['connection']->close();
   }

   private function errorResponse($time, $message)
   {
      return json_encode(["status" => "error", "timestamp" => $time, "data" => $message]);
   }

   private function successResponse($time, $data = [])
   {
      return json_encode(["status" => "success", "timestamp" => $time, "data" => $data]);
   }


   private function retSalt($email)
   {
      $stmt = $GLOBALS['connection']->prepare("SELECT salt FROM users WHERE email= ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();

      $result = $stmt->get_result()->fetch_assoc();
      return $result["salt"];
   }

   private function getSalt()
   {
      //generate random string that will be used to on the password to add flavor
      //be above 10 characters
      //dynamic created
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randomString = '';

      for ($i = 0; $i < 10; $i++) {
         $index = rand(0, strlen($characters) - 1);
         $randomString .= $characters[$index];
      }

      return $randomString;
   }

   private function HashPassword($psw, $salt)
   {
      //add salt to password before hashing
      $pswSalt = $psw . $salt;
      $hashedPassword = hash('sha256', $pswSalt);

      return $hashedPassword;
   }

   private function getApiKey()
   {
      $key = bin2hex(random_bytes(16));
      return $key;
   }

   public function registerUser($name, $surname, $email, $password, $username)
   {
      // API endpoint URL

      //METHOD POST
      //check for missing, blank, incorrect fields
      //validate email string

      // Data to be sent in the request body (in JSON format)
      // Validate input
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
      $stmt = $GLOBALS['connection']->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
         return $this->errorResponse(time(), "User already exists");
      }
      // Hash password
      // Generate salt
      $salt = $this->getSalt();

      // Hash password with salt

      $hashed_password = $this->HashPassword($password, $salt);
      $date = new DateTime();
      $lastLogin =  $date->getTimestamp();
      echo "Checked timestamp \n";

      $apiKey = $this->getApiKey();
      // Insert user into database
      $stmt = $GLOBALS['connection']->prepare("INSERT INTO users (name, surname, email, password,salt,apiKey, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssss", $name, $surname, $email, $hashed_password, $salt, $apiKey, $username);

      if ($stmt->execute()) {
         return $this->successResponse($lastLogin, ["apiKey" => $apiKey]);
      } else {
         //var_dump($stmt->error)
         return $this->errorResponse($lastLogin, $stmt->error . 500);
      }
   }

   public function login($email, $pass)
   {
      if (!$pass) {
         //unable to login
         return $this->errorResponse("Incorrect password", time());
      } else if ($this->checkCredentials($email, $pass)) {
         //create a new api key and save it
         $stmt = $GLOBALS['connection']->prepare("SELECT id, apiKey FROM users WHERE email = ? AND password = ?");
         $stmt->bind_param("ss", $email, $pass);
         $stmt->execute();
         $result = $stmt->get_result();

         $row = $result->fetch_assoc();

         if ($result->num_rows > 0) {
            // Credentials are correct, return the user ID
            session_start();
            $_SESSION["APIkey"] = $row["apikey"];
            $key = $row["apiKey"];

            $api = array("apikey" => $key);
            return $this->successResponse(time(), json_encode($api));
         } else {
            return $this->errorResponse("Incorrect login details", time());
         }
      } else {
         //wrong credentials
         return $this->errorResponse("Incorrect login details", time());
      }
   }

   public function logout($apiKey)
   {
      session_start(); // Start the session
      $_SESSION = array(); // Unset all session variables
      session_destroy(); // Destr

      $cookie_name = $apiKey;
      setcookie($cookie_name, "", time() - 3600, "/");
   }

   public function deleteUser($apiKey)
   {
      $stmt = $GLOBALS['connection']->prepare("SELECT id FROM users WHERE apikey=?");
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         return $this->errorResponse("User does not exists", time());
      } else {
         $stmt = $GLOBALS['connection']->prepare("DELETE FROM users WHERE apikey=?");
         $stmt->bind_param("s", $apiKey);
         $stmt->execute();
         $result = $stmt->get_result();

         return $this->successResponse(time(), "User successfully deleted");
      }
   }

   public function getUserRecommendations($apiKey)
   {
      $stmt = $GLOBALS['connection']->prepare("SELECT id FROM users WHERE apikey = ?");
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         return $this->errorResponse(time(), "User does not exists");
      } else {
         $stmt = $GLOBALS["connection"]->prepare("SELECT ");
      }
   }

   public function getMovies($sort, $order, $search, $return, $fuzzy)
   {
      $table = $GLOBALS['connection'];
      if ($return === "['*']") {
         $query = "SELECT * FROM films";
      } else {
         $query = "SELECT f.Title, g.Genre, r.IMDB_score, r.IMDB_votes, r.TMDB_popularity, r.TMDB_score, r.CineTech_Rating, f.Country, f.Description, f.Runtime, f.Release_Year FROM Films f JOIN Genre g ON f.Genre_ID = g.Genre_ID JOIN Rating r ON f.Rating_ID = r.Rating_ID";
      }

      //add filters on table
      if (isset($search) && is_array($search) && count($search) > 0) {
         $filters = array();
         foreach ($search as $column => $value) {
            // Escape column and value to prevent SQL injection
            $column = $GLOBALS['connection']->real_escape_string($column);
            $value = $GLOBALS['connection']->real_escape_string($value);

            if ($column === 'year') {
               $year = (int) $value;
               $filters[] = "Release_Year >= $year";
            } else if ($column === 'rating') {
               $rating = (int) $value;
               $filters[] = "CineTech_Rating >= $rating";
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

      if (isset($sort)) {
         $query .= ' ORDER BY ' . $sort;
      }

      $stmt = $table->prepare($query);

      // Execute the query
      $stmt->execute();

      // Get the result set
      $result = $stmt->get_result();

      // Check if any rows are returned
      if ($result->num_rows > 0) {
         $listings = array();
         // Fetch rows and store in array
         while ($row = $result->fetch_assoc()) {
            foreach ($return as $column) {

               $listing[$column] = $row[$column];
            }
            $listings[] = (object) $listing;
         }
         // Add the listing to the array

         return $this->successResponse(time(), $listings);
      } else {
         // Return error response if no listings found
         return $this->errorResponse(time(), "No movies found");
      }
   }
   public function getSeries($sort, $order, $search, $return)
   {
      $query = "SELECT ";

      // Add return columns to the query
      if (!empty($return)) {
         $query .= implode(", ", $return);
      } else {
         $query .= "*"; // Default to selecting all columns
      }

      $query = "SELECT s.Name , s.Seasons , g.Genre, r.IMDB_score, r.IMDB_votes, r.TMDB_popularity, r.TMDB_score, r.CineTech_Rating, s.Country, s.Description, s.Runtime, s.Release_Year FROM Shows s JOIN Genre g ON s.Genre_ID = g.Genre_ID JOIN Rating r ON s.RatingID = r.Rating_ID";

      // Add search and filter conditions
      if (!empty($search)) {
         // Add conditions based on search parameters
         if (isset($search['genre'])) {
            $genres = $search['genre'];
            $query .= " AND genre LIKE $genres ";
         }
         // Add conditions for other search parameters (e.g., language, production_country, keyword)  
      }

      // Add sorting and ordering
      if (!empty($sort)) {
         $query .= "ORDER BY $sort ";

         if (!empty($order)) {
            $query .= "$order";
         }
      }

      // Prepare and execute the query using prepared statements
      $stmt = $GLOBALS["connection"]->prepare($query);
      $stmt->execute();
      $result = $stmt->get_result();

      // Fetch results as an associative array
      if ($result->num_rows > 0) {
         $listings = array();
         // Fetch rows and store in array
         while ($row = $result->fetch_assoc()) {
            foreach ($return as $column) {
               $listing[$column] = $row[$column];
            }
            $listings[] = (object) $listing;
         }
         // Add the listing to the array

         return $this->successResponse(time(), $listings);
      } else {
         return $this->errorResponse(time(), "No shows found");
      }
   }
   private function getRatingID($ratingType)
   { //1.
      try {
         $column = '';
         // it would be nice if we could determine the column based on the rating type
         switch ($ratingType) { //adding rating type will help i wont lie 
            case 'IMDB':
               $column = 'IMDB_score';
               $column = 'IMDB_votes';
               break;
            case 'TMDB':
               $column = 'TMDB_score';
               $column = 'TMDB_popularity';
               break;
            case 'CineTech':
               $column = 'CineTech_Rating';
               break;
         }
         // SQL query using the determined column
         $query = "SELECT Rating_ID, $column FROM Rating WHERE Rating_Type = :ratingType";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bindParam(':ratingType', $ratingType, PDO::PARAM_STR);
         $stmt->execute();
         return $stmt->fetchColumn();
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }


   private function getCineTechRating($filmOrShowID, $type)
   { //2.
      try {
         if ($type === 'film') {
            $query = "SELECT Rating FROM CineTech_Film_Rating WHERE Films_ID = :id";
         } else //shows
         {
            $query = "SELECT Rating FROM CineTech_Show_Rating WHERE Show_ID = :id";
         }
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bindParam(':id', $filmOrShowID, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->fetchColumn();
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }
   public function addRatings($filmID, $showID, $rating)
   {

      //insert cintech rating
      if (isset($filmID)) {
         $query = "INSERT INTO CineTech_Film_Rating WHERE (Films_ID, Rating) VALUES (?,?)";
         $stmt = $GLOBALS["connection"]->prepare($query);
         $stmt->bind_param("id", $filmID, $rating);

         if ($stmt->execute()) {
            return $this->successResponse(time(), "CineTech Rating added successfully");
         } else {
            return $this->errorResponse(time(), "Failed to add CineTech Rating");
         }
      } else {
         $query = "INSERT INTO CineTech_Show_Rating WHERE (Show_ID, Rating) VALUES (?,?)";
         $stmt = $GLOBALS["connection"]->prepare($query);
         $stmt->bind_param("id", $showID, $rating);

         if ($stmt->execute()) {
            return $this->successResponse(time(), "CineTech Rating added successfully");
         } else {
            return $this->errorResponse(time(), "Failed to add CineTech Rating ");
         }
      }
   }
   public function getAllFavourites($apikey)
   { //need to change SQL
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT id FROM users WHERE apiKey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $apikey);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
            // Handle case where API key does not correspond to any user
            return $this->errorResponse("User not found for API key: " . $apikey, time());
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["id"];

         // Query to fetch favorites from user_favorites table
         $query = "SELECT * FROM user_favorites_info WHERE userID=?";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bind_param('i', $userID);
         $stmt->execute();
         $result = $stmt->get_result();

         // Check if any favorites are found
         if ($result->num_rows > 0) {
            $favorites = array();

            // Fetch each favorite and extract listing information from the listings table
            while ($row = $result->fetch_assoc()) {

               // Query to fetch listing information from the listings table
               $Query = "SELECT v.films_id , f.Title ,f.Country ,f.Description,f.Release_Year ,v.shows_id,s.Name,s.Seasons,s.Country,s.Release_Year FROM favourites v JOIN Films f ON f.Films_ID = v.films_id JOIN Shows s ON s.Show_id = v.shows_id WHERE user_id =?";
               $Stmt = $GLOBALS['connection']->prepare($userID);
               $Stmt->bind_param('i', $userID);
               $Stmt->execute();
               $Result = $Stmt->get_result();

               // Check if listing information is found
               if ($Result->num_rows > 0) {
                  $Data = $Result->fetch_assoc();
                  $favorites[] = $Data;
               }
            }

            // Return success response with favorites data
            return $this->successResponse(time(), $favorites);
         } else {
            // Return error response if no favorites found
            return $this->errorResponse("No favorites found for user with API key: " . $apikey, time());
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }

   private function addFavourite($api, $listingid)
   { ///need to do
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT id FROM users WHERE apiKey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $api);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
            // Handle case where API key does not correspond to any user
            return $this->errorResponse("User not found for API key: " . $api, time());
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["id"];

         // Retrieve listing information based on listing ID
         $listingQuery = "SELECT title, price, location, images FROM listings WHERE id=?";
         $listingStmt = $GLOBALS['connection']->prepare($listingQuery);
         $listingStmt->bind_param('i', $listingid);
         $listingStmt->execute();
         $listingResult = $listingStmt->get_result();

         if ($listingResult->num_rows == 0) {
            // Handle case where listing ID does not exist
            return $this->errorResponse(time(), "Listing not found for ID: " . $listingid);
         }

         $listingData = $listingResult->fetch_assoc();


         // Insert favorite into database
         $insertQuery = "INSERT INTO user_favorites_info () VALUES (?, ?, ?, ?, ?, ?)";
         $insertStmt = $GLOBALS['connection']->prepare($insertQuery);
         $insertStmt->bind_param('iisdss');

         if ($insertStmt->execute()) {
            return $this->successResponse(time(), "Favorite added successfully.");
         } else {
            // Handle SQL execution error
            // Uncomment the following line for debugging
            // var_dump($insertStmt->error);
            return $this->errorResponse("Error adding favorite: " . $insertStmt->error, time());
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }
   private function delete($title, $type)
   {
      if ($type === "film") {
         $stmt = $GLOBALS['connection']->prepare("DELETE FROM Films WHERE Title = ?");
         $stmt->bind_param("s", $title);

         if ($stmt->execute()) {
            return $this->successResponse(time(), "Film deleted successfully.");
         } else {
            return $this->errorResponse(time(), $stmt->error);
         }
      } else {
         $stmt = $GLOBALS['connection']->prepare("DELETE FROM Shows WHERE Name = ?");
         $stmt->bind_param("s", $title);

         if ($stmt->execute()) {
            return $this->successResponse(time(), "Show deleted successfully.");
         } else {
            return $this->errorResponse(time(), $stmt->error);
         }
      }
   }
   private function deleteFavourite($api, $filmID)
   {
      try {
         // Retrieve user ID based on API key
         $uIDQuery = "SELECT id FROM users WHERE apiKey=?";
         $uIDStmt = $GLOBALS['connection']->prepare($uIDQuery);
         $uIDStmt->bind_param('s', $api);
         $uIDStmt->execute();
         $uIDResult = $uIDStmt->get_result();

         if ($uIDResult->num_rows == 0) {
            // Handle case where API key does not correspond to any user
            return $this->errorResponse("User not found for API key: " . $api, time());
         }

         $userData = $uIDResult->fetch_assoc();
         $userID = $userData["id"];

         // Delete favorite from database
         $deleteQuery = "DELETE FROM user_favorites_info WHERE userID=? AND FilmID=?";
         $deleteStmt = $GLOBALS['connection']->prepare($deleteQuery);
         $deleteStmt->bind_param('ii', $userID, $filmID);

         if ($deleteStmt->execute()) {
            return $this->successResponse(time(), "Removed film from favorites.");
         } else {
            // Handle SQL execution error
            return $this->errorResponse(time(), "Error deleting favorite: " . $deleteStmt->error);
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse(time(), "An error occurred: " . $e->getMessage());
      }
   }

   private function checkCredentials($email, $password)
   {
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

   private function getPopularMovies()
   {
      //Not sure what to do please help me with the guidance 
   }

   private function getNewMovies()
   { //get movies from this year 3.
      try {
         $query = "SELECT * FROM Films WHERE YEAR(ReleaseDate) = YEAR(CURDATE()) ";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }


   private function addMovie($title, $genre, $ratingArr, $country, $description, $runtime, $year, $PostURL, $VideoURL, $ScreenURL)
   { //4.
      try {

         $query = "INSERT INTO Film (Title, Genre, Country, Description, Runtime, Year, PostURL, VideoURL, ScreenURL) 
         VALUES (:title, :genre, :country, :description, :runtime, :year, :postURL, :videoURL, :screenURL)";
         $stmt = $GLOBALS['connection']->prepare($query);
         $stmt->bindParam(':title', $title, PDO::PARAM_STR);
         $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
         $stmt->bindParam(':country', $country, PDO::PARAM_STR);
         $stmt->bindParam(':description', $description, PDO::PARAM_STR);
         $stmt->bindParam(':runtime', $runtime, PDO::PARAM_INT);
         $stmt->bindParam(':year', $year, PDO::PARAM_INT);
         $stmt->bindParam(':postURL', $PostURL, PDO::PARAM_STR);
         $stmt->bindParam(':videoURL', $VideoURL, PDO::PARAM_STR);
         $stmt->bindParam(':screenURL', $ScreenURL, PDO::PARAM_STR);
         $stmt->execute();

         $filmID = $GLOBALS['connection']->lastInsertId();
         foreach ($ratingArr as $ratingType => $ratingValue) {
            $ratingID = $this->getRatingID($ratingType);
            $ratingQuery = "INSERT INTO Film_Rating (Films_ID, Rating_ID, Rating) VALUES (:filmID, :ratingID, :ratingValue)";
            $ratingStmt = $GLOBALS['connection']->prepare($ratingQuery);
            $ratingStmt->bindParam(':filmID', $filmID, PDO::PARAM_INT);
            $ratingStmt->bindParam(':ratingID', $ratingID, PDO::PARAM_INT);
            $ratingStmt->bindParam(':ratingValue', $ratingValue, PDO::PARAM_STR);
            $ratingStmt->execute();
         }
      } catch (Exception $e) {
         // Handle any exceptions thrown during SQL execution
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
      }
   }
   private function addSeries($title, $genre, $ratingArr, $country, $description, $runtime, $year, $seasons, $PostURL, $VideoURL, $ScreenURL)
   { //5.
      $query = "INSERT INTO Shows (Title, Genre, Country, Description, Runtime, Year, Seasons, PostURL, VideoURL, ScreenURL) 
      VALUES (:title, :genre, :country, :description, :runtime, :year, :seasons, :postURL, :videoURL, :screenURL)";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
      $stmt->bindParam(':country', $country, PDO::PARAM_STR);
      $stmt->bindParam(':description', $description, PDO::PARAM_STR);
      $stmt->bindParam(':runtime', $runtime, PDO::PARAM_INT);
      $stmt->bindParam(':year', $year, PDO::PARAM_INT);
      $stmt->bindParam(':seasons', $seasons, PDO::PARAM_INT);
      $stmt->bindParam(':postURL', $PostURL, PDO::PARAM_STR);
      $stmt->bindParam(':videoURL', $VideoURL, PDO::PARAM_STR);
      $stmt->bindParam(':screenURL', $ScreenURL, PDO::PARAM_STR);
      $stmt->execute();

      $showID = $GLOBALS['connection']->lastInsertId();
      foreach ($ratingArr as $ratingType => $ratingValue) {
         $ratingID = $this->getRatingID($ratingType);
         $ratingQuery = "INSERT INTO Show_Rating (Show_ID, Rating_ID, Rating) VALUES (:showID, :ratingID, :ratingValue)";
         $ratingStmt = $GLOBALS['connection']->prepare($ratingQuery);
         $ratingStmt->bindParam(':showID', $showID, PDO::PARAM_INT);
         $ratingStmt->bindParam(':ratingID', $ratingID, PDO::PARAM_INT);
         $ratingStmt->bindParam(':ratingValue', $ratingValue, PDO::PARAM_STR);
         $ratingStmt->execute();
      }
   }

   private function getRatingAvgFilm($filmId)
   {
      $query = "SELECT r.CineTech_Rating,(SELECT AVG(CineTech_Rating) FROM Rating WHERE Rating_ID = f.Rating_ID) AS CineTech_R FROM Films f JOIN Rating r ON f.Rating_ID = r.Rating_ID WHERE f.Films_ID = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("i", $filmId);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row;
   }
   private function getRatingAvgShow($showId)
   {
      $query = "SELECT r.CineTech_Rating,(SELECT AVG(CineTech_Rating) FROM Rating WHERE Rating_ID = f.Rating_ID) AS CineTech_R FROM Films f JOIN Rating r ON f.Rating_ID = r.Rating_ID WHERE f.Films_ID = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("i", $showId);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row;
   }

   private function getUserID($apiKey)
   {
      $query = "SELECT id FROM users WHERE api_key = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         // Handle case where API key does not correspond to any user
         return $this->errorResponse(time(), "User not found for API key: " . $apiKey);
      }
      $row = $result->fetch_assoc();

      return $row;
   }
   private function getUserIDusername($username)
   {
      $query = "SELECT id FROM users WHERE username = ?";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         // Handle case where API key does not correspond to any user
         return $this->errorResponse(time(), "User not found for: " . $username);
      }
      $row = $result->fetch_assoc();

      return $row;
   }

   private function shareMovie($apiKey, $username, $filmID)
   {

      $userID = $this->getUserID($apiKey);
      $receiverID = $this->getUserIDusername($username);

      $query = "INSERT INTO Shared_movies WHERE (Receiver_ID, Sender_ID, Film_shared) VALUES (?,?,?)";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("iii", $receiverID, $userID, $filmID);
      $stmt->execute();

      if ($stmt->execute()) {
         return $this->successResponse(time(), "Movie shared successfully");
      } else {
         return $this->errorResponse(time(), "Error sharing movie");
      }
   }

   private function shareShow($apiKey, $username, $showID)
   {
      $userID = $this->getUserID($apiKey);
      $receiverID = $this->getUserIDusername($username);

      $query = "INSERT INTO Shared_shows WHERE (Reciever_ID, Sender_id, Show_ID) VALUES (?,?,?)";
      $stmt = $GLOBALS['connection']->prepare($query);
      $stmt->bind_param("iii", $receiverID, $userID, $showID);
      $stmt->execute();

      if ($stmt->execute()) {
         return $this->successResponse(time(), "Show shared successfully");
      } else {
         return $this->errorResponse(time(), "Error sharing movie");
      }
   }
   private function editMovie()
   {
   }
   private function handleReq()
   {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         // Set the appropriate content type for JSON
         header('Content-Type: application/json');

         // Decode the JSON data from the request body
         $requestData = json_decode(file_get_contents('php://input'), true);

         // Check if the JSON data is valid
         if ($requestData === null) {
            echo json_encode(array("message" => "Invalid JSON data " . http_response_code(400)));
            exit();
         }

         if (isset($requestData['type']) && $requestData['type'] === "Register") { //========================
            // Process the request
            if (isset($requestData['name']) && isset($requestData['surname']) && isset($requestData['email']) && isset($requestData['password'])) {
               echo $this->registerUser($requestData['name'], $requestData['surname'], $requestData['email'], $requestData['password'], $requestData['username']);
            } else {
               echo $this->errorResponse("User registration failed " .  http_response_code(400), time());
            }
         } else if (isset($requestData["type"]) && $requestData["type"] === "Login") { //========================
            if (isset($requestData["email"]) && isset($requestData["password"])) {
               //check user exists and pass correct, any API requests must use API key, store as cookie
               $email = $requestData["email"];

               //get salt from database
               $salt = $this->retSalt($requestData["email"]);
               if (!$salt) {
                  echo $this->errorResponse("Email does not exist.", time());
               } else {
                  if (isset($_SESSION['api_key'])) {
                     // User is logged in
                     echo $this->errorResponse("Already registered", time());
                  } else {
                     $pass = $this->HashPassword($requestData["password"], $salt);
                     echo $this->login($email, $pass);
                  }
                  //API must only accept valid requests.
               }
            } else {
               echo $this->errorResponse("Missing login information ", time());
            }
         } else if (isset($requestData["type"]) && $requestData["type"] === "Logout") { //========================
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
         } else if (isset($requestData['type']) && $requestData['type'] === "GetAllMovies") { //========================
            if (isset($requestData['apikey']) && isset($requestData['return'])) {
               echo $this->getMovies($requestData['apikey'], $requestData['limit'], $requestData['sort'], $requestData['order'], $requestData['search'], $requestData['fuzzy'] = true, $requestData['return']);
            } else {
               echo $this->errorResponse("Get listings failed", time());
            }
         } else if (isset($requestData['type']) && $requestData['type'] === "GetAllFavourites") { //===========================
            if (isset($requestData['apikey'])) {
               echo $this->getAllFavourites($requestData['apikey']);
            } else {
               echo $this->errorResponse("No API Key provided for favourites.", time());
            }
         } else if (isset($requestData["type"]) && $requestData["type"] === "Favourite") { //========================
            if (isset($requestData["apikey"]) && isset($requestData["listingID"]) && isset($requestData["add"])) {
               if ($requestData["add"] === "true") {
                  echo $this->addFavourite($requestData["apikey"], $requestData["listingID"]);
               } else {
                  echo $this->deleteFavourite($requestData["apikey"], $requestData["listingID"]);
               }
            } else {
               echo $this->errorResponse("Could not access favourites", time());
            }
         } else if (isset($requestData['type']) && $requestData['type'] === "AddMovies") {
         } else if (isset($requestData['type']) && $requestData['type'] === "RemoveMovies") {
         } else if (isset($requestData['type']) && $requestData['type'] === "AddSeries") {
         } else if (isset($requestData['type']) && $requestData['type'] === "RemoveSeries") {
         } else if (isset($requestData['type']) && $requestData['type'] === "AddFeatured") {
         } else if (isset($requestData['type']) && $requestData['type'] === "ShareFilm") {
         } else if (isset($requestData['type']) && $requestData['type'] === "AddRating") {
         } else if (isset($requestData['type']) && $requestData['type'] === "GetAllSeries") {
         } else if (isset($requestData['type']) && $requestData['type'] === "ShareSeries") {
         } else if (isset($requestData['type']) && $requestData['type'] === "GetPopularMovies") {
         } else if (isset($requestData['type']) && $requestData['type'] === "GetPopularSeries") {
         } else if (isset($requestData['type']) && $requestData['type'] === "EditMovie") {
         } else if (isset($requestData['type']) && $requestData['type'] === "EditSerie") {
         } else {
            echo $this->errorResponse("Post parameters are missing", time());
         }
         // Send a JSON response
      } else {
         echo json_encode(array("message" => "Method Not Allowed", "code" => http_response_code(405)));
      }
   }
}

// Instantiate API object
$api = new API();
