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
$class_ID = $class_name = $class_capacity = $teacher_ID = "";

// Insert Class Data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_class"])) {
    $class_name = $_POST['class_name'];
    $class_capacity = $_POST['class_capacity'];
    $teacher_ID = $_POST['teacher_ID'];

    // Insert the new class (class_ID auto-incremented)
    $stmt = $conn->prepare("INSERT INTO classes (class_name, class_capacity, teacher_ID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $class_name, $class_capacity, $teacher_ID);

    if ($stmt->execute()) {
        echo "<script>alert('Class added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fetch class data if editing
if (isset($_GET['edit'])) {
  $edit_id = intval($_GET['edit']);
  $stmt = $conn->prepare("SELECT * FROM classes WHERE class_ID = ?");
  $stmt->bind_param("i", $edit_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $class_ID = $row['class_ID'];
      $class_name = $row['class_name'];
      $class_capacity = $row['class_capacity'];
      $teacher_ID = $row['teacher_ID'];
  }
  $stmt->close();
}

// Handle Class Record Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_class"])) {
  $class_ID = intval($_POST['class_ID']);
  $class_name = $_POST['class_name'];
  $class_capacity = $_POST['class_capacity'];
  $teacher_ID = $_POST['teacher_ID'];

  $stmt = $conn->prepare("UPDATE classes SET class_name=?, class_capacity=?, teacher_ID=? WHERE class_ID=?");
  $stmt->bind_param("siii", $class_name, $class_capacity, $teacher_ID, $class_ID);

  if ($stmt->execute()) {
      echo "<script>alert('Record updated successfully!');</script>";
  } else {
      echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}

// Handle Delete Class Record
if (isset($_GET['delete'])) {
    $class_ID = $_GET['delete'];

    if (!is_numeric($class_ID)) {
        echo "<script>alert('Invalid class ID!');</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM classes WHERE class_ID = ?");
        $stmt->bind_param("i", $class_ID);

        if ($stmt->execute()) {
            echo "<script>alert('Record deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

// Clear form after submission if not editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['edit'])) {
  $class_ID = $class_name = $class_capacity = $teacher_ID = "";
}

// Fetch all classes for display
$sql = "SELECT * FROM classes ORDER BY class_ID DESC";
$result = $conn->query($sql);

// Fetch all teachers for the dropdown
$teachers_sql = "SELECT teacher_ID, teacher_first_name, teacher_last_name FROM teacher";
$teachers_result = $conn->query($teachers_sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Classes Information Page</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .form-section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; }
    .form-section h2 { margin-top: 0; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input[type="text"], input[type="number"] {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
    }
    input[type="submit"] { padding: 10px 20px; }
    nav a { margin-right: 10px; }
  </style>
</head>
<body>
  <h1>St Alphonsus Primary School Digital Records System</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="pupil.php">Pupils</a>
    <a href="parent.php">Parents/Guardians</a>
    <a href="pupil_parent.php">Pupil Parent</a>
    <a href="teacher.php">Teachers</a>
  </nav>

  <h2>Classes Information Page</h2>

  <!-- Add/Edit Class Form -->
<div class="form-section" id="classes-section">
<h2><?php echo ($class_ID) ? "Edit Class" : "Add Class"; ?></h2>
  <form method="post" action="classes.php">
    <input type="hidden" name="class_ID" value="<?php echo $class_ID; ?>">  

    <div class="form-group">
      <label for="class-name">Class Name</label>
      <input type="text" id="class-name" name="class_name" value="<?php echo $class_name; ?>" required>
    </div>

    <div class="form-group">
      <label for="class_capacity">Class Capacity</label>
      <input type="text" id="class_capacity" name="class_capacity" value="<?php echo $class_capacity; ?>" required>
    </div>

    <div class="form-group">
  <label for="teacher-ID">Teacher</label>
  <select id="teacher-ID" name="teacher_ID" required>
    <option value="">Select a Teacher</option>
    <?php
    while ($teacher = $teachers_result->fetch_assoc()) {
        $selected = ($teacher['teacher_ID'] == $teacher_ID) ? "selected" : "";
        echo "<option value='{$teacher['teacher_ID']}' $selected>
                {$teacher['teacher_ID']} - {$teacher['teacher_first_name']} {$teacher['teacher_last_name']}
              </option>";
    }
    ?>
  </select>
</div>


    <?php if (!empty($class_ID)) : ?>
        <input type="submit" name="update_class" value="Update Class">
    <?php else : ?>
        <input type="submit" name="add_class" value="Add Class">
    <?php endif; ?>
  </form>
</div>

  <!-- Display Class Records -->
  <h3>Class Records</h3>
  <table border="1">
      <tr>
          <th>Class ID</th>
          <th>Class Name</th>
          <th>Class Capacity</th>
          <th>Teacher ID</th>
          <th>Actions</th>
      </tr>

      <?php
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>".$row["class_ID"]."</td>
                      <td>".$row["class_name"]."</td>
                      <td>".$row["class_capacity"]."</td>
                      <td>".$row["teacher_ID"]."</td>
                      <td>
                          <a href='?edit=".$row["class_ID"]."'>Edit</a> |
                          <a href='?delete=".$row["class_ID"]."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No class found</td></tr>";
      }
      ?>
  </table>



</body>
</html>

<?php
$conn->close();
?>
