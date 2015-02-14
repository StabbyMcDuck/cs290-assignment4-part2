<?php
/**
 * Created by PhpStorm.
 * User: reginaimhoff
 * Date: 2/14/15
 * Time: 11:25 AM
 */

?>
<form action="main.php" method="GET">
  <select name="category">
      <option value="">All Movies</option>
      <?php
// $conn is supplied from including file
// see http://stackoverflow.com/questions/8571902/mysql-select-only-unique-values-from-a-column
if (!($select_distinct_categories_statement = $conn->prepare("SELECT DISTINCT(category) FROM videos ORDER BY category ASC"))) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}

if (!$select_distinct_categories_statement->execute()) {
    echo "Execute failed: (" . $conn->errno . ") " . $conn->error;
}

$out_category = NULL;

if (!$select_distinct_categories_statement->bind_result($out_category)) {
    echo "Binding output parameters failed: (" . $select_distinct_categories_statement->errno . ") " . $select_distinct_categories_statement->error;
}

while ($select_distinct_categories_statement->fetch()) {
?>
    <option <?php
      if ($out_category == $filter_category) {
          echo "selected=\"selected\"";
      }
    ?> value="<?php echo $out_category ?>"><?php echo $out_category ?></option>
<?php
}
?>
  </select>
    <input type="submit" value="Filter to category">
</form>