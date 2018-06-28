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
<h1>Show Movie Information</h1>

<?php
	$moid = $_GET["moid"];
	if ($moid != "")
	{
		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		
		$sql1 = "
		SELECT title,company,rating,CONCAT(first,' ',last) AS dname
		FROM Movie, Director, MovieDirector, MovieGenre
		WHERE Director.id = MovieDirector.did
		AND Movie.id = MovieGenre.mid
		AND MovieDirector.mid = $moid
		AND Movie.id = $moid;";
		 
		$sql2 = "
		SELECT id, CONCAT(first,' ',last) as name, role
		FROM Actor, MovieActor
		WHERE MovieActor.aid = Actor.id
		AND MovieActor.mid = $moid;";
		
		$sql3 = "
		SELECT genre 
		FROM MovieGenre
		WHERE MovieGenre.mid = $moid;";
		
		$sql4 = "
		SELECT AVG(rating) AS avgr, COUNT(rating) AS cntr 
		FROM Review
		WHERE Review.mid = $moid;";
		
		$sql5 = "
		SELECT name,time,comment,rating
		FROM Review
		WHERE Review.mid = $moid;";
		
	}
	else if ($moid=="")
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
	$rs3 = $db->query($sql3);
	$rs4 = $db->query($sql4);
	$rs5 = $db->query($sql5);

	$row1 = $rs1->fetch_assoc();
	echo ("Movie Name: ".$row1['title']."<br>");
	echo ("Producer: ".$row1['company']."<br>");
	echo ("MPAA Rating: ".$row1['rating']."<br>");
	echo ("Director: ".$row1['dname']."<br>");
	echo ("Genre: ");

	while($row3 = $rs3->fetch_assoc())
	{
		foreach ($row3 as $val) {
			echo ($val." ");
		}
	}
	echo "<br><br>";

	?>	


<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<td><b>Actors</b></td>
		<td><b>Role</b></td>
	</tr>
	<tbody>
		<?php
			echo "Actor's Movies and Role:";
			while ($row = $rs2->fetch_assoc()) {	
				?>
				<tr>
				<td>
					<a href="./show_Actor.php?acid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
					
				</td>
				<td>
					<?php echo $row['role'];?>
				</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>

<?php
echo "<br>";
echo "User Review:"."<br>";
	$row4 = $rs4->fetch_assoc();

	echo ("The average user rating for this movie is ".$row4['avgr']."/5 based on ".$row4['cntr']." review(s).<br>");
	echo "<br>";
	echo "<a href='./review.php?mid=$moid'>Leave a review.</a>"; 
	echo "Comment details shown below:"."<br>";

	while($row5 = $rs5->fetch_assoc())
	{
		echo ($row5['name']." rates the this movie with score ".$row5['rating']." and left a review at ".$row5['time']."<br>");
		echo ("comment:"."<br>".$row5['comment']."<br><br>");
	}
	echo "<br><br>";
	$rs1->free;
	$rs2->free;
	$rs3->free;
	$rs4->free;
	$rs5->free;
	$db->close();
?>	

</div>
</body>
</html>