<?php
require_once('../includes/connection.inc.php');
// connect to MySQL
$conn = dbConnect('read');
// set default values
$col = 'image_id';
$dir = 'ASC';
// create arrays of permitted values
$columns = array('image_id', 'filename', 'caption');
$direction = array('ASC', 'DESC');
// if the form has been submitted, use only expected values
if (isset($_GET['column']) && in_array($_GET['column'], $columns)) {
  $col = $_GET['column'];
}
if (isset($_GET['direction']) && in_array($_GET['direction'], $direction)) {
  $dir = $_GET['direction'];
}
// prepare the SQL query
$sql = "SELECT * FROM images
        ORDER BY $col $dir";
// submit the query and capture the result
$result = $conn->query($sql) or die(mysqli_error());
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Connecting with MySQLi: Order by User Input</title>
</head>

<body>
<form id="form1" method="get" action="">
  <label for="column">Order by:</label>
  <select name="column" id="column">
    <option <?php if ($col == 'image_id') echo 'selected'; ?>>image_id</option>
    <option <?php if ($col == 'filename') echo 'selected'; ?>>filename</option>
    <option <?php if ($col == 'caption') echo 'selected'; ?>>caption</option>
  </select>
  <select name="direction" id="direction">
    <option value="ASC" <?php if ($dir == 'ASC') echo 'selected'; ?>>Ascending</option>
    <option value="DESC" <?php if ($dir == 'DESC') echo 'selected'; ?>>Descending</option>
  </select>
  <input type="submit" name="change" id="change" value="Change">
</form>
<table>
  <tr>
    <th>image_id</th>
    <th>filename</th>
    <th>caption</th>
  </tr>
<?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
    <td><?php echo $row['image_id']; ?></td>
    <td><?php echo $row['filename']; ?></td>
    <td><?php echo $row['caption']; ?></td>
  </tr>
<?php } ?>
</table>
</body>
</html>