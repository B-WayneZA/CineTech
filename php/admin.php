<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the deleteTitle and deleteType are set
    if (isset($_POST['deleteTitle']) && isset($_POST['deleteType'])) {
        // Construct the request body
        $requestData = array(
            "type" => "Remove",
            "item" => $_POST['deleteType'] === "Movie" ? "film" : "show", // Convert deleteType to item
            "title" => $_POST['deleteTitle']
        );

        // Convert the request data to JSON
        $jsonData = json_encode($requestData);

        // Set API endpoint URL
        $apiUrl = "https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php";

        // Initialize cURL session
        $curl = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // Set request method to POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData); // Set request body
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as string
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            echo json_encode(array("status" => "error", "message" => 'Error: ' . curl_error($curl)));
        } else {
            // Output response
            echo $response;
        }

        // Close cURL session
        curl_close($curl);
    } else {
        echo json_encode(array("status" => "error", "message" => "Please provide both title and type for deleting."));
    }
}
?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineTech Admin Page</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" href="../img/4.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div id="wrapper">
        <div class="grid-container">
            <!-- Header -->
            <header class="header">
                <img src="../img/4.png" alt="" class="logo">
                <h1>CineTech.</h1>
            </header>

            <!-- Main -->
            <main class="main-container">
                <div class="main-title">
                    <h2>Dashboard</h2>
                </div>

                <!-- this is the boxes above that show the inventory  -->
                <div class="main-cards">
                    <div class="card">
                        <div class="card-inner">
                            <h3>MOVIES</h3>
                            <span class="material-icons-outlined">movie</span>
                        </div>
                        <h1>1000</h1>
                    </div>

                    <div class="card">
                        <div class="card-inner">
                            <h3>SERIES</h3>
                            <span class="material-icons-outlined">tv</span>
                        </div>
                        <h1>500</h1>
                    </div>

                    <div class="card">
                        <div class="card-inner">
                            <h3>USERS</h3>
                            <span class="material-icons-outlined">groups</span>
                        </div>
                        <h1>1500</h1>
                    </div>

                    <div class="card">
                        <div class="card-inner">
                            <h3>ALERTS</h3>
                            <span class="material-icons-outlined">notification_important</span>
                        </div>
                        <h1>56</h1>
                    </div>
                </div>
                <div class="charts">
                    <div class="charts-card">
                        <h2 class="chart-title">Popular Genres</h2>
                        <div id="bar-chart"></div>
                    </div>

                    <div class="charts-card">
                        <h2 class="chart-title">Movie/Series Watched</h2>
                        <div id="area-chart"></div>
                    </div>
                </div>

                <!-- this is the part that deals with input fields -->
                <div class="sql-query-row">
                    <!-- this the delete movie/series block -->
                    <div class="delete-movie-series">
                        <h2>Delete Movie or Series</h2>
                        <p>Remove Movies/Series no longer needed in the database.</p>
                        <form id="deleteForm" action="" method="post">
                            <input type="text" id="deleteTitle" name="deleteTitle" placeholder="Title" required>
                            <!-- dropdown here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Movie/Series
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="deleteMovie" name="deleteType" value="Movie"
                                            checked>
                                        <label for="deleteMovie">Movie</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="deleteSeries" name="deleteType" value="Series">
                                        <label for="deleteSeries">Series</label>
                                    </li>
                                </ul>
                            </div>
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                    <!-- this is the add to movie/series block -->
                    <div class="add-movie-series">
                        <h2>Add Movie or Series</h2>
                        <p>This box will add a movie or series to the database.</p>
                        <form id="addForm" action="" method="post">
                            <input type="text" id="addTitle" name="addTitle" placeholder="Title" required>
                            <!-- dropdown here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Movie/Series
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="addMovie" name="addType" value="Movie" checked>
                                        <label for="addMovie">Movie</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="addSeries" name="addType" value="Series">
                                        <label for="addSeries">Series</label>
                                    </li>
                                </ul>
                            </div>
                            <textarea id="addDescription" name="addDescription" placeholder="Description" required></textarea>
                            <input type="number" id="addRating" name="addRating" placeholder="Rating" min="0" max="10"
                                required>
                            <input type="text" id="addGenres" name="addGenres" placeholder="Genres" required>
                            <input type="number" id="addYearReleased" name="addYearReleased"
                                placeholder="Year Released" required>
                            <button type="submit">Add</button>
                        </form>
                    </div>

                    <!-- this is the edit to movie/series block -->
                    <div class="edit-movie-series">
                        <h2>Edit Movie or Series</h2>
                        <p>This box will edit a movie or series from the database.</p>
                        <form id="editForm" action="" method="post">
                            <input type="text" id="editTitle" name="editEntityTitle" placeholder="Title" required>
                            <!-- dropdown for what we editing here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Movie/Series
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="editMovie" name="editEntityType" value="movie" checked>
                                        <label for="editMovie">Movie</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="editSeries" name="editEntityType" value="series">
                                        <label for="editSeries">Series</label>
                                    </li>
                                </ul>
                            </div>
                            <!-- dropdown for what part we editing here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit Here
                                </button>
                                <ul class="dropdown-menu">
                                    <li><input type="radio" id="editTitle" name="editEntityFields" value="title"><label for="editTitle">Title</label></li>
                                    <li><input type="radio" id="editDescription" name="editEntityFields" value="description"><label for="editDescription">Description</label></li>
                                    <li><input type="radio" id="editPoster" name="editEntityFields" value="poster"><label for="editPoster">Poster</label></li>
                                    <li><input type="radio" id="editRating" name="editEntityFields" value="rating"><label for="editRating">Rating</label></li>
                                    <li><input type="radio" id="editGenre" name="editEntityFields" value="genre"><label for="editGenre">Genre</label></li>
                                    <li><input type="radio" id="editReleaseYear" name="editEntityFields" value="release_year"><label for="editReleaseYear">Release Year</label></li>
                                </ul>
                            </div>
                            <input type="text" id="editValue" name="editValue" placeholder="Edit" required>
                            <button type="submit">Edit</button>
                        </form>
                    </div>

                    <!-- this is the delete user block -->
                    <div class="delete-block-user">
                        <h2>Delete User from Database</h2>
                        <p>This box will remove a user from the database.</p>
                        <form id="deleteUserForm" action="" method="post">
                            <input type="email" id="deleteEmail" name="deleteEmail" placeholder="Email" required>
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </main>
            <!-- End Main -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="../js/admin.js"></script>
</body>

</html>
