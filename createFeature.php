<?php
require_once("ProjectModel.php");
require_once("HttpResource.php");

class FeatureResource extends HttpResource {
	public function init() {
		//self::$roleList = ProjectModel::getProjectRole();
		if (isset($_GET["project_id"])) {
			if (is_numeric($_GET["project_id"])) {
				$this->id = 0 + $_GET["project_id"]; // transformer en numerique
		
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
	
		global $messages;
		$name = (empty($_POST["title"])) ? "" : (trim($_POST["title"]));
		$func = (empty($_POST["func"])) ? "" : (trim($_POST["func"]));
		$benefit = (empty($_POST["benefit"])) ? "" : (trim($_POST["benefit"]));
		$user_role_id = (empty($_POST["user_role"])) ? "" : $_POST["user_role"];
		$iteration_id = (empty($_POST["iteration_id"])) ? "" : $_POST["iteration_id"];
		
		if ($name == "") {
			$messages["name"] = "title must be set";
		}
		else if($func == ""){
			$messages["func"] = "functionality must be set";
		}
		else if($benefit == ""){
			$messages["benefit"] = "benefit must be set";
		}
		else {
			$array = ProjectModel::createFeature($name,$func,$benefit,$iteration_id,$this->id,$user_role_id);
			
			if(is_numeric($array)){
				$this->statusCode = 200;
				$newFeature = ProjectModel::getFeatureById($array);
				$messages['result'] = $newFeature;
			}
			else{
				$this->exit_error(409,$array[1].' '.$array[2]);
			}
		}
	
	$this->do_get();
}
function do_get(){
	global $messages;
	if ($messages == null){
	  $messages = array();
	}
	if(array_key_exists("result", $messages))
	{
		$this->headers[] = "Content-type: text/json; charset=utf-8";
		$this->body = json_encode($messages['result'], JSON_UNESCAPED_UNICODE);
	}
	else{
	// Get input value if any
  $title = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["title"];
  $func = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["func"];
  $benefit = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["benefit"];
  $user_role = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["user_role"];
  
  
  
	if (!array_key_exists("name", $messages)) {
		$messages["name"] = "";
	}
	else {
		$messages["name"] = "<span style='color: red;'>$messages[name]</span>";
	}
	  
	if (!array_key_exists("func", $messages)) {
		$messages["func"] = "";
	}
	else {
		$messages["func"] = "<span style='color: red;'>$messages[func]</span>";
	}
	if (!array_key_exists("benefit", $messages)) {
		$messages["benefit"] = "";
	}
	else {
		$messages["benefit"] = "<span style='color: red;'>$messages[benefit]</span>";
	}
	$userRoles = ProjectModel::getUserRole();
	$result = '<td><select name="user_role">';
	
	for ($i=0;$i<count($userRoles);$i++){
		if($userRoles[$i]['id'] == $user_role){
			$result .= '<option value="'.$userRoles[$i]['id'].'" selected>'.$userRoles[$i]['name'].'</option>';
		}
		else{
			$result .= '<option value="'.$userRoles[$i]['id'].'">'.$userRoles[$i]['name'].'</option>';
		}
	}
	
	$result .= '</select></td>';

	print '<tr id="addFeatureRow">
	<td><input type="text" name="feature_name" placeholder="Feature Name" value="'.$title.'">'.$messages['name'].'</td>
	<td><input type="text" name="func" placeholder="Feature Functionality" value="'.$func.'">'.$messages['func'].'</td>
	<td><input type="text" name="benefit" placeholder="Feature Benefit" value="'.$benefit.'">'.$messages['benefit'].'</td>'.$result;
	}
	}
}

FeatureResource::run();
?>