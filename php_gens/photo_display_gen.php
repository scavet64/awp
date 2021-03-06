<?php
include_once("php_includes/check_login_status.php");
include_once("comment_controller.php");
include_once("php_includes/date_conversion.php");
include_once("php_parsers/user_tagging_parser.php");
include_once("php_parsers/hashtag_parser.php");
?>

<?php
function generatePhotoDisplay($row, $db_conx, $log_username){
    	$id = $row["photo_id"];
		$filename = $row["uploadname"];
		$description = $row["caption"];
		$uploaddate = $row["uploaddate"];
		$filelocation = $row["filelocation"];
		$photoOwner = $row["username"];
		
        $displayDate = convertDate($uploaddate, 'America/New_York');
        
        $description = parseTextForUsername($description);
        $description = parseTextForHashtag($description);
        $matches = getHashtagArray($description);
		
		$containerString = 
		'<div class="photoContainer">
    		<div style="margin:auto;">
        		<img src="'.$filelocation.'" id="photoID'.$id.'" class="displayImages" ></img>
        	</div>
            <div class="imageInfo">
                <p id="ownerTag'.$id.'" class="pictureOwner">by: 
                	<a class="linkToUser" href=user.php?u='.$photoOwner.'>'.$photoOwner.'</a>
                </p>
                <p id="uploadDate'.$id.'" class="uploadDate">Uploaded on: '.$displayDate.'</p>
            </div>
            <p id="description'.$id.'" class="descriptionText">'.$description.'</p>
            <div id=commentsForPhoto'.$id.'>
                '.genComments($id, $db_conx, $photoOwner, $log_username).'
            </div>
            <div>
                <input class="form-control commentBox" id="inputOnPhoto'.$id.'" type="text" maxlength="250">
                <button class="formButton commentButton" type="button" onclick="postComment('.$id.')" class="">Comment</button>
            </div>
        </div>';
        return $containerString;
}
?>

<?php
function genComments($id, $db_conx, $photoOwner, $log_username) {
    $commentArrayOfDivs = "";
	        
	$sqlComment = "SELECT comment_text, username, comment_date, comment_id FROM photo_comments
               JOIN photo_users USING(user_id)
               WHERE photo_id =".$id."
               UNION
               SELECT comment_text, '[deleted]', comment_date, comment_id FROM photo_comments
               WHERE user_id is NULL AND photo_id =".$id."
               ORDER BY comment_date DESC LIMIT 10";
               
	$queryComments = mysqli_query($db_conx, $sqlComment);
    
    while ($rowComment = mysqli_fetch_array($queryComments, MYSQLI_ASSOC)) {
		$commentText = $rowComment["comment_text"];
		$commenter = $rowComment["username"];
		$date = $rowComment["comment_date"];
		$comment_id = $rowComment["comment_id"];
    
        $displayDate = convertDate($date, 'America/New_York');
        
        $commentText = parseTextForUsername($commentText);
        $commentText = parseTextForHashtag($commentText);
    
        if($commenter == $log_username || $log_username == $photoOwner){
            $canDelete = True;
        } else {
            $canDelete = False;
        }
        
        if($commenter == null){
            $commenter = '[DELETED]';
        }
    
        $commentArrayOfDivs = genComment($commenter, $commentText, $displayDate, $canDelete, $comment_id).$commentArrayOfDivs;
    }
    
    return $commentArrayOfDivs;
}
?>