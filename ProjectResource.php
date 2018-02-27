<?php
ob_start();
require_once("ProjectModel.php");
require_once("HttpResource.php");

class ProjectResource extends HttpResource {
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
		$result = ProjectModel::getProjectDetails($this->id);
		$members = '';
		$html = '';
		$featuresList = ProjectModel::getCurrentIterationFeaturesDetails($this->id);
		$featureHtml = '<tr><th>Feature Title</th><th>Functionality</th><th>Benefit</th><th>User Role</th></tr>';


		for($i=0;$i<count($result);$i++){
			$members .= '<p>'.$result[$i]['first_name'].' '.$result[$i]['last_name'].' as <b>'.$result[$i]['role_name'].'</b></p>';
		}
		for($i=0;$i<count($featuresList);$i++){
			$featureHtml .= '<tr>
			<td><a href="./feature-'.$featuresList[$i]['feature_id'].'">'.$featuresList[$i]['title'].'</a></td>
			<td>'.$featuresList[$i]['functionality'].'</td>
			<td>'.$featuresList[$i]['benefit'].'</td>
			<td>'.$featuresList[$i]['user_role_name'].'</td></tr>';
		}

		if ($result != null){
			$html .= "<h3>Project ".$this->id."</h3>";
			$html .= '<div class="section"><p><b>Name:</b> <i>'.$result[0]['project_name'].'</i></p></div>';
			$html .= '<div class="section"><p><b>Description:</b> '.$result[0]['description'].'</p></div>';
			$html .= '<div class="section"><p><b>Created on:</b> '.$result[0]['created_at'].'</p></div>';

			if(!empty($result[0]['iteration_id'])){
				$html .= '<div class="section"><p><b>Members</b>
					<img id="add" src="./image/plus.png"/>
					</p>'.$members.'</div>';
				$html .='<div class="section"><p><b>Current Iteration Deadline: </b>'.$result[0]['deadline'].'</p></div>
				<table id="featureList" cellspacing="10"><tbody>'.$featureHtml.'</tbody></table>
				<div class="section"><button id="addFeature" class="btn btn-default" name="'.$result[0]['iteration_id'].'">Add feature</button></div>';
			}
			else{
				$html .= '<div class="section"><p><b>Members</b>
					</p>'.$members.'</div>';
				$html .= '<div class="section"><p><b>The project is done</b></p></div>';
			}
		}
		print $html;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Project Details</title>
<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="./css/custom.css">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

  <script>
	$(document).ready(function() {
		var doPost = false;
		var project_id = <?php print $_GET["id"]?>;
		$('#createBtn').css('display','none');
		//function for add more row to add member
		$("img#add").click(function(event) {
			window.location = "./add-members-to-project-"+project_id;
		});
		$("#addFeature").click(function(event) {
			if(!doPost){
				$.ajax({
					type: "GET",
					url: './createFeature.php?project_id='+project_id,
					success: function(data)
					{
						$('#featureList > tbody:last-child').append(data);
						$('#addFeature').html('Create');
						doPost = true;
					}
				});
			}
			else{
				var title = $("input[name='feature_name']").val();
				var func = $("input[name='func']").val();
				var benefit = $("input[name='benefit']").val();
				var role = $("select[name='user_role']").val();
				var iteration = $("#addFeature").attr('name');
				console.log(iteration);
				$.ajax({
					type: "POST",
					url: './createFeature.php?project_id='+project_id,
					data: {title: title, func: func, benefit: benefit, user_role: role, iteration_id: iteration},
					// What to do in case of error (status 400-599)
					// xhr stands for the underlying XMLHttpRequest object
					error: function (xhr, string) {
					  $("div#body").html("Error: " + xhr.status + xhr.responseText);
					},
					success: function(response, status, xhr){
						var ct = xhr.getResponseHeader("content-type") || "";
						$('#addFeatureRow').remove();
						if (ct.indexOf('html') > -1) {
						  //do something
						  $('#featureList > tbody:last-child').append(xhr.responseText);
						}
						if (ct.indexOf('json') > -1) {
							var newFeature = JSON.parse(xhr.responseText);
							var appendTxt = '<tr><td><a href="./feature-'+newFeature.id+'">'+newFeature.title+'</a></td>';
							appendTxt += '<td>'+newFeature.functionality+'</td>';
							appendTxt += '<td>'+newFeature.benefit+'</td>';
							appendTxt += '<td>'+newFeature.user_role_name+'</td></tr>';
						  $('#featureList > tbody:last-child').append(appendTxt);

						  $('#addFeature').html('Add feature');
							doPost = false;
						}

					}

				});
			}
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
	ProjectResource::run();
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
