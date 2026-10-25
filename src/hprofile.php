<?php
session_start();
require 'file/connection.php';
if (!isset($_SESSION['hid'])) { redirect('login.php'); }
$stmt = db_run($conn, 'SELECT * FROM hospitals WHERE id = ?', 'i', [(int)$_SESSION['hid']]);
$row = $stmt ? $stmt->get_result()->fetch_assoc() : null;
if (!$row) { redirect('logout.php'); }
$title = 'Blood Bank | Hospital Profile';
?>
<!DOCTYPE html>
<html lang="en">
<?php require 'head.php'; ?>
<body class="app">
<?php require 'header.php'; ?>
<main class="container app-main">
	<?php require 'message.php'; ?>
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-8">
			<div class="panel">
				<div class="profile-head">
					<span class="profile-avatar"><i class="fas fa-hospital"></i></span>
					<h4 class="mt-3 mb-0 font-weight-bold"><?php echo h($row['hname']); ?></h4>
					<small class="text-muted">Hospital account</small>
				</div>
				<form action="file/updateprofile.php" method="post">
					<div class="form-group"><label>Hospital name</label><input type="text" name="hname" value="<?php echo h($row['hname']); ?>" class="form-control" required></div>
					<div class="form-group"><label>Email</label><input type="email" name="hemail" value="<?php echo h($row['hemail']); ?>" class="form-control" required></div>
					<div class="form-group"><label>New password</label><input type="password" name="hpassword" placeholder="Leave blank to keep current password" minlength="6" class="form-control"></div>
					<div class="form-row">
						<div class="form-group col-md-6"><label>Phone</label><input type="text" name="hphone" value="<?php echo h($row['hphone']); ?>" class="form-control" required></div>
						<div class="form-group col-md-6"><label>City</label><input type="text" name="hcity" value="<?php echo h($row['hcity']); ?>" class="form-control" required></div>
					</div>
					<input type="submit" name="update" class="btn btn-primary btn-block mt-2" value="Save changes">
					<a href="bloodrequest.php" class="btn btn-block text-muted">Cancel</a>
				</form>
			</div>
		</div>
	</div>
</main>
<?php require 'footer.php'; ?>
</body>
</html>
