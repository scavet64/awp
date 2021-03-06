<?php
include_once("php_includes/check_login_status.php");
include_once("comment_controller.php");
include_once("php_includes/date_conversion.php");
include_once("php_parsers/user_tagging_parser.php");
include_once("php_parsers/hashtag_parser.php");
include_once("php_gens/photo_display_gen.php");
?>

<?php
if(isset($_GET["offset"])){
	$offset = preg_replace('#[^0-9]#i', '', $_GET['offset']);
	
	$containerString = "";
	$sql = "SELECT * FROM photo_files 
	        JOIN photo_users USING(user_id)
	        ORDER BY uploaddate DESC LIMIT 10 OFFSET ".$offset;
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $containerString .= generatePhotoDisplay($row, $db_conx, $log_username);
    }
	mysqli_close($db_conx);
	echo $containerString;
	exit();
}

?>

<?php 
	$containerString = "";
	$sql = "SELECT * FROM photo_files 
	        JOIN photo_users USING(user_id)
	        ORDER BY uploaddate DESC LIMIT 10";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $containerString .= generatePhotoDisplay($row, $db_conx, $log_username);
    }
	mysqli_close($db_conx);
	echo $containerString;
	exit();
?>