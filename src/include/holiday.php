<?php
function getHolidayDate($check_in){
	$check_in_date = new DateTime($check_in);

	include 'database.php';
	$result = '';
	$sql_holiday = 'SELECT 
	*
	FROM holidays
	WHERE holiday_date = "' .$check_in. '";';
	
	$view_holiday = mysqli_query($connection, $sql_holiday);
	if(mysqli_num_rows($view_holiday) != 0) {
		$result = 'yes';
	} else {
		if($check_in_date->format('D') == 'Sat' || $check_in_date->format('D') == 'Sun') {
			$result = 'yes';
		} else {
			$result = 'no';
		}
	}
	return $result;
}
?>