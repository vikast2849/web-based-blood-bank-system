<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['rid'])) {
    redirect('../login.php');
}
if (!isset($_POST['request'])) {
    redirect('../abs.php');
}

$hid = (int)($_POST['hid'] ?? 0);
$rid = (int)$_SESSION['rid'];
$bg  = $_POST['bg'] ?? '';

// The hospital must actually hold this blood group.
$avail = db_run($conn, 'SELECT bid FROM bloodinfo WHERE hid = ? AND bg = ?', 'is', [$hid, $bg]);
if (!$avail || $avail->get_result()->num_rows === 0) {
    redirect_msg('../abs.php', 'error', 'That blood sample is not available at this hospital.');
}

$check = db_run($conn, 'SELECT reqid FROM bloodrequest WHERE hid = ? AND rid = ?', 'ii', [$hid, $rid]);
if ($check && $check->get_result()->num_rows > 0) {
    redirect_msg('../abs.php', 'error', 'You have already requested for blood sample from this Hospital.');
}

if (db_run($conn, 'INSERT INTO bloodrequest (bg, hid, rid) VALUES (?, ?, ?)', 'sii', [$bg, $hid, $rid])) {
    redirect_msg('../sentrequest.php', 'msg', 'You have requested for blood group ' . $bg . '. Our team will contact you soon.');
}
redirect_msg('../abs.php', 'error', 'Could not send the request. Please try again.');
?>
