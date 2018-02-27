<html>
<head>
<title>HOME PAGE</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">
</head>
<body class="text-center">
<?php

/** Exemple of a form sent to the same url */
// Error messages

require_once("ProjectModel.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$messages = array();
switch ($_SERVER["REQUEST_METHOD"]) {
	case "GET":
		include_once("loginForm.php");
		break;
	case "POST":
		do_post();
		break;
	default:
		die("Not implemented");
}


function do_post() {
	
	$action = (empty($_POST["action"])) ? "" : ($_POST["action"]);
	  
	if($action == "logout"){
		session_destroy();
		header("Location: LogIn",true,301);
		exit();
	}
	else{
		global $messages;
		$name = (empty($_POST["name"])) ? "" : (trim($_POST["name"]));
		$pass = (empty($_POST["password"])) ? "" : ($_POST["password"]);
		  
		  
		if ($name == "") {
			$messages["name"] = "name must be set";
		}
		else if($pass == ""){
			$messages["password"] = "password must be set";
		}
		else if (strlen($name) < 3) {
			$messages["name"] = "Name must have at least 3 characters";
		}
		else {
			$array = ProjectModel::getByLoginPassword($name,$pass);
			if($array != null){
					
				$_SESSION["person"] = $array;
				header("Location: Home.php",true,301);
				exit();
				//print "Login successfully";
			}
			else{
				$messages["loginResponse"] = "Username or password is not correct";
			}
		}
	}
	include_once("loginForm.php");
}?>
</body>
</html>
