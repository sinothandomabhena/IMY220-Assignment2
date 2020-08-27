<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbuser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

	if(isset($_POST["submit"])){
		$target_dir = "uploads/";
		$uploadFile = $_FILES["picToUpload"];
		$target_file = $target_dir . basename($uploadFile["name"]);
		$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
		$userID = $_POST["userID"];
		
		
		//$tname = $_FILES["picToUpload"]["tmp_name"];
		// Check if image is an actual image (Note that this method is unsafe)
		
		if(( ($uploadFile["type"] == "image/jpeg" || $uploadFile["type"] == "image/jpg") && $uploadFile["size"] < 1000000)){
			if($uploadFile["error"] > 0){
				echo"Error: " . $uploadFile["error"] . "<br/>";
			}
			else{
				move_uploaded_file($uploadFile["tmp_name"],$target_file);
				$sql = "INSERT into tbgallery(user_id, filename) VALUES('$userID','$target_file')";

				if(mysqli_query($mysqli,$sql)){
					echo"file uploaded.";
				}
				else{
					echo"error";
				}
			}
		}
		else{
			echo "invalid file";
		}

		/*$check = getimagesize($uploadFile["tmp_name"]);
		if($check !== false){
			echo "File is an image – " . $check["mime"] . ".";
		}
		else {
			echo "File is not an image.";
		}*/
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Sinothando Mabhena">
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form method='POST' action='' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									<input type='hidden' name='loginEmail' value = '". $row['email'] ."'/>
									<input type='hidden' name='loginPass' value = '". $row['password'] ."'/>
									<input type='hidden' name='userID' value = '". $row['user_id'] ."'/>
								</div>
							  </form>";
					echo    "<h1><i>La Galerie<i/></h1><div class ='row imageGallery'>";
							$sql = "SELECT filename from tbgallery where user_id = " . $row["user_id"];
							$res = $mysqli->query($sql);
							if($res->num_rows > 0){
								while($row = mysqli_fetch_array($res)){
									$filename = $row["filename"];
									//echo $filename;
									echo "<div class ='col-3' style = 'background-image: url(".$filename.");'></div>";
								}
							}
					echo "<div/>";

				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>