<!DOCTYPE html>
<html>
<head>
<title>Create Project</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

  <script>
  $(document).ready(function() {
    //function for add iteration
    $(".parent #add").click(function(event) {
      $("div#iteration").append('<input type="date" name="iteration_deadline[]" /><br>');
    });
	$(".parent #remove").click(function(event) {
      $("div#iteration input").last().remove();
	  $("div#iteration br").last().remove();
    });
  });
  </script>
</head>

<body>

<?php
ob_start();

require_once("ProjectModel.php");
require_once("HttpResource.php");

class ProjectResource extends HttpResource {
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
		$iteration = (empty($_POST["iteration_deadline"])) ? "" : $_POST["iteration_deadline"];
		$descript = $_POST["descript"];
		
		
		if ($name == "") {
			$messages["name"] = "name must be set";
		}
		else if($iteration == "" || $iteration[0] == null){
			$messages["iteration"] = "there must be at least one iteration";
		}
		else {
			$array = ProjectModel::createProject($name,$descript,$_SESSION['person']['id']);
			
			if(is_numeric($array)){
				for ($i = 0; $i < count($iteration); $i++){
					if($iteration[$i] != null){
						$iteration_array = ProjectModel::createIteration($iteration[$i],(int) $array);
						if(!is_numeric($iteration_array)){
							$this->exit_error(409,$iteration_array[1].' '.$iteration_array[2]);
						}
					}
				}
				$this->statusCode = 204;
				header("Location: addMember.php?id=".$array,true,301);
				exit();
			}
			else{
				$this->exit_error(409,$array[1].' '.$array[2]);
			}
		}
	}
	$this->do_get();
}
function do_get(){
	global $messages;
	if ($messages == null){
	  $messages = array();
	}
	
	// Get input value if any
  $name = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["name"];
  $descript = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["descript"];
  $iteration = ($_SERVER["REQUEST_METHOD"] == "GET") ? null : $_POST["iteration_deadline"];
  $iteration_input = '<input type="date" name="iteration_deadline[]" /><br>';
  
  if($iteration != null && $iteration[0] != null){
	  $iteration_input = "";
	for ($i = 0; $i < count($iteration); $i++){
		$iteration_input .= '<input type="date" name="iteration_deadline[]" value="'.$iteration[$i].'"/><br>';
	}
  }
  
  if (!array_key_exists("name", $messages)) {
    $messages["name"] = "";
  }
  else {
    $messages["name"] = "<span style='color: red;'>$messages[name]</span>";
  }
  
  if (!array_key_exists("iteration", $messages)) {
    $messages["iteration"] = "";
  }
  else {
    $messages["iteration"] = "<span style='color: red;'>$messages[iteration]</span>";
  }
	
	print "<h1>Create a project</h1>";

	print <<<END_FORM
	<form method="POST" id="create_project">
		<label>Name</label><br>
		<input type="text" value="$name" name="name" placeholder="Project name" />$messages[name]<br>
		<label>Description</label><br>
		<textarea rows="8" cols="80" name="descript" placeholder="Description" form="create_project">$descript</textarea><br>
		<div class="parent">
			<div class="parent">
				<p>Iteration</p>
				<img id="add" src="./image/plus.png"/>
				<img id="remove" src="./image/minus.png"/>
			</div><br>
			$messages[iteration]
			<div id="iteration">
				$iteration_input
			</div>
		</div>
		<button type="submit" form="create_project">Finish and add member</button>
	</form>
END_FORM;
}
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (array_key_exists("person", $_SESSION)) {
	include("loginForm.php");
	print "<a href='./home'>Back to home</a>";
	$messages = array();

	ProjectResource::run();
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
