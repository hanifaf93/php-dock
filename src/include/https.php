<?php
include 'environment.php';
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
  echo "<script>window.location='https://".$root_folder."';</script>";
  exit();
}
?>