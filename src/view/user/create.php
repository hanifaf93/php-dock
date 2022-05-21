<?php
include '../../include/token.php';
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['txt_username']) 
	&& isset($_POST['txt_name'])
	&& isset($_POST['opt_unit_id'])
	&& isset($_POST['opt_role'])
	&& isset($_POST['txt_password'])
	&& Token::check($_POST['token'])) {

	createUser(strtolower($_POST['txt_username']), 
		strtoupper($_POST['txt_name']),
		$_POST['opt_unit_id'],
		$_POST['opt_role'],
		sha1($_POST['txt_password'])
	);
}

function createUser($txt_username, $txt_name, $opt_unit_id, $opt_role, $txt_password){
	include '../../include/database.php';

	$sql = 'INSERT INTO users (username, name, unit_id, role, password) VALUES ( ?, ?, ?, ?, ?)';
	$sql_create = mysqli_prepare($connection, $sql);
	$sql_create->bind_param("ssiss",$txt_username, $txt_name, $opt_unit_id, $opt_role,  $txt_password);
	$result =  $sql_create->execute();

	//for debug if query error
	if(!$result){
		throw new Exception($sql_create->error);
	}

	$sql_create->close();
}
?>