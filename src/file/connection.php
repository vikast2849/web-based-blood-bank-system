<?php
// ---------------------------------------------------------------
// Database configuration.
// Defaults suit XAMPP/WAMP (root, empty password). Override with
// environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME) when needed,
// e.g. in Docker.
// ---------------------------------------------------------------
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbname     = getenv('DB_NAME') ?: 'bloodbank';

// Classic error handling (return false) on every PHP version.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Could not connect to MySQL: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset('utf8mb4');

/** Valid blood groups. */
$BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

/** Redirect and stop. */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/** Redirect with a flash message (type = 'msg' or 'error'). */
function redirect_msg($page, $type, $text) {
    redirect($page . '?' . $type . '=' . urlencode($text));
}

/**
 * Run a prepared statement. Returns the executed mysqli_stmt, or false on failure.
 * For SELECT use ->get_result() on the return value.
 */
function db_run($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        return false;
    }
    return $stmt;
}

/**
 * Check a password against the stored value.
 * Supports bcrypt hashes and legacy plain-text passwords (the sample data).
 * $needsUpgrade is set to true when a plain-text password matched, so the
 * caller can store a hash instead.
 */
function password_ok($input, $stored, &$needsUpgrade) {
    $needsUpgrade = false;
    if (password_verify($input, $stored)) {
        if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
            $needsUpgrade = true;
        }
        return true;
    }
    if (hash_equals($stored, $input)) {
        $needsUpgrade = true;
        return true;
    }
    return false;
}
?>
