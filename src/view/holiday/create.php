<?php
include '../../include/token.php';
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['tgl']) && isset($_POST['nama']) && Token::check($_POST['token'])) {
	createUser($_POST['tgl'], $_POST['nama']);
}

function createUser($tgl, $nama){
	include '../../include/database.php';
	// $sql_create = 'INSERT IGNORE INTO holidays (id, holiday_date, name) 
		// VALUES (NULL, "'. $tgl .'", "'. $nama .'");';
	// mysqli_query($connection, $sql_create);


	$sql = 'INSERT INTO holidays (holiday_date, name) VALUES (?, ?)';
	$sql_create = mysqli_prepare($connection, $sql);
	$sql_create->bind_param("ss",$tgl, $nama);
	$result =  $sql_create->execute();

	//for debug if query error
	if(!$result){
		throw new Exception($sql_create->error);
	}

	$sql_create->close();
}
?>