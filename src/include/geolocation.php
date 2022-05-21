<?php
function getGeolocation($lat_lon){
	include 'environment.php';
	$key = $location_api_key;
	
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat_lon.'&sensor=true&key='.$key;
	$proc_url = json_decode(file_get_contents($url), true);
	$loc = $proc_url['results']['0']['address_components']['1']['long_name'].', '.$proc_url['results']['0']['address_components']['0']['long_name'].', '.$proc_url['results']['0']['address_components']['4']['long_name'].', '.$proc_url['results']['0']['address_components']['5']['long_name'];
	return $loc;
}

function getDistance($lat_lon1, $lat_lon2) {

  $loc1 = str_replace(' ', '', explode (',', $lat_lon1));
  $lat1 = $loc1[0];
  $lon1 = $loc1[1];
  $loc2 = str_replace(' ', '', explode (',', $lat_lon2));
  $lat2 = $loc2[0];
  $lon2 = $loc2[1];
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $kilometer = $dist * 60 * 1.1515 * 1.609344;
  if($kilometer <= 0.200){
    $check = 'YES';
  } else {
    $check = 'NO';
  }
  return $check;
}
?>