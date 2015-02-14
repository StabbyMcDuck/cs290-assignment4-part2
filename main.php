<!--
Regina Imhoff
CS 290
-->
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<?php
$category = "";
$name = "";
$length = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs_valid = true;

    $category = $_POST["category"];
    $name = $_POST["name"];

    $length = filter_input(INPUT_POST, 'length', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

    if ($length === false) {
        if (!(empty($_POST['length']))) {
            $inputs_valid = false;
        ?>
        <p class="error">Length must be at minimum 1 minute.</p>
<?php
        } else {
            $length = null;
        }
    }
}
?>

<form action="main.php" method="POST">
    <label>
        Video name:<input type="text" name="name" required value="<?php echo $name ?>">
    </label>
    <br>
    <label>
        Category:<input type="text" name="category">
    </label>
    <br>
    <label>
        <!--direction say to use type text, could also use a numerical input type-->
        Length:<input type="number" min="1" name="length" placeholder="minutes">
    </label>
    <br>
    <input type = "submit" value="Add video">
    <br>
</form>

<?php
/*
 * this is taken from http://www.w3schools.com/php/php_mysql_select.asp
 */

include 'configuration.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST' AND $inputs_valid === true) {
    $rented = false; // not rented out

    if (!($stmt = $conn->prepare("INSERT INTO videos(category, name, length, rented) VALUES(?,?,?,?) "))) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }

    if (!$stmt->bind_param('ssii', $category, $name, $length, $rented)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if (!$stmt->execute()) {
        if ($stmt->errno == 1062) {
            ?>
            <p class="error"><?php echo $name ?> already exists.</p>
<?php
        } else {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }
    $stmt->close();
}

$select_sql = "SELECT id, name, category, length, rented FROM videos";
$filter_category = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['category'])) {
        $filter_category = $_GET['category'];

        // empty category means All movies and therefore no filter
        if (!empty($filter_category)) {
            $select_sql .= " WHERE category = ?";
        }
    }
}

include 'filter.php'

?>
<table>
    <thead>
        <tr>
            <th>
                name
            </th>
            <th>
                category
            </th>
            <th>
                length
            </th>
            <th>
                rented
            </th>
        </tr>
    </thead>
<?php

if (!($select_statement = $conn->prepare($select_sql))) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}

if (!empty($filter_category)) {
    if (!$select_statement->bind_param('s', $filter_category)) {
        echo "Binding parameters failed: (" . $select_statement->errno . ") " . $select_statement->error;
    }
}

if (!$select_statement->execute()) {
    echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
}

$out_id = NULL;
$out_name = NULL;
$out_category = NULL;
$out_length = NULL;
$out_rented = NULL;


if (!$select_statement->bind_result($out_id, $out_name, $out_category, $out_length, $out_rented)) {
    echo "Binding output parameters failed: (" . $select_statement->errno . ") " . $select_statement->error;
}


while ($select_statement->fetch()) {
    //printf("id = %s (%s), label = %s (%s)\n", $out_id, gettype($out_id), $out_label, gettype($out_label));
    ?>
    <tr>
        <td>
            <?php
                echo $out_name;
            ?>
        </td>
        <td>
            <?php
                echo $out_category;
            ?>
        </td>
        <td>
            <?php
                echo $out_length
            ?>
        </td>
        <td>
            <?php
                if($out_rented === 1){
                    echo "checked out";
                }else{
                    echo "available";
                }
            ?>
        </td>
        <td>
            <form action="rented.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $out_id ?>">
                <button type="submit" value="toggle_rented">
                    <?php
                      if ($out_rented === 1) {
                          echo "Check In";
                      } else {
                          echo "Check Out";
                      }
                    ?>
                </button>
            </form>
        </td>
        <td>
            <form action = "delete.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $out_id ?>">
                <button type="submit" value="remove">Delete</button>
            </form>
        </td>
    </tr>
    <?php
}
?>
    <tfoot>
        <th colspan = "5">
        </th>
        <th>
            <form action = "delete_all.php" method = "POST">
                <button type="submit" value="remove">Delete All</button>
            </form>
        </th>
    </tfoot>
</table>    

</body>
</html>