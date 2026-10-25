<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['hid'])) {
    if (isset($_SESSION['rid'])) {
        redirect_msg('../sentrequest.php', 'error', 'Receivers cannot perform this action.');
    }
    redirect('../login.php');
}

$bid = (int)($_GET['bid'] ?? 0);
$hid = (int)$_SESSION['hid'];

// Only the owning hospital can delete its own sample.
$res = db_run($conn, 'DELETE FROM bloodinfo WHERE bid = ? AND hid = ?', 'ii', [$bid, $hid]);
if ($res && $res->affected_rows > 0) {
    redirect_msg('../bloodinfo.php', 'msg', 'You have deleted one blood sample.');
}
redirect_msg('../bloodinfo.php', 'error', 'Error deleting record. Please try again.');
?>
