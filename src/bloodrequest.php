<?php
session_start();
require 'file/connection.php';
if (!isset($_SESSION['hid'])) { redirect('login.php'); }
$hid = (int)$_SESSION['hid'];

$stmt = db_run($conn, 'SELECT bloodrequest.reqid, bloodrequest.bg, bloodrequest.status, receivers.rname, receivers.remail, receivers.rcity, receivers.rphone FROM bloodrequest, receivers WHERE bloodrequest.hid = ? AND bloodrequest.rid = receivers.id ORDER BY bloodrequest.reqid DESC', 'i', [$hid]);
$rows = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];

$pending = $accepted = $rejected = 0;
foreach ($rows as $r) {
    if ($r['status'] === 'Accepted') { $accepted++; }
    elseif ($r['status'] === 'Rejected') { $rejected++; }
    else { $pending++; }
}
$s = db_run($conn, 'SELECT COUNT(*) AS c FROM bloodinfo WHERE hid = ?', 'i', [$hid]);
$stock = $s ? (int)$s->get_result()->fetch_assoc()['c'] : 0;

$title = 'Blood Bank | Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<?php require 'head.php'; ?>
<body class="app">
<?php require 'header.php'; ?>
<main class="container app-main">
	<?php require 'message.php'; ?>

	<div class="page-head">
		<div>
			<h1>Hospital Dashboard</h1>
			<p>Welcome, <?php echo h($_SESSION['hname']); ?>. Manage your blood stock and incoming requests.</p>
		</div>
		<a href="bloodinfo.php" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Add blood group</a>
	</div>

	<div class="row">
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-red"><i class="fas fa-tint"></i></div><div><div class="stat-num"><?php echo $stock; ?></div><div class="stat-label">Blood groups in stock</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-amber"><i class="fas fa-clock"></i></div><div><div class="stat-num"><?php echo $pending; ?></div><div class="stat-label">Pending requests</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-green"><i class="fas fa-check-circle"></i></div><div><div class="stat-num"><?php echo $accepted; ?></div><div class="stat-label">Accepted</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-slate"><i class="fas fa-times-circle"></i></div><div><div class="stat-num"><?php echo $rejected; ?></div><div class="stat-label">Rejected</div></div></div></div>
	</div>

	<div class="panel flush">
		<div class="p-4 pb-3"><div class="panel-title mb-0">Blood requests</div></div>
		<?php if (count($rows) === 0) { ?>
			<div class="empty-state"><i class="far fa-folder-open"></i><p>No one has requested blood yet.</p></div>
		<?php } else { ?>
		<div class="table-responsive">
			<table class="table table-modern">
				<thead><tr><th>#</th><th>Receiver</th><th>Contact</th><th>City</th><th>Blood group</th><th>Status</th><th class="text-right">Action</th></tr></thead>
				<tbody>
				<?php foreach ($rows as $i => $row) { ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><strong><?php echo h($row['rname']); ?></strong></td>
						<td><?php echo h($row['remail']); ?><br><small class="text-muted"><?php echo h($row['rphone']); ?></small></td>
						<td><?php echo h($row['rcity']); ?></td>
						<td><span class="bg-badge"><?php echo h($row['bg']); ?></span></td>
						<td><?php echo status_badge($row['status']); ?></td>
						<td class="text-right text-nowrap">
							<?php if ($row['status'] !== 'Accepted') { ?>
								<a href="file/accept.php?reqid=<?php echo (int)$row['reqid']; ?>" class="btn btn-success btn-sm">Accept</a>
							<?php } ?>
							<?php if ($row['status'] !== 'Rejected') { ?>
								<a href="file/reject.php?reqid=<?php echo (int)$row['reqid']; ?>" class="btn btn-outline-danger btn-sm">Reject</a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<?php } ?>
	</div>
</main>
<?php require 'footer.php'; ?>
</body>
</html>
