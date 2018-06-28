<!DOCTYPE html>
<html>
<head><title>Calculator</title></head>
<style></style>
<body>
	<h1>Calculator</h1>
	"(Ver 1.0 4/9/2018)"
	<br>

<form method="GET">
	<input type="text" name="expr" />
	<input type="submit" value="Calculate" />
</form>

<?php

    $expr = $_GET["expr"] ?? "";
	$expr = str_replace("--", "+", $expr);
	$expr = str_replace(" ", "", $expr);
	$valid = preg_match("/^[+\-*\/0-9. ]+$/", $expr);
	$divByZero = preg_match("/^[0-9 ]+\/ *0[+\-*\/ ]*$/", $expr);
	
	if($expr!=""){
	if ($divByZero){
		?>
				<h2>Result</h2>
				<?php
		echo "Division by zero error.";
	}
	elseif ($valid)
		{	
			                ?>
				<h2>Result</h2>
				<?php
			try 
		    {

				$value = @eval("return ($expr);");
				echo $expr." = ".$value;
            } 
            catch (ParseError $e) 
            {
                echo "Invalid expression.";
            }
		}
	}

?>

</body>
</html>
