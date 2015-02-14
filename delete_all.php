<?php
/**
 * Created by PhpStorm.
 * User: reginaimhoff
 * Date: 2/13/15
 * Time: 7:16 PM
 */

include 'configuration.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {


// sql to delete a record
    $sql = "DELETE FROM videos";

    if ($conn->query($sql) !== TRUE) {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}

header('LOCATION: main.php');



?>