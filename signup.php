<?php
include_once("php_includes/check_login_status.php");
//session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=already signed up and logged in");
    exit();
}
?>
<?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_connects.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT user_id FROM photo_users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query); //either 0 or 1. 1 = failed because we found a record with that username
    if (strlen($username) < 3 || strlen($username) > 32) {
	    echo '<strong style="color:#F00;">3 - 32 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_connects.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT user_id FROM photo_users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT user_id FROM photo_users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		$p_hash = sha1($p);
		// $cryptpass = crypt($p); //always will be 34 characters
		// include_once ("php_includes/randStrGen.php");
		// $p_hash = randStrGen(20)."$cryptpass".randStrGen(20);   //surround our cryptic password with random characters
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO photo_users (username, email, password, ip, joindate, lastlogin, activated)       
		        VALUES('$u','$e','$p_hash','$ip',now(),now(),'1')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
// 		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
// 		if (!file_exists("user/$u")) {
// 			mkdir("user/$u", 0755);
// 		}
		// Email the user their activation link
		$websiteURL = "scavettapps.com/awp/";
		$mySiteName = "Super Cool Image Site";
		$UrlEncodedEmail = urlencode($e);

		$activationURL = $websiteURL.'activation.php?id='.$uid.'&u='.$u.'&e='.$UrlEncodedEmail.'&p='.$p_hash;
		
		$to = "$e";							 
		$from = "noreply@scavettapps.com";
		$subject = $mySiteName.' Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'.$mySiteName.' Message</title></head><body style="margin:0px; font-family:Tahoma, 
		Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="'.$websiteURL.'">
		<img src="'.$websiteURL.'/images/logo.jpg" width="36" height="30" alt="'.$mySiteName.'" style="border:none; float:left;"></a>'.$mySiteName.' Account Activation</div>
		<div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br />
		<a href="'.$activationURL.'">Click here to activate your account now</a><br />
		<br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
    
		if(mail($to, $subject, $message, $headers)){
			echo "signup_success";
		} else {
			echo "Email failed: Activating for testing reasons";
			header($websiteURL.'/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash);
		}
		exit();
	}
	exit();
	
	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sign Up</title>
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style/style.css">
<style type="text/css">
#signupform{
	color: white;
	margin-top:24px;	
}
#signupform > div {
	margin-top: 12px;	
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>

/* function addEvents(){
	_("elemID").addEventListener("click", func, false);
}
window.onload = addEvents; */
</script>
</head>
<body class="mainBody">
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle">
	<div class="formWrapper signupFormWrapper">
		<form name="signupform" id="signupform" onsubmit="return false;">
			<h3>Sign Up</h3>
			<div>Username: </div>
			<input id="username" class="form-control inputForm" type="text" onblur="checkusername()" onkeyup="restrict('username')" maxlength="32">
			<span id="unamestatus" class="unamestatus"></span>
			<div>Email Address:</div>
			<input id="email" class="form-control inputForm" type="text" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="255">
			<div>Create Password:</div>
			<input id="pass1" class="form-control inputForm" type="password" onfocus="emptyElement('status')" maxlength="32">
			<div>Confirm Password:</div>
			<input id="pass2" class="form-control inputForm" type="password" onfocus="emptyElement('status')" maxlength="32">
			<div>
				<a href="#" onclick="return false" onmousedown="openTerms()">
					View the Terms Of Use
				</a>
			</div>
			<div id="terms" class="terms" style="display:none;">
				<h4>Super Cool Terms Of Use</h4>
				<p>1. Play nice here.</p>
				<p>2. Take a bath before you visit.</p>
				<p>3. Brush your teeth before bed.</p>
				<p>4. Only upload PG-13 content.</p>
			</div>
			<button class="formButton" id="signupbtn" onclick="signup()">Create Account</button>
			<span id="status"></span>
		</form>
	</div>
</div>
</body>
</html>