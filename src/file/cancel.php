<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['rid'])) {
    if (isset($_SESSION['hid'])) {
        redirect_msg('../bloodrequest.php', 'error', 'Hospitals cannot perform this action.');
    }
    redirect('../login.php');
}

$reqid = (int)($_GET['reqid'] ?? 0);
$rid   = (int)$_SESSION['rid'];

// Only the receiver who made the request can cancel it.
$res = db_run($conn, 'DELETE FROM bloodrequest WHERE reqid = ? AND rid = ?', 'ii', [$reqid, $rid]);
if ($res && $res->affected_rows > 0) {
    redirect_msg('../sentrequest.php', 'msg', 'You have cancelled request for the blood.');
}
redirect_msg('../sentrequest.php', 'error', 'Error cancelling request. Please try again.');
?>
