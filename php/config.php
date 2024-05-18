<?php
// Load environment variables from .env file
define('dbServer', 'wheatley.cs.up.ac.za');
define('dbUsername', 'u23535246');
define('dbPassword', 'QVA7DVZF34LV7J4PB4YCWG7RCMZGHDVK');
define('dbName', 'u23535246_CineTechDB');

$connection = new mysqli(dbServer, dbUsername, dbPassword, dbName); // Using the constants here


// Create a database connection

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
