<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "energy_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['user_id'], $_GET['year'], $_GET['month'], $_GET['day'], $_GET['energy'])) {
    $user_id = $_GET['user_id'];
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];
    $energy = $_GET['energy'];

    $stmt = $conn->prepare("INSERT INTO energy_data (user_id, year, month, day, energy) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiid", $user_id, $year, $month, $day, $energy);
    $stmt->execute();

    echo "Data saved successfully";
} else {
    echo "Missing parameters";
}

$conn->close();
?>
