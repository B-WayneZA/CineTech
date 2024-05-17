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

   private function errorResponse($time, $message){
      return json_encode(["status" => "error", "timestamp" => $time, "data" => $message]);
   }

   private function successResponse($time, $data = []){
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

   private function HashPassword($psw, $salt){
      //add salt to password before hashing
      $pswSalt = $psw . $salt;
      $hashedPassword = hash('sha256', $pswSalt);

      return $hashedPassword;
   }

   private function getApiKey(){
      $key = bin2hex(random_bytes(16));
      return $key;
   }

   public function registerUser($name, $surname, $email, $password, $username) {
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
         return $this->errorResponse(time(),"Invalid email format");
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
         return $this->errorResponse(time(),"User already exists");
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
         return $this->errorResponse($lastLogin,$stmt->error . 500);
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

   public function logout($apiKey){
      session_start(); // Start the session
      $_SESSION = array(); // Unset all session variables
      session_destroy(); // Destr

      $cookie_name = $apiKey;
      setcookie($cookie_name, "", time() - 3600, "/");
   }

   public function deleteUser($apiKey) {
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

   public function getUserRecommendations($apiKey) {
      $stmt = $GLOBALS['connection']->prepare("SELECT id FROM users WHERE apikey = ?");
      $stmt->bind_param("s", $apiKey);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows == 0) {
         return $this->errorResponse(time(),"User does not exists");
      } else {
         $stmt = $GLOBALS["connection"]->prepare("SELECT ");
      }

   }

   public function getMovies($sort, $order, $search, $return) {

   }
   public function getSeries($sort, $order, $search, $return) {
      $query = "SELECT ";

      // Add return columns to the query
      if (!empty($return)) {
          $query .= implode(", ", $return);
      } else {
          $query .= "*"; // Default to selecting all columns
      }
  
      $query .= " FROM shows WHERE 1=1 ";
  
      // Add search and filter conditions
      if (!empty($search)) {
          // Add conditions based on search parameters
          if (isset($search['genre'])) {
              $genres = implode("', '", $search['genre']);
              $query .= "AND genre IN ('$genres') ";
          }
          // Add conditions for other search parameters (e.g., language, production_country, keyword)
          // ...
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
      $series = [];
      while ($row = $result->fetch_assoc()) {
          $series[] = $row;
      }
  
      // Return JSON response with series data
      return json_encode(["series" => $series]);

   }

   public function addRatings() {
      //insert cintech rating
   }
   public function getAllFavourites($apikey) {//need to change SQL
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
               $listingID = $row["listingID"];

               // Query to fetch listing information from the listings table
               $listingQuery = "SELECT id, title, location, price, images FROM listings WHERE id=?";
               $listingStmt = $GLOBALS['connection']->prepare($listingQuery);
               $listingStmt->bind_param('i', $listingID);
               $listingStmt->execute();
               $listingResult = $listingStmt->get_result();

               // Check if listing information is found
               if ($listingResult->num_rows > 0) {
                  $listingData = $listingResult->fetch_assoc();
                  $favorites[] = $listingData;
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




   private function addFavourite($api, $listingid){///need to do
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
            return $this->errorResponse("Listing not found for ID: " . $listingid, time());
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

   private function deleteFavourite($api, $filmID) {
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
         return $this->errorResponse("An error occurred: " . $e->getMessage(), time());
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

   private function getPopularMovies(){

   }
   private function getNewMovies() {

   }

   private function addMovie($title, $genre, $ratingArr, $country, $description, $runtime, $year, $PostURL, $VideoURL, $ScreenURL) {

   }
   private function addSeries($title, $genre, $ratingArr, $country, $description, $runtime, $year , $seasons, $PostURL, $VideoURL, $ScreenURL) {

   }


   private function getRatingAvgFilm($filmId) {

   }
   private function getRatingAvgShow($showId) {

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

?>