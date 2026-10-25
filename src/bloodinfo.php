<?php
session_start();
require 'file/connection.php';
if (!isset($_SESSION['hid'])) { redirect('login.php'); }
$hid = (int)$_SESSION['hid'];

$stmt = db_run($conn, 'SELECT bid, bg FROM bloodinfo WHERE hid = ? ORDER BY bg', 'i', [$hid]);
$rows = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
$title = 'Blood Bank | Manage Stock';
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
			<h1>Manage Blood Stock</h1>
			<p>Add or remove the blood groups available in your hospital.</p>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-5 mb-4">
			<div class="panel">
				<div class="panel-title"><i class="fas fa-plus-circle text-danger mr-2"></i>Add blood group</div>
				<form action="file/infoAdd.php" method="post">
					<div class="form-group">
						<label>Blood group</label>
						<select class="form-control" name="bg" required>
							<option value="" disabled selected>Select blood group</option>
							<?php foreach ($BLOOD_GROUPS as $g) { ?>
								<option value="<?php echo h($g); ?>"><?php echo h($g); ?></option>
							<?php } ?>
						</select>
					</div>
					<a data-toggle="collapse" href="#terms" role="button" aria-expanded="false" aria-controls="terms">Terms &amp; conditions</a>
					<div class="collapse mt-2 text-muted small" id="terms">
						Add a blood group only if the sample has been collected and tested by a doctor, nurse or trained phlebotomist at a pathology centre, clinic or hospital, and stored in tubes with the correct preservatives.
					</div>
					<div class="custom-control custom-checkbox my-3">
						<input type="checkbox" class="custom-control-input" id="agree" name="condition" value="agree" required>
						<label class="custom-control-label" for="agree">I agree</label>
					</div>
					<input type="submit" name="add" value="Add to stock" class="btn btn-primary btn-block">
				</form>
			</div>
		</div>

		<div class="col-lg-7 mb-4">
			<div class="panel flush">
				<div class="p-4 pb-3"><div class="panel-title mb-0">Your blood stock</div></div>
				<?php if (count($rows) === 0) { ?>
					<div class="empty-state"><i class="fas fa-tint"></i><p>Nothing in stock yet. Add your first blood group.</p></div>
				<?php } else { ?>
				<div class="table-responsive">
					<table class="table table-modern">
						<thead><tr><th>#</th><th>Blood group</th><th class="text-right">Action</th></tr></thead>
						<tbody>
						<?php foreach ($rows as $i => $row) { ?>
							<tr>
								<td><?php echo $i + 1; ?></td>
								<td><span class="bg-badge"><?php echo h($row['bg']); ?></span></td>
								<td class="text-right"><a href="file/delete.php?bid=<?php echo (int)$row['bid']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this blood group?');">Delete</a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</main>
<?php require 'footer.php'; ?>
</body>
</html>
