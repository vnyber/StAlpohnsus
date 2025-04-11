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
$parent_ID = $parent_title = $parent_first_name = $parent_last_name = $parent_address = $parent_email = $parent_phone = "";

// Initialize search query
$search_query = ""; 

// Fetch parent details if editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM parent_guardian WHERE parent_ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $parent_ID = $row['parent_ID'];
        $parent_title = $row['parent_title'];
        $parent_first_name = $row['parent_first_name'];
        $parent_last_name = $row['parent_last_name'];
        $parent_address = $row['parent_address'];
        $parent_email = $row['parent_email'];
        $parent_phone = $row['parent_phone'];
    } else {
        echo "<script>alert('Invalid parent ID.'); window.location.href='parent.php';</script>";
        exit;
    }
    $stmt->close();
}

// Handle parent Record Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_parent"])) {
    $parent_ID = intval($_POST['parent_ID']);
    $parent_title = $_POST['parent_title'];
    $parent_first_name = $_POST['parent_first_name'];
    $parent_last_name = $_POST['parent_last_name'];
    $parent_address = $_POST['parent_address'];
    $parent_email = $_POST['parent_email'];
    $parent_phone = $_POST['parent_phone'];
    
    $stmt = $conn->prepare("UPDATE parent_guardian SET parent_title=?, parent_first_name=?, parent_last_name=?, parent_address=?, parent_email=?, parent_phone=? WHERE parent_ID=?");
    $stmt->bind_param("ssssssi", $parent_title, $parent_first_name, $parent_last_name, $parent_address, $parent_email, $parent_phone, $parent_ID);

    if ($stmt->execute()) {
        echo "<script>alert('parent record updated successfully!'); window.location.href='parent.php';</script>";
    } else {
        echo "<script>alert('Error updating parent record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
// Handle Add New Parent Record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_parent"])) {
    $parent_title = $_POST['parent_title'];
    $parent_first_name = $_POST['parent_first_name'];
    $parent_last_name = $_POST['parent_last_name'];
    $parent_address = $_POST['parent_address'];
    $parent_email = $_POST['parent_email'];
    $parent_phone = $_POST['parent_phone'];

    $stmt = $conn->prepare("INSERT INTO parent_guardian (parent_title, parent_first_name, parent_last_name, parent_address, parent_email, parent_phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $parent_title, $parent_first_name, $parent_last_name, $parent_address, $parent_email, $parent_phone);

    if ($stmt->execute()) {
        echo "<script>alert('Parent record added successfully!'); window.location.href='parent.php';</script>";
    } else {
        echo "<script>alert('Error adding parent record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
// Clear form after submission if not editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['edit'])) {
  $parent_ID = $parent_title = $parent_first_name = $parent_last_name = $parent_address = $parent_email = $parent_phone = "";

}
// Handle Delete Parent Record
if (isset($_GET['delete'])) {
    $parent_ID = intval($_GET['delete']);
    if ($parent_ID > 0) {
        $stmt = $conn->prepare("DELETE FROM parent_guardian WHERE parent_ID = ?");
        $stmt->bind_param("i", $parent_ID);

        if ($stmt->execute()) {
            echo "<script>alert('Parent record deleted successfully!'); window.location.href='parent.php';</script>";
        } else {
            echo "<script>alert('Error deleting parent record: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Fetch all parent records
$sql = "SELECT * FROM parent_guardian ORDER BY parent_ID DESC";
$result = $conn->query($sql);

// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM parent_guardian WHERE parent_first_name LIKE '%$search_query%' OR parent_last_name LIKE '%$search_query%' ORDER BY parent_ID DESC";
} else {
    $sql = "SELECT * FROM parent_guardian ORDER BY parent_ID DESC";
}
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
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
  <a href="pupil.php">Pupils</a>
  <a href="pupil_parent.php">Pupil Parent</a>
  <a href="teacher.php">Teachers</a>
</nav>

<h2>Parents Information Page</h2>

<!-- Parents Form -->
<div class="form-section" id="parents-section">
<h2><?php echo ($parent_ID) ? "Edit Parent" : "Add Parent"; ?></h2>
  <form method="post" action="parent.php">
    <input type="hidden" name="parent_ID" value="<?php echo $parent_ID; ?>">

    <div class="form-group">
      <label for="parent-title">Title</label>
      <select id="parent-title" name="parent_title" required>
        <option value="">-- Select Title --</option>
        <option value="Mr." <?php echo ($parent_title == 'Mr.') ? 'selected' : ''; ?>>Mr.</option>
        <option value="Mrs." <?php echo ($parent_title == 'Mrs.') ? 'selected' : ''; ?>>Mrs.</option>
        <option value="Miss" <?php echo ($parent_title == 'Miss') ? 'selected' : ''; ?>>Miss</option>
        <option value="Ms." <?php echo ($parent_title == 'Ms.') ? 'selected' : ''; ?>>Ms.</option>
        <option value="Dr." <?php echo ($parent_title == 'Dr.') ? 'selected' : ''; ?>>Dr.</option>
        <option value="Prof." <?php echo ($parent_title == 'Prof.') ? 'selected' : ''; ?>>Prof.</option>

      </select>
    </div>

    <div class="form-group">
      <label for="parent-first-name">First Name</label>
      <input type="text" id="parent-first-name" name="parent_first_name" value="<?php echo $parent_first_name; ?>" required>
    </div>

    <div class="form-group">
      <label for="parent-last-name">Last Name</label>
      <input type="text" id="parent-last-name" name="parent_last_name" value="<?php echo $parent_last_name; ?>" required>
    </div>

    <div class="form-group">
      <label for="parent-address">Address</label>
      <textarea id="parent-address" name="parent_address"><?php echo $parent_address; ?></textarea>
    </div>

    <div class="form-group">
      <label for="parent-email">Email</label>
      <input type="email" id="parent-email" name="parent_email" value="<?php echo $parent_email; ?>" required>
    </div>

    <div class="form-group">
      <label for="parent-phone">Phone</label>
      <input type="text" id="parent-phone" name="parent_phone" value="<?php echo $parent_phone; ?>" required>
    </div>

    <?php if (!empty($parent_ID)) : ?>
        <input type="submit" name="update_parent" value="Update Parent">
    <?php else : ?>
        <input type="submit" name="add_parent" value="Add Parent">
    <?php endif; ?>
  </form>
</div>

<!-- Search Form -->
<form method="get" action="parent.php" id="searchForm" class="search-box">
  <label for="search">Search Parent Name:</label>
  <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter first or last name">
  <input type="submit" value="Search">
</form>

<!-- Display Parent Records -->
<h3>Parent/Guardians Records</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["parent_ID"]."</td>
                    <td>".$row["parent_title"]."</td>
                    <td>".$row["parent_first_name"]."</td>
                    <td>".$row["parent_last_name"]."</td>
                    <td>".$row["parent_address"]."</td>
                    <td>".$row["parent_phone"]."</td>
                    <td>".$row["parent_email"]."</td>
                    <td>
                        <a href='parent.php?edit=".$row["parent_ID"]."'>Edit</a> |
                        <a href='parent.php?delete=".$row["parent_ID"]."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No parents found</td></tr>";
    }     
    ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
