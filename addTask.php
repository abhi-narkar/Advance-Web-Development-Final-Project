<?php
require_once("ProjectModel.php");
require_once("HttpResource.php");

class addTask extends HttpResource {
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

		global $messages;
		$title = (empty($_POST["title"])) ? "" : (trim($_POST["title"]));
		$description = (empty($_POST["description"])) ? "" : (trim($_POST["description"]));
		$feature_id = (empty($_POST["feature_id"])) ? "" : (trim($_POST["feature_id"]));
		$status_id = (empty($_POST["status_id"])) ? "" : $_POST["status_id"];
		$owner_id = (empty($_POST["owner_id"])) ? "" : $_POST["owner_id"];
    $estimated_duration = (empty($_POST["estimated_duration"])) ? "" : $_POST["estimated_duration"];
    $actual_duration = (empty($_POST["actual_duration"])) ? "" : $_POST["actual_duration"];

		if ($title == "") {
			$messages["$title"] = "title must be set";
		}
		else if($description == ""){
			$messages["description"] = "description must be set";
		}

		else {
			$array = ProjectModel::addTask($title,$feature_id,$status_id,$owner_id,$description,$estimated_duration,$actual_duration);

			if(is_numeric($array)){
				$this->statusCode = 200;
				$newTask = ProjectModel::gettaskById($array);
				$messages['result'] = $newTask;
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
  $description = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["description"];
  $task_status = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["task_status"];



	if (!array_key_exists("title", $messages)) {
		$messages["title"] = "";
	}
	else {
		$messages["title"] = "<span style='color: red;'>$messages[title]</span>";
	}

	if (!array_key_exists("description", $messages)) {
		$messages["description"] = "";
	}
	else {
		$messages["description"] = "<span style='color: red;'>$messages[description]</span>";
	}

	$userRoles = ProjectModel::getTaskStatus();
	$result = '<td><select name="task_status">';

	for ($i=0;$i<count($task_status);$i++){
		if($task_status[$i]['id'] == $task_status){
			$result .= '<option value="'.$task_status[$i]['id'].'" selected>'.$task_status[$i]['name'].'</option>';
		}
		else{
			$result .= '<option value="'.$task_status[$i]['id'].'">'.$task_status[$i]['name'].'</option>';
		}
	}

	$result .= '</select></td>';

	print '<tr id="addFeatureRow">
	<td><input type="text" name="title" placeholder="Feature Name" value="'.$title.'">'.$messages['title'].'</td>
	<td><input type="text" name="description" placeholder="Feature Functionality" value="'.$description.'">'.$messages['description'].'</td>'.$result;

}

	}
}

addTask::run();
?>
