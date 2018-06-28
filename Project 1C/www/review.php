<!DOCTYPE html>
<html>
<head><title>Project1C</title>
<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link href="cover.css" rel="stylesheet">
</head>

<body class = "inner cover">
<div class="cover-container d-flex h-50 p-3 w-75 mx-auto flex-column">

    <header class="masthead mb-auto">
    <div class="inner">
    	<h3 class="masthead-brand">MySQL Movie Database</h3>
        <nav class="nav nav-masthead justify-content-center">
        <a class="nav-link" href="./index.php">Home</a>
        <a class="nav-link" href="./add_person.php">Add Actor/Director</a>
 		<a class="nav-link" href="./add_movie.php">Add Movie</a>
 		<a class="nav-link" href="./add_atom.php">Add Actor/Movie</a>
 		<a class="nav-link" href="./add_dtom.php">Add Director/Movie</a>
 		<a class="nav-link" href="./search.php">Search</a>
        </nav>
    </div>
    </header>
<h1>Review Movie</h1>

<?php	

	$id = intval($_REQUEST['mid']);
	if ($id =='')
	{
		echo 'Find a movie to review from the search page'
		?>
		<form action ="./search.php" method="GET">
			<label for="search">Search:</label>
			<input type="text" class = "form-control" name="expr">
		<input type="Submit" name="submit" class="btn btn-warning" values="Search">
		</form>
		<?php
		return;

	}

	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
   		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	

	$sql = "SELECT id, title, year FROM Movie WHERE id=".$id.";";
	echo "<form action ='./review.php?id=$id' method='POST'>";
?>



<div class="form-group">
	<label for="name">Movie</label>
	<select name = "mid" id = "id">
<?php	
	$rs = $db->query($sql);
	while ($row = $rs->fetch_assoc())
	{	
		$mid = $row["id"];
		$title = "$row[title] ($row[year])";
		echo "<option value=\"$mid\">$title</option>\n";
	}
	$rs->free;
?>
</select></div>

<div class="form-group">
	<label for="name">Your Name</label>
	<input type="text" class = "form-control" name="name" value = "Anonymous">
</div>

<div class="form-group">
	<label for="rating">Rating</label>
	<select class = "form-control" name = "rating">
		<option value = "1">1 star</option>
		<option value = "2">2 stars</option>
		<option value = "3">3 stars</option>
		<option value = "4">4 stars</option>
		<option value = "5">5 stars</option>
	</select>
</div>

<div class = "form-group">
	<textarea class = "form-control" name="comment" rows="5" placeholder = "No more than 500 characters"><?php echo $comment; ?></textarea><br/>
</div>


<input type="Submit" name="submit" class="btn btn-warning" values="Add">

</form>

<?php
	$name = $_POST["name"];
	$time = date('Y-m-d G:i:s');
	$mid = $_POST['mid'];
	$rating = $_POST["rating"];
	$comment = $_POST["comment"];
	
	$submit = $_POST["submit"] ?? "";

	if ($submit!="")
	{
		


		$sql = "INSERT INTO Review(name, time, mid, rating, comment)  VALUES ('".$name."','".$time."',".$mid.",".$rating.",'".$comment."'); ";
		$db->query($sql);


		echo '<h2>Successfully Added</h2>';
		echo "<a href='./show_Movie.php?mid=$mid'>Click this to go back to see the movie</a>"; 

		$rs->free;
		$db->close();	
	}
?>
</div>
</body>
</html>