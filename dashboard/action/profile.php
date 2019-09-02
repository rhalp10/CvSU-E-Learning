<?php
require_once('class.function.php');
$profile = new ACFunction(); 
session_start();
if (isset($_POST['action'])) 
{

	$output = array();
	if($_POST['action'] == "change_password")
	{
		$user_ID = $_SESSION['user_ID'];
		$update_password_old = $_REQUEST["update_password_old"];
		$update_password_new = $_REQUEST["update_password_new"];
		$update_password_newconfirm = $_REQUEST["update_password_newconfirm"];
		

		if($update_password_new === $update_password_newconfirm)
		{
			$stmt = $profile->runQuery("SELECT * FROM `user_account` WHERE user_ID =  :user_ID 
			LIMIT 1");
			$stmt->execute(array(':user_ID'=>$user_ID));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 1)
			{
				if(password_verify($update_password_old, $userRow['user_Pass']))
				{
					 $new_password = password_hash($update_password_newconfirm, PASSWORD_DEFAULT);
					
					$stmt = $profile->runQuery("UPDATE `user_account` SET `user_Pass` = :user_Pass WHERE `user_account`.`user_ID` = :user_ID");
					$stmt->bindparam(":user_ID", $user_ID);	
					$stmt->bindparam(":user_Pass", $new_password);	
					$stmt->execute();	
					$output['success'] = "Password successfully change";
					
				
				}
				else
				{
					$output['error'] = "Old Password not match";
					
					
				}

			}
		}
		else
		{
			$output['error'] = "Password not match";
		}
		
	}
	//CHANGE PROFILE PICTURE
	else if($_POST['action'] == "change_picture")
	{
		$user_ID = $_SESSION['user_ID'];
		if (isset($_FILES['change_profile']['tmp_name'])) 
		{
			$new_img = addslashes(file_get_contents($_FILES['change_profile']['tmp_name']));

			// print_r($_FILES);
			$stmt = $profile->runQuery("UPDATE `user_account` SET `user_Img` = '$new_img' WHERE `user_ID` = $user_ID ;");

			$result = $stmt->execute();
			if(!empty($result))
			{
			    $output['success'] = "Profile Image Succesfully";
			    $profile->getUserPic($user_ID);
			    
			} else {
			    $output['error'] =  "Error updating record: " . mysqli_error($conn);
			}
		}
		else{
			$new_img = '';
		}

	}
	else{
		
		
	}
	
	
	echo json_encode($output);
	
}

?>