<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['hid'])) {
    if (isset($_SESSION['rid'])) {
        redirect_msg('../sentrequest.php', 'error', 'Receivers cannot perform this action.');
    }
    redirect('../login.php');
}

$reqid  = (int)($_GET['reqid'] ?? 0);
$hid    = (int)$_SESSION['hid'];
$status = 'Rejected';

// Only the hospital that received the request can change its status.
$res = db_run($conn, 'UPDATE bloodrequest SET status = ? WHERE reqid = ? AND hid = ?', 'sii', [$status, $reqid, $hid]);
if ($res && $res->affected_rows > 0) {
    redirect_msg('../bloodrequest.php', 'msg', 'You have rejected the request.');
}
redirect_msg('../bloodrequest.php', 'error', 'Error changing status. Please try again.');
?>
