<?php
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['username'])) {
	deleteUser($_POST['username']);
}

function deleteUser($username){
	include '../../include/database.php';
	$sql_delete = 'DELETE FROM users WHERE username = "' .$username. '";';
	mysqli_query($connection, $sql_delete);
}
?>