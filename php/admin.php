<?php
session_start();

function makeApiRequest($data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteItem($title, $itemType)
{
    $data = array(
        "type" => "Remove",
        "title" => $title,
        "item" => strtolower($itemType)
    );

    $response = makeApiRequest($data);

    if ($response['status'] === 'success') {
        echo '<script>alert("Successfully deleted ' . $itemType . ': ' . $title . '");</script>';
    } else {
        echo '<script>alert("Failed to delete ' . $itemType . ': ' . $response['data'] . '");</script>';
    }
}

function editItem($title, $itemType, $fields)
{
    $data = array(
        "type" => $itemType === 'film' ? "EditMovie" : "EditShow",
        "title" => $itemType === 'film' ? $title : null,
        "name" => $itemType === 'show' ? $title : null,
        "fields" => $fields
    );

    $response = makeApiRequest($data);

    if ($response['status'] === 'success') {
        echo '<script>alert("Successfully edited ' . $itemType . ': ' . $title . '");</script>';
    } else {
        echo '<script>alert("Failed to edit ' . $itemType . ': ' . $response['data'] . '");</script>';
    }
}

function deleteUserByEmail($email)
{
    $data = array(
        "type" => "DeleteUser",
        "email" => $email
    );

    $response = makeApiRequest($data);

    if ($response['status'] === 'success') {
        echo '<script>alert("Successfully deleted user with email: ' . $email . '");</script>';
    } else {
        echo '<script>alert("Failed to delete user: ' . $response['data'] . '");</script>';
    }
}

function addMovie($title, $genreID, $ratingID, $country, $description, $runtime, $year, $postURL, $videoURL, $screenURL)
{
    $data = array(
        "type" => "AddMovies",
        "title" => $title,
        "genreID" => $genreID,
        "ratingID" => $ratingID,
        "country" => $country,
        "description" => $description,
        "runtime" => $runtime,
        "year" => $year,
        "PostURL" => $postURL,
        "VideoURL" => $videoURL,
        "ScreenURL" => $screenURL
    );

    $response = makeApiRequest($data);

    if ($response['status'] === 'success') {
        echo '<script>alert("Successfully added movie: ' . $title . '");</script>';
    } else {
        echo '<script>alert("Failed to add movie: ' . $response['data'] . '");</script>';
    }
}


function addSeries($title, $genreID, $ratingID, $country, $description, $runtime, $year, $seasons, $postURL, $videoURL, $screenURL)
{
    $data = array(
        "type" => "AddSeries",
        "title" => $title,
        "genreID" => $genreID,
        "ratingID" => $ratingID,
        "country" => $country,
        "description" => $description,
        "runtime" => $runtime,
        "year" => $year,
        "seasons" => $seasons,
        "PostURL" => $postURL,
        "VideoURL" => $videoURL,
        "ScreenURL" => $screenURL
    );

    $response = makeApiRequest($data);

    if ($response['status'] === 'success') {
        echo '<script>alert("Successfully added series: ' . $title . '");</script>';
    } else {
        echo '<script>alert("Failed to add series: ' . $response['data'] . '");</script>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deleteTitle']) && isset($_POST['deleteType'])) {
        $title = $_POST['deleteTitle'];
        $itemType = $_POST['deleteType'];
        deleteItem($title, $itemType);
    } elseif (isset($_POST['editTitle']) && isset($_POST['editType']) && isset($_POST['editField']) && isset($_POST['editValue'])) {
        $title = $_POST['editTitle'];
        $itemType = $_POST['editType'];
        $field = $_POST['editField'];
        $value = $_POST['editValue'];
        $fields = array($field => $value);
        editItem($title, $itemType, $fields);
    } elseif (isset($_POST['deleteEmail'])) {
        $email = $_POST['deleteEmail'];
        deleteUserByEmail($email);
    } elseif (
        isset($_POST['addTitle']) &&
        isset($_POST['addGenreID']) &&
        isset($_POST['addRating']) &&
        isset($_POST['addCountry']) &&
        isset($_POST['addDescription']) &&
        isset($_POST['addRuntime']) &&
        isset($_POST['addYear']) &&
        isset($_POST['addPostUrl']) &&
        isset($_POST['addVideoUrl']) &&
        isset($_POST['addScreenUrl'])
    ) {
        $title = $_POST['addTitle'];
        $genreID = $_POST['addGenreID'];
        $ratingID = $_POST['addRating'];
        $country = $_POST['addCountry'];
        $description = $_POST['addDescription'];
        $runtime = $_POST['addRuntime'];
        $year = $_POST['addYear'];
        $postURL = $_POST['addPostUrl'];
        $videoURL = $_POST['addVideoUrl'];
        $screenURL = $_POST['addScreenUrl'];

        addMovie($title, $genreID, $ratingID, $country, $description, $runtime, $year, $postURL, $videoURL, $screenURL);
    } elseif (
        isset($_POST['addTitle']) &&
        isset($_POST['addGenreID']) &&
        isset($_POST['addRating']) &&
        isset($_POST['addCountry']) &&
        isset($_POST['addDescription']) &&
        isset($_POST['addRuntime']) &&
        isset($_POST['addYear']) &&
        isset($_POST['addSeasons']) &&
        isset($_POST['addPostUrl']) &&
        isset($_POST['addVideoUrl']) &&
        isset($_POST['addScreenUrl'])
    ) {
        $title = $_POST['addTitle'];
        $genreID = $_POST['addGenreID'];
        $ratingID = $_POST['addRating'];
        $country = $_POST['addCountry'];
        $description = $_POST['addDescription'];
        $runtime = $_POST['addRuntime'];
        $year = $_POST['addYear'];
        $seasons = $_POST['addSeasons'];
        $postURL = $_POST['addPostUrl'];
        $videoURL = $_POST['addVideoUrl'];
        $screenURL = $_POST['addScreenUrl'];

        addSeries($title, $genreID, $ratingID, $country, $description, $runtime, $year, $seasons, $postURL, $videoURL, $screenURL);
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="text" id="deleteTitle" name="deleteTitle" placeholder="Title" required>
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                    <!-- this is the add to movie block -->
                    <div class="add-movie">
                        <h2>Add Movie</h2>
                        <p>This box will add a movie or series to the database.</p>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="text" id="addTitle" name="addTitle" placeholder="Title" required>
                            <input type="text" id="addGenreID" name="addGenreID" placeholder="Genre ID" required>
                            <input type="text" id="addCountry" name="addCountry" placeholder="Country" required>
                            <input type="text" id="addRating" name="addRating" placeholder="Rating" required>
                            <textarea id="addDescription" name="addDescription" placeholder="Description" required></textarea>
                            <input type="number" id="addRuntime" name="addRuntime" placeholder="Runtime" min="0" max="1000" required>
                            <input type="number" id="addYear" name="addYear" placeholder="Year" required>
                            <input type="url" id="addPostUrl" name="addPostUrl" placeholder="Post URL" required>
                            <input type="url" id="addVideoUrl" name="addVideoUrl" placeholder="Video URL" required>
                            <input type="url" id="addScreenUrl" name="addScreenUrl" placeholder="Screen URL" required>
                            <button type="submit">Add</button>
                        </form>
                    </div>
                    <!-- this is the add to series block -->
                    <div class="add-series">
                        <h2>Add Series</h2>
                        <p>This box will add a movie or series to the database.</p>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="text" id="addTitle" name="addTitle" placeholder="Title" required>
                            <input type="text" id="addGenreID" name="addGenreID" placeholder="Genre ID" required>
                            <input type="text" id="addCountry" name="addCountry" placeholder="Country" required>
                            <input type="text" id="addRating" name="addRating" placeholder="Rating" required>
                            <textarea id="addDescription" name="addDescription" placeholder="Description" required></textarea>
                            <input type="number" id="addRuntime" name="addRuntime" placeholder="Runtime" min="0" max="1000" required>
                            <input type="number" id="addYear" name="addYear" placeholder="Year" required>
                            <input type="number" id="addSeasons" name="addSeasons" placeholder="Seasons" required>
                            <input type="url" id="addPostUrl" name="addPostUrl" placeholder="Post URL" required>
                            <input type="url" id="addVideoUrl" name="addVideoUrl" placeholder="Video URL" required>
                            <input type="url" id="addScreenUrl" name="addScreenUrl" placeholder="Screen URL" required>
                            <button type="submit">Add</button>
                        </form>
                    </div>
                    <!-- this is the edit to movie/series block -->
                    <div class="edit-movie-series">
                        <h2>Edit Movie or Series</h2>
                        <p>This box will edit a movie or series from the database.</p>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="text" id="editTitle" name="editTitle" placeholder="Title" required>
                            <!-- dropdown for what we editing here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Movie/Series
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="editMovie" name="editType" value="film" checked>
                                        <label for="editMovie">Movie</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="editSeries" name="editType" value="show">
                                        <label for="editSeries">Series</label>
                                    </li>
                                </ul>
                            </div>
                            <!-- dropdown for what part we editing here -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit Here
                                </button>
                                <ul class="dropdown-menu">
                                    <li><input type="radio" id="editTitle" name="editField" value="Title"><label for="editTitle">Title</label></li>
                                    <li><input type="radio" id="editDescription" name="editField" value="Description"><label for="editDescription">Description</label></li>
                                    <li><input type="radio" id="editPoster" name="editField" value="PosterURL"><label for="editPoster">Poster</label></li>
                                    <li><input type="radio" id="editRating" name="editField" value="RatingID"><label for="editRating">Rating</label></li>
                                    <li><input type="radio" id="editGenre" name="editField" value="Genre_ID"><label for="editGenre">Genre</label></li>
                                    <li><input type="radio" id="editReleaseYear" name="editField" value="Release_Year"><label for="editReleaseYear">Release Year</label></li>
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
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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