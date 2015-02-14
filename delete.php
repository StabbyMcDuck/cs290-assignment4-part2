<?php

include 'configuration.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id =$_POST['id'];

    if (!($stmt = $conn->prepare("DELETE FROM videos WHERE id = ?"))) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }

    if (!$stmt->bind_param('i', $id)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->close();
}

header('LOCATION: main.php');



?>