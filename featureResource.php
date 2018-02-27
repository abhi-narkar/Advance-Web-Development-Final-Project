<?php
ob_start();
require_once("ProjectModel.php");
require_once("HttpResource.php");

class FeatureResource extends HttpResource {
	/** Person id */
  protected $id;

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
			$this->exit_error(400, "idRequis");
		}
	}



	function do_post() {
		$action = (empty($_POST["action"])) ? "" : ($_POST["action"]);


		if($action == "logout"){
			session_destroy();
			header("Location: LogIn",true,301);
			exit();
		}
	}

	function do_get(){
		$result = ProjectModel::getFeatureDetails($this->id);
		$tests = '';
		$html = '';
		$testsList = ProjectModel::getAcceptanceTest($this->id);
		$testsHtml = '<tr><th>Acceptance Test Description</th><th>Status</th></tr>';


		/*for($i=0;$i<count($result);$i++){
			$members .= '<p>'.$result[$i]['first_name'].' '.$result[$i]['last_name'].' as <b>'.$result[$i]['role_name'].'</b></p>';
		}*/
		for($i=0;$i<count($testsList);$i++){
			$testsHtml .= '<tr>
			<td>'.$testsList[$i]['description'].'</td>';
      if($testsList[$i]['is_satisfied'] == 1){
        $testsHtml .= '<td>True</td></tr>';
      }
      else{
        $testsHtml .= '<td>False</td></tr>';
      }
		}

		if ($result != null){
			$html .= "<h3>Feature ".$this->id."</h3>";
			$html .= '<div class="section"><p><b>Title:</b> <i>'.$result[0]['title'].'</i></p></div>';
			$html .= '<div class="section"><p><b>Functionality:</b> '.$result[0]['functionality'].'</p></div>';
			$html .= '<div class="section"><p><b>Benefit:</b> '.$result[0]['benefit'].'</p></div>';
      $html .= '<div class="section"><p><b>Priority:</b> '.$result[0]['priority'].'</p></div>';
      $html .= '<div class="section"><p><b>User Role:</b> '.$result[0]['user_name'].'</p></div>';

			if(!empty($testsList)){
				/*$html .= '<div class="section"><p><b>Members</b>
					<img id="add" src="./image/plus.png"/>
					</p>'.$members.'</div>';*/
				$html .='<table id="testList" cellspacing="10"><tbody>'.$testsHtml.'</tbody></table>';

			}
			else{
				/*$html .= '<div class="section"><p><b>Members</b>
					</p>'.$members.'</div>';*/
				$html .= '<div class="section"><p><b>No acceptance test</b></p></div>';
			}
      $html .='<div class="section"><button id="addFeature" class="btn btn-default" name="'.$this->id.'" iteration="'.$result[0]['iteration_id'].'">Add test</button></div>';
		}
		print $html;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Feature Details</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

  <script>
  $(document).ready(function() {
    //function for add iteration
    $("button#addFeature").click(function(event) {
      var id = $("button#addFeature").attr("name");
      var i_id = $("button#addFeature").attr("iteration");
      window.location = "./AcceptanceTest.php?id="+id+"&i_id="+i_id;
    });
  });
  </script>
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
<div id="body">
<?php
	FeatureResource::run();
?>
</div>
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
