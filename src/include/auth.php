<?php
session_start();
include 'environment.php';
include 'database.php';

if (isset($_POST['txt_user']) 
	&& isset($_POST['txt_pass']) 
	&& isset($_POST['txt_loc']) 
	&& $_POST['txt_user'] != '' 
	&& $_POST['txt_pass'] != '' 
	&& $_POST['txt_loc'] != ''){

	// cari timezone
	$key = $location_api_key;
	$url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$_POST['txt_loc'].'&timestamp=1458000000&key='.$key;
	$proc_url = json_decode(file_get_contents($url), true);

	// cari di ldap
	$server = '10.15.179.66';
	$dn = 'ou=people,dc=pins,dc=co,dc=id';
	$con = ldap_connect($server);
	ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
	$user_search = ldap_search($con,$dn,'(|(uid='.$_POST['txt_user'].')(mail='.$_POST['txt_user'].'))');
	$user_get = ldap_get_entries($con, $user_search);
	$user_entry = ldap_first_entry($con, $user_search);
	$user_dn = ldap_get_dn($con, $user_entry);
	if (@ldap_bind($con, $user_dn, $_POST['txt_pass']) === true){
		$_SESSION['isLoggedIn'] = 1;
		$_SESSION['isFromLdap'] = 1;
		$_SESSION['username'] = str_replace('@pins.co.id', '', strtolower($_POST['txt_user']));
		$_SESSION['timezone'] = $proc_url['timeZoneId'];

		// set role
		$sql = 'SELECT users.role AS role, 
			users.name AS name,
			units.name AS unit,
			units.unit_id AS unit_id,
			users.isleader AS isleader
			FROM users 
			JOIN units ON users.unit_id = units.unit_id
			WHERE users.username = ? LIMIT 1';
		$sql_check_role = mysqli_prepare($connection, $sql);
		$sql_check_role->bind_param('s', $_POST['txt_user']);
		$sql_check_role->execute();
		$result = $sql_check_role->get_result();
		
		if($result->num_rows != 0) {
			while($row = $result->fetch_assoc()) {
				$_SESSION['role'] = $row['role']; // set role
				$_SESSION['fullname'] = $row['name'];
				$_SESSION['unit'] = $row['unit'];
				$_SESSION['unit_id'] = $row['unit_id'];
				$_SESSION['isLeader'] = $row['isleader']; //set SL
			}
		} else {
			$_SESSION['role'] = 'user'; // set role
			$_SESSION['fullname'] = $_POST['txt_user'];
			$_SESSION['unit'] = 'Other unit';
			$_SESSION['unit_id'] = '';
			$_SESSION['isLeader'] = 0; //set SL
		}
		$sql_check_role->close();
		header('location: ../view/main/index.php');
	} else {
		$pass = sha1($_POST['txt_pass']);
		$sql ='SELECT users.role AS role, 
		users.username AS username,
		users.password AS password,
		users.name AS name,
		units.name AS unit,
		units.unit_id AS unit_id,
		users.isleader AS isleader
		FROM users 
		JOIN units ON users.unit_id = units.unit_id
		WHERE users.username = ? AND users.password = ?  LIMIT 1';
		$sql_check_user = mysqli_prepare($connection, $sql);
		$sql_check_user->bind_param('ss', $_POST['txt_user'], $pass);
		$sql_check_user->execute();
		$result = $sql_check_user->get_result();
		
		if($result->num_rows != 0) {
			while($row = $result->fetch_assoc()) {
				if(strtolower($_POST['txt_user']) == strtolower($row['username']) && sha1($_POST['txt_pass']) == $row['password']){
					$_SESSION['isLoggedIn'] = 1;
					$_SESSION['isFromLdap'] = 0;
					$_SESSION['username'] = $row['username'];
					$_SESSION['timezone'] = $proc_url['timeZoneId'];
					$_SESSION['role'] = $row['role'];
					$_SESSION['fullname'] = $row['name'];
					$_SESSION['unit'] = $row['unit'];
					$_SESSION['unit_id'] = $row['unit_id'];
					$_SESSION['isLeader'] = $row['isleader']; //set SL
					header('location: ../view/main/index.php');
				}
			}
		} else {
			echo "<script>alert('Username atau password salah');window.location='../login.php';</script>";
		}

		$sql_check_user->close();
	}
} else {
	echo "<script>alert('Username atau password /lokasi kosong');window.location='../login.php';</script>";
}

?>