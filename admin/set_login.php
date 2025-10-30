<?php 
	session_start();

		$_SESSION['crm_is_login'] = 1;
		$_SESSION['crm_user_id'] = $_POST['id'];
		$_SESSION['crm_user_role'] = $_POST['role_id'];

		
		$_SESSION['crm_user_name'] = $_POST['name'];
		$_SESSION['crm_user_email'] = $_POST['email'];
		$_SESSION['crm_user_phone'] = $_POST['phone'];
		
		$_SESSION['crm_profile_img'] = $_POST['profile_img'];

	
	// echo json_encode($_POST);
	// return true;
    echo json_encode(['status' => 'success']);
return;
 ?>