<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["username"])){
	include_once("php_includes/check_login_status.php");
	
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['username']);
	
	if($username == $log_username){
	    //safe to delete
	    $sql = "DELETE FROM photo_users WHERE username='$username'";
        $query = mysqli_query($db_conx, $sql); 
	    
	    header("location: index.php");
	} else {
	    header("location: message.php?msg=Cant_Delete_Another_User_You_Silly");
	}
}
?>