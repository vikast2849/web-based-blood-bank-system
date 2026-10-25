<?php
session_start();
require 'file/connection.php';

$searchKey = isset($_GET['search']) ? trim($_GET['search']) : '';
$cols = 'bloodinfo.bid, bloodinfo.hid, bloodinfo.bg, hospitals.hname, hospitals.hcity, hospitals.hemail, hospitals.hphone';
if ($searchKey !== '' && in_array($searchKey, $BLOOD_GROUPS, true)) {
    $stmt = db_run($conn, "SELECT $cols FROM bloodinfo, hospitals WHERE bloodinfo.hid = hospitals.id AND bloodinfo.bg = ? ORDER BY hospitals.hname", 's', [$searchKey]);
} else {
    $searchKey = '';
    $stmt = db_run($conn, "SELECT $cols FROM bloodinfo, hospitals WHERE bloodinfo.hid = hospitals.id ORDER BY hospitals.hname, bloodinfo.bg");
}
$rows = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
$title = 'Blood Bank | Available Blood Samples';
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
			<h1>Available Blood Samples</h1>
			<p>Find hospitals that currently hold the blood group you need.</p>
		</div>
		<span class="count-pill"><?php echo count($rows); ?> found</span>
	</div>

	<div class="panel mb-4">
		<div class="chip-row">
			<a href="abs.php" class="chip<?php echo $searchKey === '' ? ' active' : ''; ?>">All</a>
			<?php foreach ($BLOOD_GROUPS as $g) { ?>
				<a href="abs.php?search=<?php echo urlencode($g); ?>" class="chip<?php echo $searchKey === $g ? ' active' : ''; ?>"><?php echo h($g); ?></a>
			<?php } ?>
		</div>
	</div>

	<div class="panel flush">
		<?php if (count($rows) === 0) { ?>
			<div class="empty-state"><i class="fas fa-tint"></i><p>No blood samples to show for this selection.</p></div>
		<?php } else { ?>
		<div class="table-responsive">
			<table class="table table-modern">
				<thead><tr><th>#</th><th>Hospital</th><th>City</th><th>Contact</th><th>Blood group</th><th class="text-right">Action</th></tr></thead>
				<tbody>
				<?php foreach ($rows as $i => $row) { ?>
					<tr>
						<td><?php echo $i + 1; ?></td>
						<td><strong><i class="fas fa-hospital text-muted mr-2"></i><?php echo h($row['hname']); ?></strong></td>
						<td><?php echo h($row['hcity']); ?></td>
						<td><?php echo h($row['hemail']); ?><br><small class="text-muted"><?php echo h($row['hphone']); ?></small></td>
						<td><span class="bg-badge"><?php echo h($row['bg']); ?></span></td>
						<td class="text-right">
						<?php if (isset($_SESSION['hid'])) { ?>
							<a href="javascript:void(0);" class="btn btn-soft btn-sm hospital">Request sample</a>
						<?php } elseif (isset($_SESSION['rid'])) { ?>
							<form action="file/request.php" method="post" class="d-inline">
								<input type="hidden" name="bid" value="<?php echo (int)$row['bid']; ?>">
								<input type="hidden" name="hid" value="<?php echo (int)$row['hid']; ?>">
								<input type="hidden" name="bg" value="<?php echo h($row['bg']); ?>">
								<input type="submit" name="request" class="btn btn-primary btn-sm" value="Request sample">
							</form>
						<?php } else { ?>
							<a href="login.php" class="btn btn-outline-danger btn-sm">Login to request</a>
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
<script>
$('.hospital').on('click', function(){ alert("Hospital users can't request blood."); });
</script>
</body>
</html>
