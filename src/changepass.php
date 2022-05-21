<?php

/**
 *	 LDAP PHP Change Password Webpage (modified for Active Directory)
 *	 @author:	 Matt Rude <http://mattrude.com>
 *   @author:	 Isaiah Olson <http://www.olsontech.io/>
 *	 @website:	http://technology.mattrude.com/2010/11/ldap-php-change-password-webpage/
 *
 *
 *							GNU GENERAL PUBLIC LICENSE
 *								 Version 2, June 1991
 *
 * Copyright (C) 1989, 1991 Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Since we're dealing with AD credentials, this is a good idea
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

$message = array();
$message_css = "";

function ADUnicodePwdValue($plain_txt_value)
{
    // This requires recode to be installed on your webserver
    // If this isn't possible, look up alternate ways of formatting unicodePwd in PHP
    return str_replace("\n", "", shell_exec("echo -n '\"" . $plain_txt_value . "\"' | recode latin1..utf-16le/base64"));
}

// To prevent LDAP injection in username field
function ldapspecialchars($string)
{
    $sanitized = array(
        '\\' => '\5c',
        '*' => '\2a',
        '(' => '\28',
        ')' => '\29',
        "\x00" => '\00'
    );

    return str_replace(array_keys($sanitized), array_values($sanitized), $string);
}

function changePassword($user, $oldPassword, $newPassword, $newPasswordCnf)
{
    global $message;
    global $message_css;

    $domain = 'pins.co.id';      // DNS domain name (do not enter specific host here)
    $server = "ldaps://" . $domain; // MUST be LDAPS to change AD password
    $basedn = "DC=example,DC=com";
    $dn = "OU=Example Users," . $basedn; // Fill this in if you keep your users in a specific OU

    error_reporting(0);
    ldap_connect($server);
    $con = ldap_connect($server);
    ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

    // Bind first because AD has limited anon access
    if (ldap_bind($con, $user . '@' . $domain, $oldPassword) === false) {
        $message[] = "Error E101 - Current Username or Password is wrong.";
        return false;
    }

    // bind anon and find user by sAMAccountName
    $user_search = ldap_search($con, $dn, "(&(objectClass=User)(sAMAccountName=$user))", array("sAMAccountName", "mail", "givenName", "*"));
    $user_get = ldap_get_entries($con, $user_search);
    $user_entry = ldap_first_entry($con, $user_search);
    $user_dn = ldap_get_dn($con, $user_entry);
    $user_id = $user_get[0]["samaccountname"][0];
    $user_search_arry = array("*", "ou", "sAMAccountName", "mail", "passwordRetryCount", "passwordhistory");
    $user_search_filter = "(&(objectClass=User)(sAMAccountName=$user_id))";
    $user_search_opt = ldap_search($con, $user_dn, $user_search_filter, $user_search_arry);
    $user_get_opt = ldap_get_entries($con, $user_search_opt);
    $passwordRetryCount = $user_get_opt[0]["passwordRetryCount"][0];
    $passwordhistory = $user_get_opt[0]["passwordhistory"][0];

    // $message[] = "Username: " . $user_id;
    // $message[] = "DN: " . $user_dn;
    // $message[] = "Current Pass: " . $oldPassword;
    // $message[] = "New Pass: " . $newPassword;

    /* Start the testing */
    if ($passwordRetryCount == 3) {
        $message[] = "Error E101 - Your Account is Locked Out!!!";
        return false;
    }

    if ($newPassword != $newPasswordCnf) {
        $message[] = "Error E102 - Your New passwords do not match!";
        return false;
    }

    $history_arr = ldap_get_values($con, $user_dn, "passwordhistory");
    if ($history_arr) {
        $message[] = "Error E102 - Your new password matches one of the last 10 passwords that you used, you MUST come up with a new password.";
        return false;
    }
    if (strlen($newPassword) < 8) {
        $message[] = "Error E103 - Your new password is too short.<br/>Your password must be at least 8 characters long.";
        return false;
    }
    if (!preg_match("/[0-9]/", $newPassword)) {
        $message[] = "Error E104 - Your new password must contain at least one number.";
        return false;
    }
    if (!preg_match("/[a-zA-Z]/", $newPassword)) {
        $message[] = "Error E105 - Your new password must contain at least one letter.";
        return false;
    }
    if (!preg_match("/[A-Z]/", $newPassword)) {
        $message[] = "Error E106 - Your new password must contain at least one uppercase letter.";
        return false;
    }
    if (!preg_match("/[a-z]/", $newPassword)) {
        $message[] = "Error E107 - Your new password must contain at least one lowercase letter.";
        return false;
    }
    if (!$user_get) {
        $message[] = "Error E200 - Unable to connect to server, you may not change your password at this time, sorry.";
        return false;
    }

    $auth_entry = ldap_first_entry($con, $user_search);
    $mail_addresses = ldap_get_values($con, $auth_entry, "mail");
    $given_names = ldap_get_values($con, $auth_entry, "givenName");
    $password_history = ldap_get_values($con, $auth_entry, "passwordhistory");
    $mail_address = $mail_addresses[0];
    $first_name = $given_names[0];

    $newpw64 = ADUnicodePwdValue($newPassword);
    $oldpw64 = ADUnicodePwdValue($oldPassword);

    // Must use LDIF with ldapmodify because AD requires that password changes take place in a single modify operation
    $ldif = <<<EOT
dn: $user_dn
changetype: modify
delete: unicodePwd
unicodePwd:: $oldpw64
-
add: unicodePwd
unicodePwd:: $newpw64
-
EOT;

    // Build LDAP command string
    $cmd = sprintf("/usr/bin/ldapmodify -H %s -D '%s' -x -w %s", $server, $user_dn, $oldPassword);
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("pipe", "w") // stderr is a file to write to
    );

    $process = proc_open($cmd, $descriptorspec, $pipes);

    if (is_resource($process)) {
        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout

        fwrite($pipes[0], "$ldif\n");
        fclose($pipes[0]);

        $proc_stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $proc_stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // It is important that you close any pipes before calling
        // proc_close in order to avoid a deadlock
        $return_value = proc_close($process);

        if ($return_value > 0) {
            $message[] = "An error occurred while changing the password! Please provide the following info to your system administrator:";
            $message[] = "STDOUT: $proc_stdout";
            $message[] = "STDERR: $proc_stderr";
            $message[] = "EXIT: $return_value";
        } else {
            $message_css = "yes";
            $message[] = "The password for $user_id has been changed.<br/>Your new password is now fully active.";

            // Could have mail code here
        }
    }

    ldap_close($con);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Password Change Page</title>
    <style type="text/css">
        body {
            font-family: Verdana, Arial, Courier New;
            font-size: 0.7em;
        }

        th {
            text-align: right;
            padding: 0.8em;
        }

        #container {
            text-align: center;
            width: 500px;
            margin: 5% auto;
        }

        .msg_yes {
            margin: 0 auto;
            text-align: center;
            color: green;
            background: #D4EAD4;
            border: 1px solid green;
            border-radius: 10px;
            margin: 2px;
        }

        .msg_no {
            margin: 0 auto;
            text-align: center;
            color: red;
            background: #FFF0F0;
            border: 1px solid red;
            border-radius: 10px;
            margin: 2px;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <div id="container">
        <h2>Password Change Page</h2>
        <p>Your new password must be 8 characters long or longer and have at least:<br />
            one capital letter, one lowercase letter, &amp; one number.<br />
            You must use a new password, your current password<br />can not be the same as your new password.</p>
        <?php
        if (isset($_POST["submitted"])) {
            changePassword(ldapspecialchars($_POST['username']), $_POST['oldPassword'], $_POST['newPassword1'], $_POST['newPassword2']);
            global $message_css;
            if ($message_css == "yes") {
        ?><div class="msg_yes"><?php
                            } else {
                                ?><div class="msg_no"><?php
                                                        $message[] = "Your password was not changed.";
                                                    }
                                                    foreach ($message as $one) {
                                                        echo "<p>$one</p>";
                                                    }
                                                        ?></div><?php
                                                                        } ?>
                <form action="<?php print $_SERVER['PHP_SELF']; ?>" name="passwordChange" method="post">
                    <table style="width: 400px; margin: 0 auto;">
                        <tr>
                            <th>Username:</th>
                            <td><input name="username" type="text" size="20px" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th>Current password:</th>
                            <td><input name="oldPassword" size="20px" type="password" /></td>
                        </tr>
                        <tr>
                            <th>New password:</th>
                            <td><input name="newPassword1" size="20px" type="password" /></td>
                        </tr>
                        <tr>
                            <th>New password (again):</th>
                            <td><input name="newPassword2" size="20px" type="password" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <input name="submitted" type="submit" value="Change Password" />
                                <button onclick="$('frm').action='changepassword.php';$('frm').submit();">Cancel</button>
                            </td>
                        </tr>
                    </table>
                </form>
                </div>
</body>

</html>
