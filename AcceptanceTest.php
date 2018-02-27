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
  /*$(document).ready(function() {
    //function for add iteration
    $(".parent #add").click(function(event) {
      $("div#iteration").append('<input type="date" name="iteration_deadline[]" /><br>');
    });
	$(".parent #remove").click(function(event) {
      $("div#iteration input").last().remove();
	  $("div#iteration br").last().remove();
    });
  });*/
  </script>
</head>

<body>

<?php
ob_start();

require_once("ProjectModel.php");
require_once("HttpResource.php");

class AddAcceptanceTest extends HttpResource {
  /** Person id */
  protected $id;
  protected $iteration_id;

  /** Initialize $id. Send 400 if id missing or not positive integer */
  public function init() {
    //self::$roleList = ProjectModel::getProjectRole();
    if (isset($_GET["id"])) {
      if (is_numeric($_GET["id"])) {
        $this->id = 0 + $_GET["id"]; // transformer en numerique

        if (!is_int($this->id) || $this->id <= 0) {
          $this->exit_error(400, "idNotPositiveInteger");
        }
      }
      else {
        $this->exit_error(400, "idNotPositiveInteger");
      }
    }
    else {
      $this->exit_error(400, "iteration id Requis");
    }
    if (isset($_GET["i_id"])) {
      if (is_numeric($_GET["i_id"])) {
        $this->id = 0 + $_GET["i_id"]; // transformer en numerique

        if (!is_int($this->iteration_id) || $this->iteration_id <= 0) {
          $this->exit_error(400, "iterationIdNotPositiveInteger");
        }
      }
      else {
        $this->exit_error(400, "iterationIdNotPositiveInteger");
      }
    }
    else {
      $this->exit_error(400, "iterationIdRequis");
    }
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
		$description = (empty($_POST["description"])) ? "" : (trim($_POST["description"]));
    $status = $_POST["test_status"];


		if ($description == "") {
			$messages["description"] = "description must be set";
		}

    $result = ProjectModel::addTest($this->id,$description,$this->iteration_id,$status);
    if($result == null){

      $this->statusCode = 204;
      header("Location: featureResource.php?id=".$this->id,true,301);
      exit();
    }
    else{
      $this->exit_error(409,$result[1].' '.$result[2]);
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

  $description = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["description"];




  if (!array_key_exists("description", $messages)) {
    $messages["description"] = "";
  }


	print "<h1>Acceptance Test Description</h1>";

	print <<<END_FORM
	<form method="POST" id="acceptance_desc">
		<label>Description</label><br>
		<input type="text" value="$description" name="Description" placeholder="Test Description" />$messages[description]<br>
		<label>Test Status</label><br>
    <select name="test_status">
      <option value="0">False</option>
      <option value="1">True</option>
      </option>
    </select>
		<button type="submit" form="acceptance_desc">Add Test</button>
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

	AddAcceptanceTest::run();
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
