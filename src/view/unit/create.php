<?php
include '../../include/token.php';
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['unit_id']) && isset($_POST['name']) && Token::check($_POST['token'])) {
	createUser($_POST['unit_id'], $_POST['name']);
}

function createUser($unit_id, $name){
	include '../../include/database.php';

	$sql = 'INSERT INTO units (unit_id, name) VALUES (?, ?)';
	$sql_create = mysqli_prepare($connection, $sql);
	$sql_create->bind_param("is",$unit_id, $name);
	$result =  $sql_create->execute();

	//for debug if query error
	if(!$result){
		throw new Exception($sql_create->error);
	}

	$sql_create->close();
}
?>