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
<h1>Add Director/Movie Relation</h1>

<?php	
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
   		die('Unable to connect to database [' . $db->connect_error . ']');
	}
?>

<form action ="./add_dtom.php" method="POST">

Director:
<select class = "form-control" name = "aid">
<?php	
	$rs = $db->query("SELECT id, first, last, dob FROM Director ORDER BY last, first, id;");
	while ($row = $rs->fetch_assoc())
	{	
		$did = $row["id"];
		$name = "$row[first] $row[last] ($row[dob])";
		echo "<option value=\"$did\">$name</option>\n";
	}
	$rs->free;
?>
</select><br>

Movie:
<select class = "form-control" name = "mid">
<?php	
	$rs = $db->query("SELECT id, title, year FROM Movie ORDER BY title, year, id;");
	while ($row = $rs->fetch_assoc())
	{	
		$mid = $row["id"];
		$title = "$row[title] ($row[year])";
		echo "<option value=\"$mid\">$title</option>\n";
	}
	$rs->free;
?>
</select><br>


<input type="Submit" name="submit" class="btn btn-warning" values="Add">

</form>

<?php

	$did = $_POST["aid"];
	$mid = $_POST["mid"];
	
	$submit = $_POST["submit"] ?? "";

	if ($submit!="")
	{
		$sql = "INSERT INTO MovieDirector(mid, did)  VALUES (".$mid.",".$did."); ";
	
		$db->query($sql);


		echo '<h2>Successfully Added</h2>';


		$rs->free;
		$db->close();	
	}
?>
</div>
</body>
</html>