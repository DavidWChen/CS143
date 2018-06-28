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
<h1>Add Actor/Director</h1>


<form action ="./add_person.php" method="POST">

<input type="radio" name = "identity" checked="checked" value ="Actor"> Actor
<input type="radio" name = "identity" value ="Director"> Director

<div class="form-group">
	<label for="first name">First Name</label>
	<input type="text" class = "form-control" name="first" placeholder = "First Name">
</div>

<div class="form-group">
	<label for="last name">Last Name</label>
	<input type="text" class = "form-control" name="last" placeholder = "Last Name">
</div>

<input type="radio" name = "sex" checked="checked" value ="Male"> Male
<input type="radio" name = "sex" value ="Female"> Female

<div class="form-group">
	<label for="dob">Date of Birth</label>
	<input type="text" class = "form-control" name="dob" placeholder = "YYYY-MM-DD">
</div>

<div class="form-group">
	<label for="dod">Date of Death</label>
	<input type="text" class = "form-control" name="dod" placeholder = "YYYY-MM-DD">
</div>

<input type="Submit" name="submit" class="btn btn-warning" values="Add">

</form>

<?php

	$identity = $_POST["identity"];
	$first = $_POST["first"];
	$last = $_POST["last"];
	$sex = $_POST["sex"];
	$dob = $_POST["dob"];
	$dod = $_POST["dod"];
	

	$submit = $_POST["submit"] ?? "";

	if ($submit!="")
	{
		if ($first == "" || $last=="" || $dob=="")
		{
			echo "<h5>Invalid Input:</h5>";
			
			if ($first == "")
			{
				echo "First Name cannot be Empty<br>";
			}
			if ($last == "")
			{
				echo "Last Name cannot be Empty<br>";
			}
			if ($dob == "")
			{
				echo "Date of Birth cannot be Empty<br>";
			}
			return;
		}

		$valid_dob = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dob);
		$valid_dod = True;

		if ($dod != "NULL" && $dod != "")
		{
			$valid_dod = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dod);
		}

		if (!$valid_dob || !$valid_dod)
		{
			echo "<h5>Invalid Input:</h5>";
			echo "Invalid Date";
			return;
		}


		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
   			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		$rs = $db->query("SELECT id FROM MaxPersonID;");
		$val = $rs->fetch_assoc();
		$old_id = $val["id"];
		$id = $old_id+1;

		$sql = "UPDATE MaxPersonID SET id =".$id." WHERE id=".$old_id.";";
		$db->query($sql);

		if ($identity == "Actor")
		{
			if ($dod == "")
			{
				$sql = "INSERT INTO Actor (id, last, first, sex, dob, dod)  VALUES (".$id.",'".$last."','".$first."','".$sex."','".$dob."',NULL); ";
			}
			else
			{
				$sql = "INSERT INTO Actor (id, last, first, sex, dob, dod)  VALUES (".$id.",'".$last."','".$first."','".$sex."','".$dob."','".$dod."'); ";
			}
		}
		else if ($identity == "Director")
		{
			if ($dod == "")
			{
				$sql = "INSERT INTO Director (id, last, first, dob, dod)  VALUES (".$id.",'".$last."','".$first."','".$dob."',NULL); ";
			}
			else
			{
				$sql = "INSERT INTO Director (id, last, first, dob, dod)  VALUES (".$id.",'".$last."','".$first."','".$dob."','".$dod."'); ";
			}
		}
		$db->query($sql);


		echo '<h2>Successfully Added</h2>';


		$rs->free;
		$db->close();	
	}
?>

</div>
</body>
</html>