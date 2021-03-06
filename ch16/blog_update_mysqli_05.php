<?php
include('../includes/connection.inc.php');
// initialize flags
$OK = false;
$done = false;
// create database connection
$conn = dbConnect('write');
// initialize statement
$stmt = $conn->stmt_init();

// get details of selected record
if (isset($_GET['article_id']) && !$_POST) {
  // prepare SQL query
  $sql = 'SELECT article_id, image_id, title, article
		  FROM blog WHERE article_id = ?';
  $stmt->prepare($sql);
  // bind the query parameter
  $stmt->bind_param('i', $_GET['article_id']);
  // bind the results to variables
  $stmt->bind_result($article_id, $image_id, $title, $article);
  // execute the query, and fetch the result
  $OK = $stmt->execute();
  $stmt->fetch();
  // free the database resource for the next query
  $stmt->free_result();
  // get categories associated with the article
  $sql = 'SELECT cat_id FROM article2cat
          WHERE article_id = ?';
  $stmt->prepare($sql);
  $stmt->bind_param('i', $_GET['article_id']);
  $stmt->bind_result($cat_id);
  $OK = $stmt->execute();
  // loop through the results to store them in an array
  $selected_categories = array();
  while ($stmt->fetch()) {
	$selected_categories[] = $cat_id;
  }
}
// if form has been submitted, update record
if (isset($_POST ['update'])) {
  // prepare update query
  if (!empty($_POST['image_id'])) {
	$sql = 'UPDATE blog SET image_id = ?, title = ?, article = ?
			WHERE article_id = ?';
	$stmt->prepare($sql);
	$stmt->bind_param('issi', $_POST['image_id'], $_POST['title'], $_POST['article'], $_POST['article_id']);
  } else {
	$sql = 'UPDATE blog SET image_id = NULL, title = ?, article = ?
			WHERE article_id = ?';
	$stmt->prepare($sql);
	$stmt->bind_param('ssi', $_POST['title'], $_POST['article'], $_POST['article_id']);	
  }
  $stmt->execute();
  $done = $stmt->affected_rows;
}
// redirect if $_GET['article_id'] not defined
if ($done || !isset($_GET['article_id'])) {
  header('Location: http://localhost/phpsols/admin/blog_list_mysqli.php');
  exit;
}
// store error message if query fails
if (isset($stmt) && !$OK && !$done) {
  $error = $stmt->error;
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Update Blog Entry</title>
<link href="../styles/admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<h1>Update Blog Entry</h1>
<p><a href="blog_list_mysqli.php">List all entries </a></p>
<?php 
if (isset($error)) {
  echo "<p class='warning'>Error: $error</p>";
}
if($article_id == 0) { ?>
<p class="warning">Invalid request: record does not exist.</p>
<?php } else { ?>
<form id="form1" method="post" action="">
  <p>
    <label for="title">Title:</label>
    <input name="title" type="text" class="widebox" id="title" value="<?php echo htmlentities($title, ENT_COMPAT, 'utf-8'); ?>">
  </p>
  <p>
    <label for="article">Article:</label>
    <textarea name="article" cols="60" rows="8" class="widebox" id="article"><?php echo htmlentities($article, ENT_COMPAT, 'utf-8'); ?></textarea>
  </p>
  <p>
    <label for="category">Categories:</label>
    <select name="category[]" size="5" multiple id="category">
    <?php
	// get categories
	$getCats = 'SELECT cat_id, category FROM categories
	            ORDER BY category';
	$categories = $conn->query($getCats);
	while ($row = $categories->fetch_assoc()) {
	?>
    <option value="<?php echo $row['cat_id']; ?>" <?php
    if (in_array($row['cat_id'], $selected_categories)) {
	  echo 'selected';
	} ?>><?php echo $row['category']; ?></option>
    <?php } ?>
    </select>
  </p>
  <p>
    <label for="image_id">Uploaded image:</label>
    <select name="image_id" id="image_id">
      <option value="">Select image</option>
      <?php
	  // get the list of images
	  $getImages = 'SELECT image_id, filename
	                FROM images ORDER BY filename';
	  $images = $conn->query($getImages);
	  while ($row = $images->fetch_assoc()) {
	  ?>
      <option value="<?php echo $row['image_id']; ?>"
      <?php
	  if ($row['image_id'] == $image_id) {
		echo 'selected';
	  }
	  ?>><?php echo $row['filename']; ?></option>
      <?php } ?>
    </select>
  </p>
  <p>
    <input type="submit" name="update" value="Update Entry" id="update">
    <input name="article_id" type="hidden" value="<?php echo $article_id; ?>">
  </p>
</form>
<?php } ?>
</body>
</html>