<!DOCTYPE html>
<html>
<head><title>Project1B</title></head>
<h1>MySQL Movie Database</h1>

<form method="GET">
		<textarea action="./query.php" name="query" cols="60" rows="8"><?php echo $query; ?></textarea><br/>
		<input type="submit" value="Submit" />
</form>

<?php
	$query = $_GET["query"] ?? "";
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
   		die('Unable to connect to database [' . $db->connect_error . ']');
	}

	if ($query != "")
	{
		$rs = $db->query($query);
		if($rs) {
?>

<table border=1 cellspacing=1 cellpadding=2>
	<tr align=center>
		<?php
			$finfo = $rs->fetch_fields();
    		foreach ($finfo as $val) {
			 	echo '<td><b>'.$val->name.'</b></td>';
			}
		?>
	</tr>
	<tbody>
		<?php
			while ($row = $rs->fetch_assoc()) {
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
</table>

<?php
			$rs->free;
			}
		}
	$db->close();
?>

</html>