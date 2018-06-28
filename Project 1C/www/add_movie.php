<!DOCTYPE html>
<html>
<head><title>Project1C</title>
<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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

<h1>Add Movie</h1>


<form action ="./add_movie.php" method="POST">

<div class="form-group">
	<label for="title">Title</label>
	<input type="text" class = "form-control" name="title" placeholder ="Title">
</div>

<div class="form-group">
	<label for="company">Company</label>
	<input type="text" class = "form-control" name="company" placeholder="Company">
</div>

<div class="form-group">
	<label for="year">Year</label>
	<input type="text" class = "form-control" name="year" placeholder ="YYYY">
</div>

<div class="form-group">
	<label for="rating">MPAA Rating</label>
	<select class = "form-control" name = "rating">
		<option value = "G">G</option>
		<option value = "PG">PG</option>
		<option value = "PG-13">PG-13</option>
		<option value = "R">R</option>
		<option value = "NC-17">NC-17</option>
		<option value = "surrendere">surrendere</option>
	</select>
</div>

<div class="form-group">
	<label>Genre:</label><br>
		<label><input type="checkbox" name="genre[]" value = "Action">  Action   </label>
		<label><input type="checkbox" name="genre[]" value = "Adult">  Adult   </label>
		<label><input type="checkbox" name="genre[]" value = "Adventure">  Adventure   </label>
		<label><input type="checkbox" name="genre[]" value = "Animation">  Animation   </label>
		<label><input type="checkbox" name="genre[]" value = "Comedy">  Comedy   </label>
		<label><input type="checkbox" name="genre[]" value = "Crime">  Crime   </label>
		<label><input type="checkbox" name="genre[]" value = "Documentry">  Documentry   </label>
		<label><input type="checkbox" name="genre[]" value = "Drama">  Drama   </label>
		<label><input type="checkbox" name="genre[]" value = "Family">  Family   </label>
		<label><input type="checkbox" name="genre[]" value = "Fantasy">  Fantasy   </label>
		<label><input type="checkbox" name="genre[]" value = "Horror">  Horror   </label>
		<label><input type="checkbox" name="genre[]" value = "Musical">  Musical  </label> 
		<label><input type="checkbox" name="genre[]" value = "Mystery">  Mystery   </label>
		<label><input type="checkbox" name="genre[]" value = "Romance">  Romance   </label>
		<label><input type="checkbox" name="genre[]" value = "Sci-Fi">  Sci-Fi   </label>
		<label><input type="checkbox" name="genre[]" value = "Short">  Short   </label>
		<label><input type="checkbox" name="genre[]" value = "Thriller">  Thriller   </label>
		<label><input type="checkbox" name="genre[]" value = "War">  War   </label>
		<label><input type="checkbox" name="genre[]" value = "Western">  Western   </label>
</div>
<input type="Submit" name="submit" class="btn btn-warning" values="Add">

</form>

<?php

	$title = $_POST["title"];
	$company = $_POST["company"];
	$year = $_POST["year"];
	$rating = $_POST["rating"];
	$genre= $_POST["genre"];
	$submit = $_POST["submit"] ?? "";

	if ($submit!="")
	{
		if ($title == "" || $company=="" || $year=="")
		{
			echo "<h5>Invalid Input:</h5>";
			
			if ($title == "")
			{
				echo "Title cannot be Empty<br>";
			}
			if ($company == "")
			{
				echo "Company cannot be Empty<br>";
			}
			if ($year == "")
			{
				echo "Year cannot be Empty<br>";
			}
			return;
		}

		$valid_year = preg_match("/^[0-9]{4}$/", $year);
		if (!$valid_year)
		{
			echo "<h3>Invalid Input:</h3>";
			echo "Invalid Year";
			return;
		}


		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
   			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		$rs = $db->query("SELECT id FROM MaxMovieID;");
		$val = $rs->fetch_assoc();
		$old_id = $val["id"];
		$id = $old_id+1;

		$sql = "UPDATE MaxMovieID SET id =".$id." WHERE id=".$old_id.";";
		$db->query($sql);

		$sql = "INSERT INTO Movie (id, title, year, rating, company)  VALUES (".$id.",'".$title."',".$year.",'".$rating."','".$company."'); ";

		$db->query($sql);

		foreach ($genre as $val)
		{
			$sql = "INSERT INTO MovieGenre (mid, genre) VALUES (".$id.",'".$val."'); ";
			$db->query($sql);
		}
		echo '<h2>Successfully Added</h2>';
		$rs->free;
		$db->close();	
	}
?>
</div>
</body>
</html>