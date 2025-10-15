<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_result_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Reusable function to compute grade based on score
function computeGrade($score) {
    if ($score >= 70) return 'A';
    elseif ($score >= 60) return 'B';
    elseif ($score >= 50) return 'C';
    elseif ($score >= 40) return 'D';
    else return 'F';
}
?>