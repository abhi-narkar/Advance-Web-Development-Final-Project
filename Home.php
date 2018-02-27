<!DOCTYPE html>
<html>
<head>
<title>HOME PAGE</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
</head>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (array_key_exists("person", $_SESSION)) {
	include("loginForm.php");
	switch ($_SERVER["REQUEST_METHOD"]) {
		case "GET":
?>
<body>
	<div>	
		<h1 class="display-3">Welcome to the IAM System</h1>
	</div>
	<div class="menu">
		<p>Create a project</p>
		<a href="./create-project">Create!</a>
	</div>
	<div class="menu">
		<p>See the previous projects</p>
		<a href="./all-projects">See!</a>
	</div>
</body>
<?php
			break;
		case "POST":
			session_destroy();
			header("Location: LogIn",true,301);
			exit();
			break;
	default:
		die("Not implemented");
	}
?>



<?php
}
else{
	session_destroy();
	header("Location: LogIn",true,301);
	exit();
}
?>
</html>