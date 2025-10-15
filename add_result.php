<?php
include 'db.php';

// Define fixed courses
$courses = [
    'CYB321 - Introduction to Cyber',
    'CYB322 - Introduction to Net Conf',
    'CSC325 - Algorithm',
    'CSC321 - Web Design'
];

$error = "";
$success = "";
$student_found = false;
$student_id = 0;
$student_name = '';
$student_dept = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reg_no']) && !isset($_POST['scores'])) {
        // Step 1: Find student by reg_no
        $reg_no = trim($_POST['reg_no']);
        if (empty($reg_no)) {
            $error = "Matric number required.";
        } else {
            $stmt = $conn->prepare("SELECT id, name, department FROM students WHERE reg_no = ?");
            $stmt->bind_param("s", $reg_no);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($student = $result->fetch_assoc()) {
                $student_found = true;
                $student_id = $student['id'];
                $student_name = $student['name'];
                $student_dept = $student['department'];
            } else {
                $error = "Student not found.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['scores'])) {
        // Step 2: Process scores
        $student_id = (int)$_POST['student_id'];
        $all_valid = true;
        foreach ($courses as $course) {
            $score_key = str_replace(' ', '_', $course);  // For input name
            $score = isset($_POST['scores'][$score_key]) ? (int)$_POST['scores'][$score_key] : -1;
            if ($score < 0 || $score > 100) {
                $all_valid = false;
                $error = "Scores must be between 0 and 100.";
                break;
            }
            if ($score >= 0) {  // Only insert if score provided
                $grade = computeGrade($score);
                $stmt = $conn->prepare("INSERT IGNORE INTO results (student_id, subject, score, grade) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isis", $student_id, $course, $score, $grade);
                $stmt->execute();
                $stmt->close();
            }
        }
        if ($all_valid) {
            $success = "Results added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Result</title>
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
                <h2 class="card-title mb-4">Add Student Results</h2>
                <?php if (!$student_found): ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Enter Matric Number</label>
                            <input type="text" name="reg_no" class="form-control" placeholder="e.g., ABC/123" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Find Student</button>
                    </form>
                <?php else: ?>
                    <h5>Student: <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($student_dept); ?>)</h5>
                    <form method="post">
                        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                        <?php foreach ($courses as $course): ?>
                            <?php $score_key = str_replace(' ', '_', $course); ?>
                            <div class="mb-3">
                                <label class="form-label"><?php echo htmlspecialchars($course); ?></label>
                                <input type="number" name="scores[<?php echo $score_key; ?>]" class="form-control" min="0" max="100" placeholder="Enter score (0-100)" required>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Add Results</button>
                    </form>
                <?php endif; ?>
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