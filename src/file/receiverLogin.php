<?php
session_start();
require 'connection.php';

if (!isset($_POST['rlogin'])) {
    redirect('../login.php');
}

$remail    = trim($_POST['remail'] ?? '');
$rpassword = $_POST['rpassword'] ?? '';

$stmt = db_run($conn, 'SELECT id, rname, remail, rpassword FROM receivers WHERE remail = ?', 's', [$remail]);
$row  = $stmt ? $stmt->get_result()->fetch_assoc() : null;

if ($row && password_ok($rpassword, $row['rpassword'], $upgrade)) {
    if ($upgrade) {
        $hash = password_hash($rpassword, PASSWORD_DEFAULT);
        db_run($conn, 'UPDATE receivers SET rpassword = ? WHERE id = ?', 'si', [$hash, $row['id']]);
    }
    session_regenerate_id(true);
    $_SESSION['remail'] = $row['remail'];
    $_SESSION['rname']  = $row['rname'];
    $_SESSION['rid']    = (int)$row['id'];
    redirect_msg('../abs.php', 'msg', $row['rname'] . ' have logged in.');
}

redirect_msg('../login.php', 'error', 'Wrong email or password. Please try again.');
?>
