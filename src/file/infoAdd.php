<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['hid'])) {
    redirect('../login.php');
}
if (!isset($_POST['add'])) {
    redirect('../bloodinfo.php');
}

$hid = (int)$_SESSION['hid'];
$bg  = $_POST['bg'] ?? '';

if (!in_array($bg, $BLOOD_GROUPS, true)) {
    redirect_msg('../bloodinfo.php', 'error', 'Please select a valid blood group.');
}

$check = db_run($conn, 'SELECT bid FROM bloodinfo WHERE hid = ? AND bg = ?', 'is', [$hid, $bg]);
if ($check && $check->get_result()->num_rows > 0) {
    redirect_msg('../bloodinfo.php', 'error', 'You have already added this blood sample.');
}

if (db_run($conn, 'INSERT INTO bloodinfo (bg, hid) VALUES (?, ?)', 'si', [$bg, $hid])) {
    redirect_msg('../bloodinfo.php', 'msg', 'You have added record successfully.');
}
redirect_msg('../bloodinfo.php', 'error', 'Could not add the blood sample. Please try again.');
?>
