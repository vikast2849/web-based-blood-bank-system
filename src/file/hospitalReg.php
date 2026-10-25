<?php
require 'connection.php';

if (!isset($_POST['hregister'])) {
    redirect('../register.php');
}

$hname     = trim($_POST['hname'] ?? '');
$hemail    = trim($_POST['hemail'] ?? '');
$hpassword = $_POST['hpassword'] ?? '';
$hphone    = trim($_POST['hphone'] ?? '');
$hcity     = trim($_POST['hcity'] ?? '');

if ($hname === '' || $hcity === '' || $hphone === '' || strlen($hpassword) < 6 || !filter_var($hemail, FILTER_VALIDATE_EMAIL)) {
    redirect_msg('../register.php', 'error', 'Please fill in all fields correctly (password: minimum 6 characters).');
}

$check = db_run($conn, 'SELECT id FROM hospitals WHERE hemail = ?', 's', [$hemail]);
if ($check && $check->get_result()->num_rows > 0) {
    redirect_msg('../register.php', 'error', 'Email Already exists. Please try another Email.');
}

$hash = password_hash($hpassword, PASSWORD_DEFAULT);
if (db_run($conn, 'INSERT INTO hospitals (hname, hemail, hpassword, hphone, hcity) VALUES (?, ?, ?, ?, ?)', 'sssss', [$hname, $hemail, $hash, $hphone, $hcity])) {
    redirect_msg('../login.php', 'msg', 'You have successfully registered. Please, login to continue.');
}
redirect_msg('../register.php', 'error', 'Registration failed. Please try again.');
?>
