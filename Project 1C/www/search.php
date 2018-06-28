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
<h1>Search Actor/Movie</h1>

<form action ="./search.php" method="GET">
<div class="form-group">
	<label for="search">Search:</label>
	<input type="text" class = "form-control" name="expr">
</div>
<input type="Submit" name="submit" class="btn btn-warning" values="Search">
</form>



<?php
$submit = $_REQUEST["submit"] ?? "";
if ($submit=="")
{return;}

	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
   		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	$exp = $_GET["expr"]??'';
	$exp = $db->real_escape_string($exp);
	$expr = explode(' ', $exp);
	$numArgs = count($expr);

	if ($exp == '')
	{
		$sql1 = "SELECT id, CONCAT(first,' ',last) AS name, dob FROM Actor;";
		$sql2 = "SELECT id, title, year FROM Movie;";
	}
	else
	{
		$sql1 = "SELECT id, CONCAT(first,' ',last) AS name, dob FROM Actor WHERE";
		$sql2 = "SELECT id, title, year FROM Movie WHERE";
	}
	for($i = 0; $i< $numArgs; $i++)
	{
		$sql1 = $sql1." (first LIKE '%".$expr[$i]."%' OR last LIKE '%".$expr[$i]."%')";
		$sql2 = $sql2." (title LIKE '%".$expr[$i]."%')";
		if($i >= $numArgs-1)
		{
			$sql1 = $sql1." ;";
			$sql2 = $sql2." ;";
        }
        else 
        {
            $sql1 = $sql1." AND ";
            $sql2 = $sql2." AND ";
        }
		
	}

	$rs1 = $db->query($sql1);
	$rs2 = $db->query($sql2);
?>
	
	
<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<td><b>Name</b></td>
		<td><b>Date of Birth</b></td>
	</tr>
	<tbody>
		<?php
			echo "matching Actors are:";
			while ($row = $rs1->fetch_assoc()) {
				?>
				<tr>
				<td>
					<a href="./show_Actor.php?acid=<?php echo $row['id']; ?>"><?php echo $row['name'];?></a>
				</td>
				<td>
					<a href="./show_Actor.php?acid=<?php echo $row['id']; ?>"><?php echo $row['dob']; ?>
				</td>
				</tr>
				<?php
			}


		?>
	</tbody>
</table><br>


<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<td><b>Title</b></td>
		<td><b>Year Released</b></td>
	</tr>
	<tbody>
		<?php
			echo "matching Movies are:";
			while ($row = $rs2->fetch_assoc()) {
				?>
				<tr>
				<td>
					<a href="./show_Movie.php?moid=<?php echo $row['id']; ?>"><?php echo $row['title'];?></a>
				</td>
				<td>
					<a href="./show_Movie.php?moid=<?php echo $row['id']; ?>"><?php echo $row['year']; ?>
				</td>
				</tr>
				<?php
			}
		$rs1->free;
		$rs2->free;
		$db->close();
		?>
	</tbody>
</table>

</div>
</body>

</html>