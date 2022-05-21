
<?php
$user = "bayu.respati";
$passw = "pins2020";
// $dataToEncrypt = "P#ssw0rd_1990";

$con = ldap_connect('ldaps://10.15.179.86:636');

ldap_set_option($con, LDAP_OPT_X_TLS_CACERTDIR, '/home/hanif/LdapSSL');
ldap_set_option($con, LDAP_OPT_X_TLS_CACERTFILE, '/home/hanif/LdapSSL/ca.pem');
// $server = "ldap://10.15.179.86";
// $con = ldap_connect($server, 636);

$bind = ldap_bind($con, "PINS\\Administrator", "AdminIT#123");

// dd($bind);

$dn = "OU=Head Office,DC=pins,DC=co,DC=id";
// dd(1);
// $dataToEncrypt = ADUnicodePwdValue("pins2030");
$user_search = ldap_search($con, $dn, "(|(uid=$user)(cn=$user))");
$user_get = ldap_get_entries($con, $user_search);
$user_entry = ldap_first_entry($con, $user_search);
$user_dn = ldap_get_dn($con, $user_entry);
// dd($user_dn);

// $modifs = [
//     [
//         "attrib"  => "unicodePwd",
//         "modtype" => LDAP_MODIFY_BATCH_REMOVE,
//         "values"  => [iconv("UTF-8", "UTF-16LE", '"' . $passw . '"')],
//     ],
//     [
//         "attrib"  => "unicodePwd",
//         "modtype" => LDAP_MODIFY_BATCH_ADD,
//         "values"  => [iconv("UTF-8", "UTF-16LE", '"' . $dataToEncrypt . '"')],
//     ],
// ];
$newpassword = "pins_jaya";
$newpassword = "\"" . $newpassword . "\"";
$len = strlen($newpassword);
for ($i = 0; $i < $len; $i++) $newpass .= "{$newpassword{$i}}\000";
$entry["unicodePwd"] = $newpass;

$result = ldap_modify($con, $user_dn, $entry);

// $result = ldap_modify_batch($con, $user_dn, $modifs);
// dd($result);
// dd(ldap_errno($con));
var_dump($result, "User Baru");
die();

function dd($value)
{
    var_dump($value);
    die();
};
?>