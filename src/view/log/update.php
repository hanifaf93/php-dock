<?php
include '../../include/session.php';
include '../../include/geolocation.php';

date_default_timezone_set($_SESSION['timezone']);

if (isset($_POST['id_user']) 
	&& isset($_POST['masuk']) 
	&& isset($_POST['keluar'])
	&& isset($_POST['loc'])) {

	updateLogKeluar($_POST['id_user'], 
		$_POST['masuk'], 
		$_POST['keluar'],
		$_POST['loc'],
		getGeolocation($_POST['loc']));
}

function updateLogKeluar($id_user, $masuk, $keluar, $latlon, $loc){
	include '../../include/database.php';
	
	$sql = 'UPDATE logs SET check_out = ?, lat_lon_check_out = ?, location_check_out = ? WHERE username = ? AND check_in = ?';
	$sql_keluar = mysqli_prepare($connection, $sql);
	$sql_keluar->bind_param('sssss', $keluar, $latlon, $loc, $id_user, $masuk);
	$result = $sql_keluar->execute();
	
	//for debug if query error
	if(!$result){
		throw new Exception($sql_keluar->error);
	}
	$sql_keluar->close();
}
?>