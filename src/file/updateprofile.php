<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['rid']) && !isset($_SESSION['hid'])) {
    redirect('../login.php');
}

if (isset($_SESSION['rid'])) {
    // ---------------- Receiver ----------------
    if (!isset($_POST['update'])) {
        redirect('../rprofile.php');
    }
    $id        = (int)$_SESSION['rid'];
    $rname     = trim($_POST['rname'] ?? '');
    $remail    = trim($_POST['remail'] ?? '');
    $rphone    = trim($_POST['rphone'] ?? '');
    $rcity     = trim($_POST['rcity'] ?? '');
    $bg        = $_POST['bg'] ?? '';
    $rpassword = $_POST['rpassword'] ?? '';

    if ($rname === '' || $rphone === '' || $rcity === '' || !filter_var($remail, FILTER_VALIDATE_EMAIL) || !in_array($bg, $BLOOD_GROUPS, true)) {
        redirect_msg('../rprofile.php', 'error', 'Please fill in all fields correctly.');
    }

    if ($rpassword !== '') {
        if (strlen($rpassword) < 6) {
            redirect_msg('../rprofile.php', 'error', 'Password must have at least 6 characters.');
        }
        $hash = password_hash($rpassword, PASSWORD_DEFAULT);
        $ok = db_run($conn, 'UPDATE receivers SET rname=?, remail=?, rpassword=?, rphone=?, rbg=?, rcity=? WHERE id=?',
                     'ssssssi', [$rname, $remail, $hash, $rphone, $bg, $rcity, $id]);
    } else {
        $ok = db_run($conn, 'UPDATE receivers SET rname=?, remail=?, rphone=?, rbg=?, rcity=? WHERE id=?',
                     'sssssi', [$rname, $remail, $rphone, $bg, $rcity, $id]);
    }

    if ($ok) {
        $_SESSION['rname']  = $rname;
        $_SESSION['remail'] = $remail;
        redirect_msg('../rprofile.php', 'msg', 'Your profile is updated successfully.');
    }
    redirect_msg('../rprofile.php', 'error', 'Could not update profile. This email may already be in use.');
}

// ---------------- Hospital ----------------
if (!isset($_POST['update'])) {
    redirect('../hprofile.php');
}
$id        = (int)$_SESSION['hid'];
$hname     = trim($_POST['hname'] ?? '');
$hemail    = trim($_POST['hemail'] ?? '');
$hphone    = trim($_POST['hphone'] ?? '');
$hcity     = trim($_POST['hcity'] ?? '');
$hpassword = $_POST['hpassword'] ?? '';

if ($hname === '' || $hphone === '' || $hcity === '' || !filter_var($hemail, FILTER_VALIDATE_EMAIL)) {
    redirect_msg('../hprofile.php', 'error', 'Please fill in all fields correctly.');
}

if ($hpassword !== '') {
    if (strlen($hpassword) < 6) {
        redirect_msg('../hprofile.php', 'error', 'Password must have at least 6 characters.');
    }
    $hash = password_hash($hpassword, PASSWORD_DEFAULT);
    $ok = db_run($conn, 'UPDATE hospitals SET hname=?, hemail=?, hpassword=?, hphone=?, hcity=? WHERE id=?',
                 'sssssi', [$hname, $hemail, $hash, $hphone, $hcity, $id]);
} else {
    $ok = db_run($conn, 'UPDATE hospitals SET hname=?, hemail=?, hphone=?, hcity=? WHERE id=?',
                 'ssssi', [$hname, $hemail, $hphone, $hcity, $id]);
}

if ($ok) {
    $_SESSION['hname']  = $hname;
    $_SESSION['hemail'] = $hemail;
    redirect_msg('../hprofile.php', 'msg', 'Your profile is updated successfully.');
}
redirect_msg('../hprofile.php', 'error', 'Could not update profile. This email may already be in use.');
?>
