<?php
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['unit_id'])) {
	deleteUser($_POST['unit_id']);
}

function deleteUser($unit_id){
	include '../../include/database.php';
	$sql_delete = 'DELETE FROM units WHERE unit_id = "' .$unit_id. '";';
	mysqli_query($connection, $sql_delete);
}
?>