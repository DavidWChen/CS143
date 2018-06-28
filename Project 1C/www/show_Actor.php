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
<h1>Show Actor Information</h1>

<?php
	$acid = $_GET['acid'];
	if ($acid != "")
	{
		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		$sql1 = "SELECT CONCAT(first,' ',last) AS name,sex,dob,dod FROM Actor WHERE id = $acid;"; 
		$sql2 = "SELECT id, role, title FROM Movie INNER JOIN MovieActor ON Movie.id = MovieActor.mid WHERE MovieActor.aid = $acid;";
	}
	else if ($acid=="")
	{
		echo 'Find a movie to review from the search page'
		?>
		<form action ="./search.php" method="GET">
			<label for="search">Search:</label>
			<input type="text" class = "form-control" name="expr"><br>
		<input type="Submit" name="submit" class="btn btn-warning" values="Search">
		</form>
		<?php
		return;
	}		
	$rs1 = $db->query($sql1);
	$rs2 = $db->query($sql2);
?>	
<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<?php
			$finfo = $rs1->fetch_fields();
			foreach ($finfo as $val) {
			 	echo '<td><b>'.$val->name.'</b></td>';
			}
		?>
	</tr>
	<tbody>
		<?php
			echo "Actor information is:";
			while ($row = $rs1->fetch_assoc()) {
				echo '<tr>';
				foreach ($row as $val) {
					if($val) {
						echo '<td>'.$val.'</td>';
					} 
					else 
					{
						echo '<td>'.'N/A'.'</td>';
					}
				}	
				echo '</tr>';
			}
		?>
	</tbody>
</table><br>


<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<td><b>Role</b></td>
		<td><b>Movie</b></td>
	</tr>
	<tbody>
		<?php
			echo "Actor's Movies and Role:";
			while ($row = $rs2->fetch_assoc()) {	
				?>
				<tr>
				<td>
					<?php echo $row['role'];?>
				</td>
				<td>
					<a href="./show_Movie.php?moid=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a>
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


	
