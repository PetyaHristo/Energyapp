<?php
require 'db.php'; // свързване с базата

// Очакваме данни с GET (или POST)
if (isset($_GET['user_id']) && isset($_GET['energy']) && isset($_GET['month']) && isset($_GET['day'])) {
    $user_id = (int)$_GET['user_id'];
    $energy = (float)$_GET['energy'];
    $month = (int)$_GET['month'];
    $day = (int)$_GET['day'];

    $stmt = $pdo->prepare("REPLACE INTO energy_data (user_id, month, day, energy) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $month, $day, $energy]);

    echo "OK";
} else {
    echo "Missing parameters";
}
