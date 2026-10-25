<?php
session_start();
require 'connection.php';

if (!isset($_POST['hlogin'])) {
    redirect('../login.php');
}

$hemail    = trim($_POST['hemail'] ?? '');
$hpassword = $_POST['hpassword'] ?? '';

$stmt = db_run($conn, 'SELECT id, hname, hemail, hpassword FROM hospitals WHERE hemail = ?', 's', [$hemail]);
$row  = $stmt ? $stmt->get_result()->fetch_assoc() : null;

if ($row && password_ok($hpassword, $row['hpassword'], $upgrade)) {
    if ($upgrade) {
        $hash = password_hash($hpassword, PASSWORD_DEFAULT);
        db_run($conn, 'UPDATE hospitals SET hpassword = ? WHERE id = ?', 'si', [$hash, $row['id']]);
    }
    session_regenerate_id(true);
    $_SESSION['hemail'] = $row['hemail'];
    $_SESSION['hname']  = $row['hname'];
    $_SESSION['hid']    = (int)$row['id'];
    redirect_msg('../bloodrequest.php', 'msg', $row['hname'] . ' have logged in.');
}

redirect_msg('../login.php', 'error', 'Wrong email or password. Please try again.');
?>
