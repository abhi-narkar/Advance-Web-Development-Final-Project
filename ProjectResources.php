<?php
ob_start();
require_once("ProjectModel.php");
require_once("HttpResource.php");

class ProjectResources extends HttpResource {
		
	function do_post() {
		$action = (empty($_POST["action"])) ? "" : ($_POST["action"]);
		
		
		if($action == "logout"){
			session_destroy();
			header("Location: LogIn",true,301);
			exit();
		}
	}
	
	function do_get(){
		$result = ProjectModel::getProjectSmallDetail($_SESSION['person']['id']);
		$html = '<h1>List of project</h1>';
		
		if ($result != null){
			$html .= '<table><tbody><tr><th>Project Name</th><th>Description</th><th>Created On</th></tr>';
		
			for($i=0;$i<count($result);$i++){
				$html .= '<tr>
				<td><a href="./project-'.$result[$i]['id'].'">'.$result[$i]['name'].'</a></td>
				<td>'.$result[$i]['description'].'</td>
				<td>'.$result[$i]['created_at'].'</td></tr>';
			}
			$html .= "</tbody></table>";
		}
		print $html;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>All Projects</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
 
</head>
<body>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (array_key_exists("person", $_SESSION)) {
	include("loginForm.php");
	print "<a href='./home'>Back to home</a>";
	$messages = array();
?>
<?php
	ProjectResources::run();
?>
</body>
<?php
}
else{
	session_destroy();
	header("Location: LogIn",true,301);
	exit();
}
?>
</html>