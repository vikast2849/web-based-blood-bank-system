<?php
/**
 * Blood Bank System - End-to-End CLI Smoke Test Suite
 * Usage: php tests/smoke_test.php [base_url]
 */

$baseUrl = $argv[1] ?? 'http://127.0.0.1:8000';

function get_db_config() {
    $cfgFile = __DIR__ . '/../src/file/config.local.php';
    $local = file_exists($cfgFile) ? include $cfgFile : [];
    return [
        'host' => $local['host'] ?? (getenv('DB_HOST') ?: '127.0.0.1'),
        'user' => $local['user'] ?? (getenv('DB_USER') ?: 'root'),
        'pass' => $local['pass'] ?? (getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''),
        'name' => $local['name'] ?? (getenv('DB_NAME') ?: 'bloodbank'),
    ];
}

function get_db() {
    static $conn = null;
    if ($conn === null) {
        $c = get_db_config();
        $conn = new mysqli($c['host'], $c['user'], $c['pass'], $c['name']);
        if ($conn->connect_error) {
            die("DB connection failed: " . $conn->connect_error . "\n");
        }
    }
    return $conn;
}

function run_query($sql) {
    $db = get_db();
    $res = $db->query($sql);
    if (!$res) {
        return '';
    }
    if ($res instanceof mysqli_result) {
        $row = $res->fetch_row();
        return $row ? (string)$row[0] : '';
    }
    return '';
}

function reset_db() {
    $c = get_db_config();
    $conn = new mysqli($c['host'], $c['user'], $c['pass']);
    if ($conn->connect_error) {
        die("Reset DB connection failed: " . $conn->connect_error . "\n");
    }
    $dbName = $c['name'];
    $conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8mb4");
    $conn->select_db($dbName);
    
    // Clear existing tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['bloodrequest', 'bloodinfo', 'receivers', 'hospitals'];
    foreach ($tables as $t) {
        $conn->query("DROP TABLE IF EXISTS `$t`");
    }
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    $sqlFile = realpath(__DIR__ . '/../database/bloodbank.sql');
    $sql = file_get_contents($sqlFile);
    $conn->multi_query($sql);
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    $conn->close();
}

class TestClient {
    public $cookies = [];
    public $baseUrl;

    public function __construct($baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function request($path, $method = 'GET', $data = []) {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $cookieHeader = '';
        if (!empty($this->cookies)) {
            $pairs = [];
            foreach ($this->cookies as $k => $v) {
                $pairs[] = "$k=$v";
            }
            $cookieHeader = 'Cookie: ' . implode('; ', $pairs) . "\r\n";
        }

        $parsedHost = parse_url($this->baseUrl, PHP_URL_HOST);
        $hostHeader = ($parsedHost === '127.0.0.1' || $parsedHost === 'localhost') ? "Host: bloodbank.vikasst.shop\r\n" : "";

        $opts = [
            'http' => [
                'method' => $method,
                'header' => $hostHeader . $cookieHeader . "User-Agent: SmokeTestRunner\r\n",
                'follow_location' => 0,
                'ignore_errors' => true,
            ]
        ];

        if ($method === 'POST') {
            $content = http_build_query($data);
            $opts['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n" .
                                       "Content-Length: " . strlen($content) . "\r\n";
            $opts['http']['content'] = $content;
        }

        $ctx = stream_context_create($opts);
        $body = @file_get_contents($url, false, $ctx);
        $responseHeaders = $http_response_header ?? [];

        $statusCode = 0;
        $location = null;

        foreach ($responseHeaders as $hdr) {
            if (preg_match('#^HTTP/\S+\s+(\d+)#i', $hdr, $m)) {
                $statusCode = (int)$m[1];
            } elseif (preg_match('#^Set-Cookie:\s*([^=;]+)=([^;]+)#i', $hdr, $m)) {
                $this->cookies[trim($m[1])] = trim($m[2]);
            } elseif (preg_match('#^Location:\s*(.+)#i', $hdr, $m)) {
                $location = trim($m[1]);
            }
        }

        return [
            'status' => $statusCode,
            'headers' => $responseHeaders,
            'location' => $location,
            'body' => (string)$body,
        ];
    }

    public function requestFollow($path, $method = 'GET', $data = [], $maxRedirects = 5) {
        $curPath = $path;
        $curMethod = $method;
        $curData = $data;

        for ($i = 0; $i < $maxRedirects; $i++) {
            $res = $this->request($curPath, $curMethod, $curData);
            if (in_array($res['status'], [301, 302, 303, 307, 308]) && !empty($res['location'])) {
                $loc = $res['location'];
                if (preg_match('#^https?://#i', $loc)) {
                    $parts = parse_url($loc);
                    $curPath = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
                } else {
                    $curPath = $loc;
                }
                $curMethod = 'GET';
                $curData = [];
                continue;
            }
            return $res;
        }
        return $res;
    }
}

echo "=== Blood Bank System E2E Smoke Tests ===\n";
echo "Target Base URL: $baseUrl\n";
echo "Resetting database to seed state...\n";
reset_db();
echo "Database ready.\n\n";

$results = [];

function record_test(&$results, $suite, $test, $pass, $details = '') {
    $status = $pass ? 'PASS' : 'FAIL';
    $results[] = [
        'suite' => $suite,
        'test' => $test,
        'status' => $status,
        'details' => $details
    ];
    printf("[%s] %s - %s: %s\n", $status, $suite, $test, $details);
}

// 1. Root & Page Loads
$anon = new TestClient($baseUrl);
$root = $anon->request('/');
$rootOk = ($root['status'] === 302 && strpos($root['location'] ?? '', 'login.php') !== false);
record_test($results, 'Routing', 'Root URL redirects to login.php', $rootOk, "Status: {$root['status']}, Location: {$root['location']}");

$pages = ['/login.php', '/register.php', '/abs.php', '/info.php'];
$allPagesClean = true;
foreach ($pages as $p) {
    $res = $anon->request($p);
    $hasWarnings = preg_match('#(Warning|Notice|Fatal error|Deprecated):#i', $res['body']);
    if ($res['status'] !== 200 || $hasWarnings) {
        $allPagesClean = false;
        break;
    }
}
record_test($results, 'Pages', 'Pages load with 200 OK and no PHP warnings', $allPagesClean, "Checked login, register, abs, info");

// 2. Seeded Accounts & Password Rehash
$gandhiPlain = run_query("SELECT hpassword FROM hospitals WHERE hemail='gandhi@gmail.com'");
$hSeedClient = new TestClient($baseUrl);
$hSeedRes = $hSeedClient->requestFollow('/file/hospitalLogin.php', 'POST', [
    'hlogin' => '1',
    'hemail' => 'gandhi@gmail.com',
    'hpassword' => 'gandhi'
]);
$gandhiHashed = run_query("SELECT hpassword FROM hospitals WHERE hemail='gandhi@gmail.com'");
$hSeedOk = (trim($gandhiPlain) === 'gandhi' && strpos($gandhiHashed, '$2y$') !== false && strpos($hSeedRes['body'], 'Gandhi hospital') !== false);
record_test($results, 'Auth', 'Seeded hospital gandhi@gmail.com logs in and rehashes password', $hSeedOk, "Plain: $gandhiPlain -> Hash: " . substr($gandhiHashed, 0, 15) . "...");

$testPlain = run_query("SELECT rpassword FROM receivers WHERE remail='test@gmail.com'");
$rSeedClient = new TestClient($baseUrl);
$rSeedRes = $rSeedClient->requestFollow('/file/receiverLogin.php', 'POST', [
    'rlogin' => '1',
    'remail' => 'test@gmail.com',
    'rpassword' => 'test'
]);
$testHashed = run_query("SELECT rpassword FROM receivers WHERE remail='test@gmail.com'");
$rSeedOk = (trim($testPlain) === 'test' && strpos($testHashed, '$2y$') !== false && strpos($rSeedRes['body'], 'test') !== false);
record_test($results, 'Auth', 'Seeded receiver test@gmail.com logs in and rehashes password', $rSeedOk, "Plain: $testPlain -> Hash: " . substr($testHashed, 0, 15) . "...");

// 3. Hospital Full Lifecycle
$hClient = new TestClient($baseUrl);
$newHEmail = 'metrohospital@health.org';
$hReg = $hClient->requestFollow('/file/hospitalReg.php', 'POST', [
    'hregister' => '1',
    'hname' => 'Metro Care Hospital',
    'hcity' => 'Bengaluru',
    'hphone' => '9876543210',
    'hemail' => $newHEmail,
    'hpassword' => 'securePass123'
]);
$hRegOk = strpos($hReg['body'], 'successfully registered') !== false;

$hLogin = $hClient->requestFollow('/file/hospitalLogin.php', 'POST', [
    'hlogin' => '1',
    'hemail' => $newHEmail,
    'hpassword' => 'securePass123'
]);
$hLoginOk = strpos($hLogin['body'], 'Metro Care Hospital') !== false;

// Dashboard stats check
$dash = $hClient->request('/bloodrequest.php');
$dashOk = (strpos($dash['body'], 'Blood groups in stock') !== false && strpos($dash['body'], 'Pending requests') !== false);

// Add blood group O-
$addStock = $hClient->requestFollow('/file/infoAdd.php', 'POST', [
    'add' => '1',
    'bg' => 'O-',
    'condition' => 'agree'
]);
$addStockOk = strpos($addStock['body'], 'added record successfully') !== false;

// Duplicate add rejected
$addDup = $hClient->requestFollow('/file/infoAdd.php', 'POST', [
    'add' => '1',
    'bg' => 'O-',
    'condition' => 'agree'
]);
$dupRejected = strpos($addDup['body'], 'already added this blood sample') !== false;

// Delete it
$bid = trim(run_query("SELECT bid FROM bloodinfo WHERE bg='O-' AND hid=(SELECT id FROM hospitals WHERE hemail='$newHEmail')"));
$delStock = $hClient->requestFollow('/file/delete.php?bid=' . $bid);
$delStockOk = strpos($delStock['body'], 'deleted one blood sample') !== false;

// Edit profile without password change
$editProf1 = $hClient->requestFollow('/file/updateprofile.php', 'POST', [
    'update' => '1',
    'hname' => 'Metro Care Super Hospital',
    'hemail' => $newHEmail,
    'hphone' => '9876543299',
    'hcity' => 'Bengaluru Central',
    'hpassword' => ''
]);
$editProf1Ok = strpos($editProf1['body'], 'profile is updated successfully') !== false;

// Edit profile with password change
$editProf2 = $hClient->requestFollow('/file/updateprofile.php', 'POST', [
    'update' => '1',
    'hname' => 'Metro Care Super Hospital',
    'hemail' => $newHEmail,
    'hphone' => '9876543299',
    'hcity' => 'Bengaluru Central',
    'hpassword' => 'newPassword456'
]);
$editProf2Ok = strpos($editProf2['body'], 'profile is updated successfully') !== false;

// Logout
$hLogout = $hClient->requestFollow('/logout.php');
$hLogoutOk = strpos($hLogout['body'], 'logged out') !== false || strpos($hLogout['body'], 'Sign in') !== false;

$hospitalSuiteOk = ($hRegOk && $hLoginOk && $dashOk && $addStockOk && $dupRejected && $delStockOk && $editProf1Ok && $editProf2Ok && $hLogoutOk);
record_test($results, 'Hospital', 'Hospital complete lifecycle & profile updates', $hospitalSuiteOk, "Reg, login, stats, add/dup/del stock, edit w/o & w/ pass, logout");

// 4. Receiver Full Lifecycle
$rClient = new TestClient($baseUrl);
$newREmail = 'priya.sharma@example.com';
$rReg = $rClient->requestFollow('/file/receiverReg.php', 'POST', [
    'rregister' => '1',
    'rname' => 'Priya Sharma',
    'rbg' => 'B+',
    'rcity' => 'Delhi',
    'rphone' => '9123456789',
    'remail' => $newREmail,
    'rpassword' => 'receiverPass123'
]);
$rRegOk = strpos($rReg['body'], 'successfully registered') !== false;

$rLogin = $rClient->requestFollow('/file/receiverLogin.php', 'POST', [
    'rlogin' => '1',
    'remail' => $newREmail,
    'rpassword' => 'receiverPass123'
]);
$rLoginOk = strpos($rLogin['body'], 'Priya Sharma') !== false;

// Filter blood group
$filterRes = $rClient->request('/abs.php?search=B%2B');
$filterOk = (strpos($filterRes['body'], 'Gandhi hospital') !== false && strpos($filterRes['body'], 'B+') !== false);

// Request a sample from Gandhi hospital (hid=1, B+)
$reqSample = $rClient->requestFollow('/file/request.php', 'POST', [
    'request' => '1',
    'hid' => '1',
    'bg' => 'B+'
]);
$reqSampleOk = strpos($reqSample['body'], 'requested for blood group B+') !== false;

// Duplicate request rejected
$reqDup = $rClient->requestFollow('/file/request.php', 'POST', [
    'request' => '1',
    'hid' => '1',
    'bg' => 'B+'
]);
$reqDupRejected = strpos($reqDup['body'], 'already requested for blood sample') !== false;

// Cancel request
$reqId = trim(run_query("SELECT reqid FROM bloodrequest WHERE hid=1 AND rid=(SELECT id FROM receivers WHERE remail='$newREmail')"));
$cancelReq = $rClient->requestFollow('/file/cancel.php?reqid=' . $reqId);
$cancelOk = strpos($cancelReq['body'], 'cancelled request') !== false;

// Edit profile
$rEdit = $rClient->requestFollow('/file/updateprofile.php', 'POST', [
    'update' => '1',
    'rname' => 'Priya Sharma Updated',
    'remail' => $newREmail,
    'rphone' => '9123456700',
    'rcity' => 'New Delhi',
    'bg' => 'O+',
    'rpassword' => ''
]);
$rEditOk = strpos($rEdit['body'], 'profile is updated successfully') !== false;

// Logout
$rLogout = $rClient->requestFollow('/logout.php');
$rLogoutOk = strpos($rLogout['body'], 'logged out') !== false || strpos($rLogout['body'], 'Sign in') !== false;

$receiverSuiteOk = ($rRegOk && $rLoginOk && $filterOk && $reqSampleOk && $reqDupRejected && $cancelOk && $rEditOk && $rLogoutOk);
record_test($results, 'Receiver', 'Receiver complete lifecycle & requests', $receiverSuiteOk, "Reg, login, search/filter, request, dup reject, cancel, edit, logout");

// 5. Accept & Reject Workflow
// Login receiver again and submit request to Gandhi hospital
$rClient2 = new TestClient($baseUrl);
$rClient2->requestFollow('/file/receiverLogin.php', 'POST', ['rlogin' => '1', 'remail' => $newREmail, 'rpassword' => 'receiverPass123']);
$rClient2->requestFollow('/file/request.php', 'POST', ['request' => '1', 'hid' => '1', 'bg' => 'B+']);
$reqIdAccept = trim(run_query("SELECT reqid FROM bloodrequest WHERE hid=1 AND rid=(SELECT id FROM receivers WHERE remail='$newREmail')"));

// Gandhi hospital accepts
$gClient = new TestClient($baseUrl);
$gClient->requestFollow('/file/hospitalLogin.php', 'POST', ['hlogin' => '1', 'hemail' => 'gandhi@gmail.com', 'hpassword' => 'gandhi']);
$acceptRes = $gClient->requestFollow('/file/accept.php?reqid=' . $reqIdAccept);
$acceptOk = strpos($acceptRes['body'], 'accepted the request') !== false;

$statusCheck1 = $rClient2->request('/sentrequest.php');
$receiverSeesAccept = strpos($statusCheck1['body'], 'Accepted') !== false;

// Another request to Unknown hospital (hid=2, A+) for rejection test
$rClient2->requestFollow('/file/request.php', 'POST', ['request' => '1', 'hid' => '2', 'bg' => 'A+']);
$reqIdReject = trim(run_query("SELECT reqid FROM bloodrequest WHERE hid=2 AND rid=(SELECT id FROM receivers WHERE remail='$newREmail')"));

$uClient = new TestClient($baseUrl);
$uClient->requestFollow('/file/hospitalLogin.php', 'POST', ['hlogin' => '1', 'hemail' => 'unknown@gmail.com', 'hpassword' => 'unknown']);
$rejectRes = $uClient->requestFollow('/file/reject.php?reqid=' . $reqIdReject);
$rejectOk = strpos($rejectRes['body'], 'rejected the request') !== false;

$statusCheck2 = $rClient2->request('/sentrequest.php');
$receiverSeesReject = strpos($statusCheck2['body'], 'Rejected') !== false;

$workflowOk = ($acceptOk && $receiverSeesAccept && $rejectOk && $receiverSeesReject);
record_test($results, 'Workflow', 'Hospital accepts and rejects requests; Receiver sees updated status', $workflowOk, "Accept flow: $acceptOk, Receiver sees: $receiverSeesAccept; Reject flow: $rejectOk, Receiver sees: $receiverSeesReject");

// 6. Authorization & Security Boundaries
$rBypassAccept = $rClient2->requestFollow('/file/accept.php?reqid=' . $reqIdAccept);
$recCantAccept = (strpos($rBypassAccept['body'], 'Receivers cannot perform this action') !== false || strpos($rBypassAccept['body'], 'Sign in') !== false);

$rBypassReject = $rClient2->requestFollow('/file/reject.php?reqid=' . $reqIdAccept);
$recCantReject = (strpos($rBypassReject['body'], 'Receivers cannot perform this action') !== false || strpos($rBypassReject['body'], 'Sign in') !== false);

$rBypassDelete = $rClient2->requestFollow('/file/delete.php?bid=1');
$recCantDelete = (strpos($rBypassDelete['body'], 'Receivers cannot perform this action') !== false || strpos($rBypassDelete['body'], 'Sign in') !== false);

// Cross receiver cancel test
$rOther = new TestClient($baseUrl);
$rOther->requestFollow('/file/receiverLogin.php', 'POST', ['rlogin' => '1', 'remail' => 'xyz@gmail.com', 'rpassword' => 'xyz']);
$crossCancel = $rOther->requestFollow('/file/cancel.php?reqid=' . $reqIdAccept);
$crossCancelBlocked = (strpos($crossCancel['body'], 'Error cancelling request') !== false || strpos($crossCancel['body'], 'My Blood Requests') !== false);
$reqStillExists = (trim(run_query("SELECT COUNT(*) FROM bloodrequest WHERE reqid=$reqIdAccept")) === '1');

$authOk = ($recCantAccept && $recCantReject && $recCantDelete && $crossCancelBlocked && $reqStillExists);
record_test($results, 'Security', 'Receiver cannot accept/reject/delete via URL tampering', $authOk, "Bypass accept blocked: $recCantAccept, reject blocked: $recCantReject, delete blocked: $recCantDelete, cross-cancel blocked: $reqStillExists");

// SQL Injection Test
$sqliClient = new TestClient($baseUrl);
$sqliPayload = "' OR 1=1 -- ";
$sqliRes1 = $sqliClient->request('/file/hospitalLogin.php', 'POST', ['hlogin' => '1', 'hemail' => $sqliPayload, 'hpassword' => $sqliPayload]);
$sqliRes2 = $sqliClient->request('/file/receiverLogin.php', 'POST', ['rlogin' => '1', 'remail' => $sqliPayload, 'rpassword' => $sqliPayload]);
$sqliBlocked = (strpos($sqliRes1['location'] ?? '', 'bloodrequest.php') === false && strpos($sqliRes2['location'] ?? '', 'abs.php') === false);
record_test($results, 'Security', "SQL injection payload ' OR 1=1 -- fails login", $sqliBlocked, "Hospital & Receiver login endpoints reject injection payload");

// XSS Escaping Test
$xssPayload = "<script>alert('xss')</script>";
$xssRes = $anon->requestFollow('/login.php?msg=' . urlencode($xssPayload));
$xssClean = (strpos($xssRes['body'], "<script>alert('xss')</script>") === false && strpos($xssRes['body'], "&lt;script&gt;") !== false);
record_test($results, 'Security', '<script> in ?msg= is rendered safely escaped', $xssClean, "Output escaped with htmlspecialchars");

// Reset back to pure seed data
echo "\nResetting database back to initial seed data...\n";
reset_db();
echo "Database reset complete.\n\n";

echo "==================== TEST SUMMARY TABLE ====================\n";
echo "| Test Suite | Test Case | Status | Details |\n";
echo "|---|---|:---:|---|\n";
$allPass = true;
foreach ($results as $r) {
    echo "| {$r['suite']} | {$r['test']} | **{$r['status']}** | {$r['details']} |\n";
    if ($r['status'] !== 'PASS') {
        $allPass = false;
    }
}
echo "\nOVERALL STATUS: " . ($allPass ? "ALL TESTS PASSED" : "TESTS FAILED") . "\n";
exit($allPass ? 0 : 1);
