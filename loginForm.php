<?php

function display_form() {
  // Use global variable $messages
  global $messages;
  if ($messages == null){
	  $messages = array();
  }
  // Get input value if any
  $name = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["name"];
  $pass = ($_SERVER["REQUEST_METHOD"] == "GET") ? "" : $_POST["password"];
  
  if (!array_key_exists("name", $messages)) {
    $messages["name"] = "";
  }
  else {
    $messages["name"] = "<span style='color: red;'>$messages[name]</span>";
  }
  
  if (!array_key_exists("password", $messages)) {
    $messages["password"] = "";
  }
  else {
    $messages["password"] = "<span style='color: red;'>$messages[password]</span>";
  }
  
  if (!array_key_exists("loginResponse", $messages)) {
    $messages["loginResponse"] = "";
  }
  else {
    $messages["loginResponse"] = "<span style='color: red;'>$messages[loginResponse]</span>";
  }
  
  //print $messages["loginResponse"];	
  // Print the form
  print <<<END_FORM
  <form class="form-signin" id="loginForm" method="POST">
	$messages[loginResponse]
	<label class="sr-only">Username</label>
    <input class="form-control" id="name" type="text" name="name" value="$name" placeholder="Last name or Email"/>$messages[name]
	
	
	<label class="sr-only">Password</label>
    <input class="form-control" id="password" type="password" name="password" placeholder="password"/> $messages[password]
	
	
    <button id="login" class="btn btn-lg btn-primary btn-block">Log In</button>
	
  </form>
END_FORM;
}
	
	if (array_key_exists("person", $_SESSION)) {
		$currentID = $_SESSION["person"];
		print "<form id='logoutForm' method='POST' style='float: right;'><button id='logout' type='Submit' name='action' class='btn btn-default' value='logout'>Log out</button></form>";
	}
	else{
		display_form();
	}

?>