<?php

	$ldap_dn = "uid=".$_POST["username"].",dc=pins,dc=co,dc=id";
	$ldap_password = $_POST["password"];

	$ldap_con = ldap_connect("dc01.pins.co.id");
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

	if(@ldap_bind($ldap_con,$ldap_dn,$ldap_password))
		echo "Authenticated";
	else
		echo "Invalid Credential";
?>
