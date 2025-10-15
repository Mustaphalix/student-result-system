<?php
include 'db.php';

$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $reg_no = trim($_POST['reg_no']);
    $department = trim($_POST['department']);

    // Server-side validation
    if (empty($name) || empty($reg_no) || empty($department)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[A-Za-z0-9\/]+$/', $reg_no)) {
        $error = "Invalid registration number format.";
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $department)) {
        $error = "Invalid department name (letters and spaces only).";
    } else {
        // Prepared statement for security
        $stmt = $conn->prepare("INSERT INTO students (name, reg_no, department) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $reg_no, $department);
        if ($stmt->execute()) {
            $success = "Student added successfully!";
        } else {
            $error = "Error adding student: " . $stmt->error;  // Handles duplicates, etc.
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Student Result System</h1>
    </header>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">Add New Student</h2>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Registration Number (Matric No)</label>
                        <input type="text" name="reg_no" class="form-control" placeholder="22L1CS000" required pattern="[A-Za-z0-9/]+" title="Alphanumeric or / only">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g., Computer Science" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Add Student</button>
                </form>
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3"><?php echo $success; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer class="bg-light text-center py-3 mt-5">
        <p>&copy; 2025 Student Result System</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>