<?php
require 'connection.php';

if (!isset($_POST['rregister'])) {
    redirect('../register.php');
}

$rname     = trim($_POST['rname'] ?? '');
$remail    = trim($_POST['remail'] ?? '');
$rpassword = $_POST['rpassword'] ?? '';
$rphone    = trim($_POST['rphone'] ?? '');
$rcity     = trim($_POST['rcity'] ?? '');
$rbg       = $_POST['rbg'] ?? '';

if ($rname === '' || $rcity === '' || $rphone === '' || strlen($rpassword) < 6
    || !filter_var($remail, FILTER_VALIDATE_EMAIL) || !in_array($rbg, $BLOOD_GROUPS, true)) {
    redirect_msg('../register.php', 'error', 'Please fill in all fields correctly (password: minimum 6 characters).');
}

$check = db_run($conn, 'SELECT id FROM receivers WHERE remail = ?', 's', [$remail]);
if ($check && $check->get_result()->num_rows > 0) {
    redirect_msg('../register.php', 'error', 'Email Already exists. Please try another Email.');
}

$hash = password_hash($rpassword, PASSWORD_DEFAULT);
if (db_run($conn, 'INSERT INTO receivers (rname, remail, rpassword, rphone, rcity, rbg) VALUES (?, ?, ?, ?, ?, ?)', 'ssssss', [$rname, $remail, $hash, $rphone, $rcity, $rbg])) {
    redirect_msg('../login.php', 'msg', 'You have successfully registered. Please, login to continue.');
}
redirect_msg('../register.php', 'error', 'Registration failed. Please try again.');
?>
