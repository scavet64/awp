<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_connects.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$u = mysqli_real_escape_string($db_conx, $_POST['u']);
	$p = sha1($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($u == "" || $p == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT user_id, username, password FROM photo_users WHERE username='$u' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE photo_users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Log In</title>
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style/style.css">
<style type="text/css">
#loginform{
	color: white;
	margin-left: auto;
	margin-top:24px;	
	width: 260px;
}
#loginform > div {
	margin-top: 12px;	
	width: 260px;
}

#forgotPassButton {
	color: #5BC0BE;
	
	margin-top: 10px;
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
</head>
<body class="mainBody">
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle">
  <!-- LOGIN FORM -->
  <div class="formWrapper loginFormWrapper">
	  <form id="loginform" onsubmit="return false;">
	  	<h3>Log In</h3>
	    <div>username:</div>
	    <input class="form-control inputForm" type="text" id="username" onfocus="emptyElement('status')" maxlength="32">
	    <div>Password:</div>
	    <input class="form-control inputForm" type="password" id="password" onfocus="emptyElement('status')" maxlength="32">
	    <div><a id="forgotPassButton" href="forgot_pass.php">Forgot Your Password?</a></div>
	    <button class="formButton" id="loginbtn" onclick="loginForm()">Submit</button> 
	    <p id="status"></p>

	  </form>
  </div>
  <!-- LOGIN FORM -->
</div>
</body>
</html>