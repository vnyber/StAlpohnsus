<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stalphonsusdatabase"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!-- Pupil Search Form -->
<head>
  <meta charset="UTF-8">
  <title>Parents Information Page</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .form-section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; }
    .form-section h2 { margin-top: 0; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
    input[type="submit"] { padding: 10px 20px; }
    nav a { margin-right: 10px; }
  </style>
</head>
<body>
<h1>St Alphonsus Primary School Digital Records System</h1>
<!-- Navigation -->
<nav>
  <a href="index.php">Home</a>
  <a href="classes.php">Classes</a>
  <a href="pupil.php">Pupil</a>
  <a href="parent.php">Parents/Guardians</a>
  <a href="teacher.php">Teachers</a>
</nav>
<h2>Pupil Search</h2>
<div class="form-section" id="pupil-section">
<h2>Search for Pupil</h2>
<!-- Search Form -->
<form method="GET" action="">
    <label for="search">Search Pupil by Last Name:</label>
    <input type="text" name="search" placeholder="Enter last name..." required>
    <button type="submit">Search</button>
</form>
</div>
<!-- Pupil Search Results -->
<div class="form-section" id="pupil-results">
<h2>Search Results</h2>


<?php
// Check if a search query is entered
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    // Search pupils by last name
    $sql_pupil = "SELECT pupil_ID, pupil_first_name, pupil_last_name FROM pupil WHERE pupil_last_name LIKE ?";
    $stmt_pupil = $conn->prepare($sql_pupil);
    $search_param = "%" . $search . "%";
    $stmt_pupil->bind_param("s", $search_param);
    $stmt_pupil->execute();
    $result_pupil = $stmt_pupil->get_result();

    if ($result_pupil->num_rows > 0) {
        echo "<h3>Pupils Found:</h3>";

        while ($pupil = $result_pupil->fetch_assoc()) {
            echo "Pupil Name: " . $pupil['pupil_first_name'] . " " . $pupil['pupil_last_name'] . "<br>";
        }

        // Fetch parents with the same last name
        $sql_parent = "SELECT parent_first_name, parent_last_name, parent_phone, parent_email, parent_address FROM parent_guardian WHERE parent_last_name LIKE ?";
        $stmt_parent = $conn->prepare($sql_parent);
        $stmt_parent->bind_param("s", $search_param);
        $stmt_parent->execute();
        $result_parent = $stmt_parent->get_result();

        echo "<h3>Parent(s) Found:</h3>";

        if ($result_parent->num_rows > 0) {
            while ($parent = $result_parent->fetch_assoc()) {
                echo "Parent Name: " . $parent['parent_first_name'] . " " . $parent['parent_last_name'] . "<br>";
                echo "Phone: " . $parent['parent_phone'] . "<br>";
                echo "Email: " . $parent['parent_email'] . "<br>";
                echo "Address: " . $parent['parent_address'] . "<br><br>";
            }
        } else {
            echo "No parent found with that last name.";
        }
    } else {
        echo "No pupils found with that last name.";
    }
}

// Close connection
$conn->close();
?>
