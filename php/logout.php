<?php
  session_start();
  // Unset all session variables
  session_unset(); 
  // Destroy the session
  session_destroy(); 

  // Prevent caching
  header("Cache-Control: no-cache, no-store, must-revalidate"); 
  header("Pragma: no-cache"); 
  header("Expires: 0"); 

  header("Location: ../html/launch.html");
  exit();
?>
