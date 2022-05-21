<?php
include '../../include/session.php';

if($_SESSION['role'] != 'admin'){
	header('location: ../../index.php');
	exit();
}

if (isset($_POST['id']) 
	&& isset($_POST['type'])
	&& isset($_POST['work_from'])
	&& isset($_POST['check_in'])
	&& isset($_POST['check_out'])
	&& isset($_POST['more_information'])) {

	if($_POST['type'] != 'reguler'){
		$_POST['check_out'] = $_POST['check_in'];
		$_POST['work_from'] = null;
	}

	updateReport($_POST['id'], 
		$_POST['type'], 
		$_POST['work_from'], 
		$_POST['check_in'],
		$_POST['check_out'],
		$_POST['more_information']);
}

function updateReport($id, $type, $work_from, $check_in, $check_out, $more_information){
	include '../../include/database.php';
	$sql_update = "UPDATE logs SET type='".$type."', work_from='".$work_from."', check_in='".$check_in."', check_out='".$check_out."', more_information='".$more_information."' WHERE id='".$id."'";
	mysqli_query($connection, $sql_update);
	echo "<script>alert('Report id ".$id." berhasil diperbarui');window.history.back();</script>";
}
?>