<?php
/**
 * Created by PhpStorm.
 * User: reginaimhoff
 * Date: 2/14/15
 * Time: 11:09 AM
 */

include 'configuration.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id =$_POST['id'];

    // see http://blog.dephuedatadesign.com/mysql-toggle-boolean-tinyint-with-xor
    if (!($stmt = $conn->prepare("UPDATE videos SET rented = rented XOR 1 WHERE id = ?"))) {
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