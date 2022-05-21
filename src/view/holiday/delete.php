<?php
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['tgl'])) {
	deleteUser($_POST['tgl']);
}

function deleteUser($tgl){
	include '../../include/database.php';
	$sql_delete = 'DELETE FROM holidays WHERE holiday_date = "' .$tgl. '";';
	mysqli_query($connection, $sql_delete);
}
?>