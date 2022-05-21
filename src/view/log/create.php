<?php
include '../../include/token.php';
include '../../include/session.php';
include '../../include/geolocation.php';

date_default_timezone_set($_SESSION['timezone']);

$tipe = 'ijin';
if (
	isset($_POST['id_user'])
	&& isset($_POST['tipe'])
	&& isset($_POST['masuk'])
	&& isset($_POST['keluar'])
	&& isset($_POST['ket'])
	&& isset($_POST['ip'])
	&& isset($_POST['loc'])
	&& isset($_POST['tgl_mulai'])
	&& isset($_POST['tgl_selesai'])
	&& Token::check($_POST['token'])
) {

	if ($_POST['tipe'] == 'reguler' && isset($_POST['tipe_reguler'])) {
		createLog(
			$_POST['id_user'],
			$_POST['tipe'],
			$_POST['tipe_reguler'],
			$_POST['masuk'],
			$_POST['keluar'],
			$_POST['ket'],
			$_POST['ip'],
			$_POST['loc'],
			getGeolocation($_POST['loc'])
		);
	} else {
		if ($_POST['tipe_ijin'] == 'sakit') {
			$tipe = 'sakit';
		} else {
			$tipe = $_POST['tipe'];
		}
		$array_date = getDatesFromRange($_POST['tgl_mulai'], $_POST['tgl_selesai']);
		foreach ($array_date as $key => $value) {
			createLog(
				$_POST['id_user'],
				$tipe,
				NULL,
				$value . ' 00:00:00',
				$value . ' 00:00:00',
				$_POST['ket'],
				$_POST['ip'],
				$_POST['loc'],
				getGeolocation($_POST['loc'])
			);
		}
	}
}

function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
	$array = array();
	$interval = new DateInterval('P1D');
	$realEnd = new DateTime($end);
	$realEnd->add($interval);
	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);
	foreach ($period as $date) {
		$array[] = $date->format($format);
	}
	return $array;
}

function createLog($id_user, $tipe, $tipe_reguler, $masuk, $keluar, $ket, $ip, $latlon,  $loc)
{
	include '../../include/database.php';
        $id_user = trim($id_user, " ");
	//Get Data from database
	$sql = 'SELECT id FROM logs WHERE username = ? AND DATE(check_in) LIKE "%' . date('Y-m-d') . '%";';
	$sql_check_masuk = mysqli_prepare($connection, $sql);
	$sql_check_masuk->bind_param('s', $id_user);
	$sql_check_masuk->execute();
	$sql_check_masuk->store_result();
	$sum = $sql_check_masuk->num_rows;
	$sql_check_masuk->close();

	//Insert into daabase
	if ($sum == 0) {
		$sql = 'INSERT INTO logs (username, type, work_from, check_in, check_out, more_information, ip_address, lat_lon, location) VALUES (?,?,?,?,?,?,?,?,?)';
		$sql_absen = mysqli_prepare($connection, $sql);
		$sql_absen->bind_param('sssssssss', $id_user, $tipe, $tipe_reguler, $masuk, $keluar, $ket, $ip, $latlon, $loc);
		$result = $sql_absen->execute();

		//for debug if query error
		if (!$result) {
			throw new Exception($sql_absen->error);
		}
		$sql_absen->close();
	}
}
