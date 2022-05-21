<?php
include 'environment.php';
include 'https.php';
session_start();
if($_SESSION['isLoggedIn'] != '1'){
  session_destroy();
  header('location: https://'.$root_folder.'/login.php');
	exit();
}
?>
