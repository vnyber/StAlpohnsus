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
$teacher_ID = $teacher_title = $teacher_first_name = $teacher_last_name = $teacher_address = $teacher_phone = $teacher_email = $teacher_annual_salary = $teacher_background_check = $teacher_hire_date = $teacher_termination_date = "";

// Fetch teacher details if editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM teacher WHERE teacher_ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $teacher_ID = $row['teacher_ID'];
        $teacher_title = $row['teacher_title'];
        $teacher_first_name = $row['teacher_first_name'];
        $teacher_last_name = $row['teacher_last_name'];
        $teacher_address = $row['teacher_address'];
        $teacher_phone = $row['teacher_phone'];
        $teacher_email = $row['teacher_email'];
        $teacher_annual_salary = $row['teacher_annual_salary'];
        $teacher_background_check = $row['teacher_background_check'];
        $teacher_hire_date = $row['teacher_hire_date'];
        $teacher_termination_date = $row['teacher_termination_date'];
    }
    $stmt->close();
}

// Handle Teacher Record Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_teacher"])) {
    $teacher_ID = intval($_POST['teacher_ID']);
    $teacher_title = $_POST['teacher_title'];
    $teacher_first_name = $_POST['teacher_first_name'];
    $teacher_last_name = $_POST['teacher_last_name'];
    $teacher_address = $_POST['teacher_address'];
    $teacher_phone = $_POST['teacher_phone'];
    $teacher_email = $_POST['teacher_email'];
    $teacher_annual_salary = floatval($_POST['teacher_annual_salary']);
    $teacher_background_check = isset($_POST['teacher_background_check']) ? 1 : 0;
    $teacher_hire_date = $_POST['teacher_hire_date'];
    $teacher_termination_date = $_POST['teacher_termination_date'];

    $stmt = $conn->prepare("UPDATE teacher SET teacher_title=?, teacher_first_name=?, teacher_last_name=?, teacher_address=?, teacher_phone=?, teacher_email=?, teacher_annual_salary=?, teacher_background_check=?, teacher_hire_date=?, teacher_termination_date=? WHERE teacher_ID=?"); 
    $stmt->bind_param("ssssssssssi", $teacher_title, $teacher_first_name, $teacher_last_name, $teacher_address, $teacher_phone, $teacher_email, $teacher_annual_salary, $teacher_background_check, $teacher_hire_date, $teacher_termination_date, $teacher_ID); 

    if ($stmt->execute()) {
        echo "<script>alert('Teacher record updated successfully!'); window.location.href='teacher.php';</script>";
    } else {
        echo "<script>alert('Error updating teacher record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
// Handle Add New Teacher Record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_teacher"])) {
  $teacher_title = $_POST['teacher_title'];
  $teacher_first_name = $_POST['teacher_first_name'];
  $teacher_last_name = $_POST['teacher_last_name'];
  $teacher_address = $_POST['teacher_address'];
  $teacher_phone = $_POST['teacher_phone'];
  $teacher_email = $_POST['teacher_email'];
  $teacher_annual_salary = floatval($_POST['teacher_annual_salary']);
  $teacher_background_check = isset($_POST['teacher_background_check']) ? 1 : 0;
  $teacher_hire_date = $_POST['teacher_hire_date'];
  $teacher_termination_date = $_POST['teacher_termination_date'];

  $stmt = $conn->prepare("INSERT INTO teacher (teacher_title, teacher_first_name, teacher_last_name, teacher_address, teacher_phone, teacher_email, teacher_annual_salary, teacher_background_check, teacher_hire_date, teacher_termination_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssssssi", $teacher_title, $teacher_first_name, $teacher_last_name, $teacher_address, $teacher_phone, $teacher_email, $teacher_annual_salary, $teacher_background_check, $teacher_hire_date, $teacher_termination_date);

  if ($stmt->execute()) {
      echo "<script>alert('Teacher record added successfully!'); window.location.href='teacher.php';</script>";
  } else {
      echo "<script>alert('Error adding teacher record: " . $stmt->error . "');</script>";
  }
  $stmt->close();
}
// Clear form after submission if not editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['edit'])) {
  $teacher_ID = $teacher_title = $teacher_first_name = $teacher_last_name = $teacher_address = $teacher_phone = $teacher_annual_salary = $teacher_background_check = $teacher_hire_date = $teacher_termination_date = "";
}
// Handle Delete Teacher Record
if (isset($_GET['delete'])) {
    $teacher_ID = intval($_GET['delete']);
    if ($teacher_ID > 0) {
        $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_ID = ?");
        $stmt->bind_param("i", $teacher_ID);

        if ($stmt->execute()) {
            echo "<script>alert('Teacher record deleted successfully!'); window.location.href='teacher.php';</script>";
        } else {
            echo "<script>alert('Error deleting teacher record: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Search teacher records
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM teacher WHERE teacher_first_name LIKE '%$search_query%' OR teacher_last_name LIKE '%$search_query%' ORDER BY teacher_ID DESC";
} else {
    $sql = "SELECT * FROM teacher ORDER BY teacher_ID DESC";
}
$result = $conn->query($sql);

// Phone number validation
function validate_phone($phone) {
  return preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone);
}
// Salary validation
function validate_salary($salary) {
  return preg_match("/^\d+(\.\d{1,2})?$/", $salary);
}
// Date validation
function validate_date($date) {
  return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date);
}
// Email validation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["teacher_email"])) {
  $teacher_email = trim($_POST["teacher_email"]);
  
  if (empty($teacher_email)) {
      echo "<script>alert('Email cannot be empty.');</script>";
  } elseif (strlen($teacher_email) > 50) {
      echo "<script>alert('Email is too long. Maximum 50 characters allowed.');</script>";
  } elseif (strpos($teacher_email, "@") === false) {
      echo "<script>alert('Invalid email. An email must contain \"@\".');</script>";
  } elseif (!filter_var($teacher_email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email format.');</script>";
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teachers Information Page</title>
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
  <a href="parent.php">Parents/Guardians</a>
  <a href="pupil_parent.php">Pupil Parent</a>
</nav>

<h2>Teachers Information Page</h2>

<!-- Teachers Form -->
<div class="form-section" id="teachers-section">
<h2><?php echo ($teacher_ID) ? "Edit Teacher" : "Add Teacher"; ?></h2>
  <form method="post" action="teacher.php">
    <input type="hidden" name="teacher_ID" value="<?php echo $teacher_ID; ?>">

    <div class="form-group">
      <label for="teacher-title">Title</label>
      <select id="teacher-title" name="teacher_title" required>
        <option value="">-- Select Title --</option>
        <option value="Mr." <?php echo ($teacher_title == 'Mr.') ? 'selected' : ''; ?>>Mr.</option>
        <option value="Mrs." <?php echo ($teacher_title == 'Mrs.') ? 'selected' : ''; ?>>Mrs.</option>
        <option value="Miss" <?php echo ($teacher_title == 'Miss') ? 'selected' : ''; ?>>Miss</option>
        <option value="Ms." <?php echo ($teacher_title == 'Ms.') ? 'selected' : ''; ?>>Ms.</option>
        <option value="Dr." <?php echo ($teacher_title == 'Dr.') ? 'selected' : ''; ?>>Dr.</option>
        <option value="Prof." <?php echo ($teacher_title == 'Prof.') ? 'selected' : ''; ?>>Prof.</option>

      </select>
    </div>

    <div class="form-group">
      <label for="teacher-first-name">First Name</label>
      <input type="text" id="teacher-first-name" name="teacher_first_name" value="<?php echo $teacher_first_name; ?>" required>
    </div>

    <div class="form-group">
      <label for="teacher-last-name">Last Name</label>
      <input type="text" id="teacher-last-name" name="teacher_last_name" value="<?php echo $teacher_last_name; ?>" required>
    </div>

    <div class="form-group">
      <label for="teacher-address">Address</label>
      <textarea id="teacher-address" name="teacher_address"><?php echo $teacher_address; ?></textarea>
    </div>

    <div class="form-group">
      <label for="teacher-phone">Phone</label>
      <input type="text" id="teacher-phone" name="teacher_phone" value="<?php echo $teacher_phone; ?>" required>
    </div>

    <div class = "form-group">
      <label for="teacher-email">Email</label>
      <input type="email" id="teacher-email" name="teacher_email" value="<?php echo $teacher_email; ?>" required>
    </div>

    <div class="form-group">
      <label for="teacher-annual-salary">Annual Salary</label>
      <input type="number" id="teacher-annual-salary" name="teacher_annual_salary" value="<?php echo $teacher_annual_salary; ?>" step="0.01" required>
    </div>

    <div class="form-group">
      <label for="teacher-background-check">Background Check</label>
      <input type="checkbox" id="teacher-background-check" name="teacher_background_check" <?php echo ($teacher_background_check == 1) ? 'checked' : ''; ?>>
    </div>

    <div class="form-group">
      <label for="teacher-hire-date">Hire Date</label>
      <input type="date" id="teacher-hire-date" name="teacher_hire_date" value="<?php echo $teacher_hire_date; ?>" required>
    </div>

    <div class="form-group">
      <label for="teacher-termination-date">Termination Date</label>
      <input type="date" id="teacher-termination-date" name="teacher_termination_date" value="<?php echo $teacher_termination_date; ?>">
    </div>

    <?php if (!empty($teacher_ID)) : ?>
        <input type="submit" name="update_teacher" value="Update Teacher">
    <?php else : ?>
        <input type="submit" name="add_teacher" value="Add Teacher">
    <?php endif; ?>
  </form>
</div>

<!-- Search Form -->
<form method="get" action="teacher.php" id="searchForm" class="search-box">
  <label for="search">Search Teacher Name:</label>
  <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter first or last name">
  <input type="submit" value="Search">
</form>

<!-- Display Teacher Records -->
<h3>Teacher Records</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Address</th>
        <th>Annual Salary</th>
        <th>Background Check</th>
        <th>Hire Date</th>
        <th>Termination Date</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["teacher_ID"]."</td>
                    <td>".$row["teacher_title"]."</td>
                    <td>".$row["teacher_first_name"]."</td>
                    <td>".$row["teacher_last_name"]."</td>
                    <td>".$row["teacher_phone"]."</td>
                    <td>".$row["teacher_email"]."</td>
                    <td> ".$row["teacher_address"]."</td>
                    <td>".$row["teacher_annual_salary"]."</td>
                    <td>".($row["teacher_background_check"] ? "Yes" : "No")."</td>
                    <td>".$row["teacher_hire_date"]."</td>
                    <td>".$row["teacher_termination_date"]."</td>
                    <td>
                        <a href='teacher.php?edit=".$row["teacher_ID"]."'>Edit</a> |
                        <a href='teacher.php?delete=".$row["teacher_ID"]."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No teachers found</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
