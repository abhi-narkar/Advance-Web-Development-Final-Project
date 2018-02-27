<?php
ob_start();
require_once("ProjectModel.php");
require_once("HttpResource.php");

class ProjectMemberResource extends HttpResource {
	/** Project id */
  protected $id;
  public static $roleList;

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
	
	public static function addMemberHtml($first,$last,$role,$mess){
		self::$roleList = ProjectModel::getProjectRole();
		
		if(!is_array($mess) || $mess == null){
			$mess=array("","");
		}
		
		
		$add_input = '<div class="member"><label>First Name: </label> <input type="text" value="'.$first.'" name="first_name[]" placeholder="First Name" />'.$mess[0];
		$add_input .= '<label>Last Name: </label> <input type="text" value="'.$last.'" name="last_name[]" placeholder="Last Name" />'.$mess[1];
		$add_input .= '<label>Role: </label> <select name="project_role[]">';
	  
	  
		for ($i = 0; $i < count(self::$roleList); $i++){
			if($role == self::$roleList[$i]['name']){
				$add_input .= '<option value="'.self::$roleList[$i]['name'].'" selected>'.self::$roleList[$i]['name'].'</option>';
			}
			else{
				$add_input .= '<option value="'.self::$roleList[$i]['name'].'">'.self::$roleList[$i]['name'].'</option>';
			}
		}
		
		$add_input .= '</select><br></div>';
		
		return $add_input;
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
			
			$fname = (empty($_POST["first_name"])) ? "" : $_POST["first_name"];
			$lname = (empty($_POST["last_name"])) ? "" : $_POST["last_name"];
			$role = $_POST["project_role"];
			
			$error = false;
			for($i=0;$i<count($fname);$i++){
				if($fname[$i] == "" && $lname[$i]==""){
					$messages[$i] = array("<span style='color: red; margin-right: 10px;'>First name must be set</span>","<span style='color: red; margin-right: 10px;'>Last name must be set</span>");
					$error = true;
				}
				else if($lname[$i]==""){
					$messages[$i] = array("","<span style='color: red; margin-right: 10px;'>Last name must be set</span>");
					$error = true;
				}
				else if($fname[$i] == ""){
					$messages[$i] = array("<span style='color: red; margin-right: 10px;'>First name must be set</span>","");
					$error = true;
				}
				else{
					$messages[$i] = array("","");
				}
				
			}
			
			
			if(!$error) {
				for($i=0;$i<count($fname);$i++){
					$member_id = ProjectModel::getMemberId($fname[$i],$lname[$i]);
					if(is_numeric($member_id['id'])){
						$role_id = ProjectModel::getProjectRoleId($role[$i]);
						$addMember = ProjectModel::addProjectMember($member_id['id'],$this->id,$role_id['id']);
						if($addMember != null){
							$this->exit_error(409,$addMember[1]." : ".$addMember[2]);
						}
						else{
							$this->statusCode = 204;
							header("Location: project-".$this->id,true,301);
							exit();
						}
					}
					else{
						$mess = "Member ".$fname[$i]." ".$lname[$i]." does not exist";
						$this->exit_error(409,$mess);
					}
				}
			}
		}
		$this->do_get();
	}
	function do_get(){
		global $messages;
		if ($messages == null){
		  $messages = array(array("",""));
		}
			// Get input value if any
		$fname = ($_SERVER["REQUEST_METHOD"] == "GET") ? null : $_POST["first_name"];
		$lname = ($_SERVER["REQUEST_METHOD"] == "GET") ? null : $_POST["last_name"];
		$role = ($_SERVER["REQUEST_METHOD"] == "GET") ? null : $_POST["project_role"];
		  
		  
		  
		if(!is_array(self::$roleList)){
			$this->exit_error(500, 'no default project role');
		}
		  
		$add_input = $this->addMemberHtml($fname[0],$lname[0],$role[0],$messages[0]);
	  
		if($fname != null && $fname[0] != null){
			for ($i = 1; $i < count($fname); $i++){
				$add_input .= $this->addMemberHtml($fname[$i],$lname[$i],$role[$i],$messages[$i]);
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
		
		print "<h1>Add member to project ".$this->id."</h1>";

		print'<div class="parent">
				<p>Add more member</p>
				<img id="add" src="./image/plus.png"/>
				<img id="remove" src="./image/minus.png"/>
			</div>';
		
		print <<<END_FORM
		<form method="POST" id="add_project_member">
			
			<div id="add">
				
				$add_input
			</div>
			<button type="submit" form="add_project_member">Finish</button>
		</form>
END_FORM;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Member</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

  <?php
  $temp = array();
  print '<script>
  $(document).ready(function() {
    //function for add more row to add member
    $(".parent #add").click(function(event) {
		$("div#add").append('."'".ProjectMemberResource::addMemberHtml("","","",$temp)."'".');
    });
	$(".parent #remove").click(function(event) {
      $("div#add .member").last().remove();
    });
  });
  </script>';
  ?>
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

	ProjectMemberResource::run();
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
