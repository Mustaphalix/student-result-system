<?php
include 'db.php';

$results = [];
$error = "";
$student_name = '';
$student_dept = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = trim($_POST['reg_no']);

    // Server-side validation
    if (empty($reg_no)) {
        $error = "Matric number is required.";
    } else {
        // Prepared statement for security
        $stmt = $conn->prepare("SELECT id, name, department FROM students WHERE reg_no = ?");
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $student_result = $stmt->get_result();
        if ($student = $student_result->fetch_assoc()) {
            $student_id = $student['id'];
            $student_name = $student['name'];
            $student_dept = $student['department'];
            $stmt->close();

            $stmt = $conn->prepare("SELECT subject, score, grade FROM results WHERE student_id = ? ORDER BY subject");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $results_result = $stmt->get_result();
            while ($row = $results_result->fetch_assoc()) {
                $results[] = $row;
            }
            if (empty($results)) {
                $error = "No results found for this student.";
            }
        } else {
            $error = "Student not found.";
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
    <title>View Results</title>
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
                <h2 class="card-title mb-4">View Your Results</h2>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Enter Matric Number</label>
                        <input type="text" name="reg_no" class="form-control" placeholder="e.g., ABC/123" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">View Results</button>
                </form>
                <?php if ($error): ?>
                    <div class="alert alert-warning mt-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (!empty($results)): ?>
                    <h3 class="mt-4">Results for <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($student_dept); ?>)</h3>
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Course</th>
                                <th>Score</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['subject']); ?></td>
                                    <td><?php echo $result['score']; ?></td>
                                    <td><?php echo $result['grade']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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