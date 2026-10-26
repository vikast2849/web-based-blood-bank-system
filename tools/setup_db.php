<?php
// Creates the database and imports database/bloodbank.sql if it is not there yet.
// Usage: php tools/setup_db.php
mysqli_report(MYSQLI_REPORT_OFF);

$cfgFile = __DIR__ . '/../src/file/config.local.php';
$local = file_exists($cfgFile) ? include $cfgFile : [];
$host = $local['host'] ?? (getenv('DB_HOST') ?: 'localhost');
$port = (int)($local['port'] ?? (getenv('DB_PORT') ?: 3306));
$user = $local['user'] ?? (getenv('DB_USER') ?: 'root');
$pass = $local['pass'] ?? (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
$db   = $local['name'] ?? (getenv('DB_NAME') ?: 'bloodbank');

if (!extension_loaded('mysqli')) {
    fwrite(STDERR, "ERROR: the PHP 'mysqli' extension is not enabled. Use XAMPP's PHP or enable extension=mysqli in php.ini.\n");
    exit(1);
}

$conn = @new mysqli($host, $user, $pass, '', $port);
if ($conn->connect_error) {
    fwrite(STDERR, "ERROR: cannot connect to MySQL at $host as '$user': " . $conn->connect_error . "\n");
    fwrite(STDERR, "Start MySQL (XAMPP Control Panel -> MySQL -> Start) and try again.\n");
    exit(1);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$db` DEFAULT CHARACTER SET utf8mb4");
if (!$conn->select_db($db)) {
    fwrite(STDERR, "ERROR: cannot use database '$db': " . $conn->error . "\n");
    exit(1);
}

$r = $conn->query("SHOW TABLES LIKE 'hospitals'");
if ($r && $r->num_rows > 0) {
    echo "Database '$db' is ready.\n";
    exit(0);
}

$sql = file_get_contents(__DIR__ . '/../database/bloodbank.sql');
if (!$conn->multi_query($sql)) {
    fwrite(STDERR, "ERROR importing SQL: " . $conn->error . "\n");
    exit(1);
}
do {
    if ($res = $conn->store_result()) { $res->free(); }
} while ($conn->more_results() && $conn->next_result());

if ($conn->errno) {
    fwrite(STDERR, "ERROR importing SQL: " . $conn->error . "\n");
    exit(1);
}
echo "Database '$db' created and sample data imported.\n";
exit(0);
