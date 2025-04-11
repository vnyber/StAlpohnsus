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

// Initialize variables for editing
$pupil_ID = $pupil_first_name = $pupil_last_name = $pupil_address = $pupil_medical_info = $class_ID = "";

// Fetch pupil details if editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM pupil WHERE pupil_ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $pupil_ID = $row['pupil_ID'];
        $pupil_first_name = $row['pupil_first_name'];
        $pupil_last_name = $row['pupil_last_name'];
        $pupil_address = $row['pupil_address'];
        $pupil_medical_info = $row['pupil_medical_info'];
        $class_ID = $row['class_ID'];
    }
    $stmt->close();
}

// Handle Add New Pupil Record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_pupil"])) {
  $pupil_first_name = $_POST['pupil_first_name'];
  $pupil_last_name = $_POST['pupil_last_name'];
  $pupil_address = $_POST['pupil_address'];
  $pupil_medical_info = $_POST['pupil_medical_info'];
  $class_ID = $_POST['class_ID'];

  $stmt = $conn->prepare("INSERT INTO pupil (pupil_first_name, pupil_last_name, pupil_address, pupil_medical_info, class_ID) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssi", $pupil_first_name, $pupil_last_name, $pupil_address, $pupil_medical_info, $class_ID);

  if ($stmt->execute()) {
      echo "<script>alert('New pupil added successfully!'); window.location.href='pupil.php';</script>";
  } else {
      echo "<script>alert('Error adding pupil: " . $stmt->error . "');</script>";
  }
  $stmt->close();
}

// Handle Pupil Record Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_pupil"])) {
    $pupil_ID = intval($_POST['pupil_ID']);
    $pupil_first_name = $_POST['pupil_first_name'];
    $pupil_last_name = $_POST['pupil_last_name'];
    $pupil_address = $_POST['pupil_address'];
    $pupil_medical_info = $_POST['pupil_medical_info'];
    $class_ID = $_POST['class_ID'];

    $stmt = $conn->prepare("UPDATE pupil SET pupil_first_name=?, pupil_last_name=?, pupil_address=?, pupil_medical_info=?, class_ID=? WHERE pupil_ID=?");
    $stmt->bind_param("ssssii", $pupil_first_name, $pupil_last_name, $pupil_address, $pupil_medical_info, $class_ID, $pupil_ID);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='pupil.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle Delete Pupil Record
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_pupil'])) {
  $pupil_ID = intval($_POST['pupil_ID']);

        if ($stmt->execute()) {
            echo "<script>alert('Record deleted successfully!'); window.location.href='pupil.php';</script>";
        } else {
            echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

// Search Pupil Record
$search_query = "";
if (isset($_GET["search"])) {
    $search_query = $_GET["search"];
}

// Fetch pupils with search filter
$sql = "SELECT * FROM pupil WHERE pupil_first_name LIKE ? OR pupil_last_name LIKE ? ORDER BY pupil_ID DESC";
$search_param = "%" . $search_query . "%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Fetch class records
$sql_classes = "SELECT * FROM classes ORDER BY class_ID ASC";
$class_result = $conn->query($sql_classes);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Pupils Information Page</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .form-section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
    .form-section h2 { margin-top: 0; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input[type="text"], input[type="number"], textarea, select {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
    }
    input[type="submit"] { padding: 10px 20px; }
    nav a { margin-right: 10px; }
    .search-box { border: 2px solid #007bff; padding: 10px; border-radius: 8px; width: fit-content; margin-bottom: 20px; }
  </style>
</head>
<body>
<h1>St Alphonsus Primary School Digital Records System</h1>

<!-- Navigation -->
<nav>
  <a href="index.php">Home</a>
  <a href="classes.php">Classes</a>
  <a href="parent.php">Parents/Guardians</a>
  <a href="pupil_parent.php">Pupil Parent</a>
  <a href="teacher.php">Teachers</a>
</nav>

<h2>Pupils Information Page</h2>

<!-- Add/Edit Pupil Form -->
<div class="form-section" id="pupil-section">
<h2><?php echo ($pupil_ID) ? "Edit Pupil" : "Add Pupil"; ?></h2>
<form method="post" action="pupil.php">
  <input type="hidden" name="pupil_ID" value="<?php echo $pupil_ID; ?>">  

  <div class="form-group">
    <label for="pupil-first-name">Pupil First Name</label>
    <input type="text" id="pupil-first-name" name="pupil_first_name" value="<?php echo htmlspecialchars($pupil_first_name); ?>" required>
  </div>

  <div class="form-group">
    <label for="pupil-last-name">Pupil Last Name</label>
    <input type="text" id="pupil-last-name" name="pupil_last_name" value="<?php echo htmlspecialchars($pupil_last_name); ?>" required>
  </div>

  <div class="form-group">
    <label for="pupil-address">Pupil Address</label>
    <textarea id="pupil-address" name="pupil_address" required><?php echo htmlspecialchars($pupil_address); ?></textarea>
  </div>

  <div class="form-group">
    <label for="pupil-medical-info">Medical Information</label>
    <textarea id="pupil-medical-info" name="pupil_medical_info" required><?php echo htmlspecialchars($pupil_medical_info); ?></textarea>
  </div>

  <div class="form-group">
    <label for="class-ID">Class ID</label>
    <select id="class-ID" name="class_ID" required>
      <option value="">Select a Class</option>
      <?php
      while ($class = $class_result->fetch_assoc()) {
        $selected = ($class['class_ID'] == $class_ID) ? "selected" : "";
        echo "<option value='{$class['class_ID']}' $selected>{$class['class_ID']} - {$class['class_name']}</option>";
      }
      ?>
    </select>
  </div>

  <?php if (!empty($pupil_ID)) : ?>
    <input type="submit" name="update_pupil" value="Update Pupil">
  <?php else : ?>
    <input type="submit" name="add_pupil" value="Add Pupil">
  <?php endif; ?>
</form>
</div>

<!-- Search Form -->
<form method="get" action="pupil.php" id="searchForm" class="search-box">
  <label for="search">Search Pupil Name:</label>
  <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter first or last name">
  <input type="submit" value="Search">
</form>

<!-- Display Pupil Records Table -->
<h3>Pupil Records</h3>
<table border="1">
  <tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Address</th>
    <th>Medical Info</th>
    <th>Class ID</th>
    <th>Actions</th>
  </tr>

  <?php
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      echo "<tr>
              <td>".$row["pupil_ID"]."</td>
              <td>".$row["pupil_first_name"]."</td>
              <td>".$row["pupil_last_name"]."</td>
              <td>".$row["pupil_address"]."</td>
              <td>".$row["pupil_medical_info"]."</td>
              <td>".$row["class_ID"]."</td>
              <td>
                <a href='pupil.php?edit=".$row["pupil_ID"]."'>Edit</a> |
                <a href='pupil.php?delete=".$row["pupil_ID"]."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
              </td>
            </tr>";
    }
  } else {
    echo "<tr><td colspan='7'>No pupil found</td></tr>";
  }
  ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
