<?php
session_start();
require 'file/connection.php';
if (!isset($_SESSION['rid'])) { redirect('login.php'); }
$rid = (int)$_SESSION['rid'];

$stmt = db_run($conn, 'SELECT bloodrequest.reqid, bloodrequest.bg, bloodrequest.status, hospitals.hname, hospitals.hemail, hospitals.hcity, hospitals.hphone FROM bloodrequest, hospitals WHERE bloodrequest.rid = ? AND bloodrequest.hid = hospitals.id ORDER BY bloodrequest.reqid DESC', 'i', [$rid]);
$rows = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];

$pending = $accepted = $rejected = 0;
foreach ($rows as $r) {
    if ($r['status'] === 'Accepted') { $accepted++; }
    elseif ($r['status'] === 'Rejected') { $rejected++; }
    else { $pending++; }
}
$title = 'Blood Bank | My Requests';
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
			<h1>My Blood Requests</h1>
			<p>Track every request you have sent to hospitals.</p>
		</div>
		<a href="abs.php" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Find blood</a>
	</div>

	<div class="row">
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-red"><i class="fas fa-paper-plane"></i></div><div><div class="stat-num"><?php echo count($rows); ?></div><div class="stat-label">Total requests</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-amber"><i class="fas fa-clock"></i></div><div><div class="stat-num"><?php echo $pending; ?></div><div class="stat-label">Pending</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-green"><i class="fas fa-check-circle"></i></div><div><div class="stat-num"><?php echo $accepted; ?></div><div class="stat-label">Accepted</div></div></div></div>
		<div class="col-6 col-lg-3 mb-4"><div class="stat-card"><div class="stat-icon ic-slate"><i class="fas fa-times-circle"></i></div><div><div class="stat-num"><?php echo $rejected; ?></div><div class="stat-label">Rejected</div></div></div></div>
	</div>

	<div class="panel flush">
		<?php if (count($rows) === 0) { ?>
			<div class="empty-state"><i class="far fa-folder-open"></i><p>You have not requested blood yet.</p><a href="abs.php" class="btn btn-soft">Browse available samples</a></div>
		<?php } else { ?>
		<div class="table-responsive">
			<table class="table table-modern">
				<thead><tr><th>#</th><th>Hospital</th><th>Contact</th><th>City</th><th>Blood group</th><th>Status</th><th class="text-right">Action</th></tr></thead>
				<tbody>
				<?php foreach ($rows as $i => $row) { ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><strong><?php echo h($row['hname']); ?></strong></td>
						<td><?php echo h($row['hemail']); ?><br><small class="text-muted"><?php echo h($row['hphone']); ?></small></td>
						<td><?php echo h($row['hcity']); ?></td>
						<td><span class="bg-badge"><?php echo h($row['bg']); ?></span></td>
						<td><?php echo status_badge($row['status']); ?></td>
						<td class="text-right">
							<?php if ($row['status'] !== 'Accepted') { ?>
								<a href="file/cancel.php?reqid=<?php echo (int)$row['reqid']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cancel this request?');">Cancel</a>
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
