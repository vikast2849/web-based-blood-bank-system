<?php
// Entry point: send visitors straight to their landing page (login for guests).
session_start();
if (isset($_SESSION['hid'])) {
    header('Location: bloodrequest.php');
} elseif (isset($_SESSION['rid'])) {
    header('Location: abs.php');
} else {
    $q = '';
    if (isset($_GET['msg']))   { $q = '?msg=' . urlencode($_GET['msg']); }
    if (isset($_GET['error'])) { $q = '?error=' . urlencode($_GET['error']); }
    header('Location: login.php' . $q);
}
exit;
