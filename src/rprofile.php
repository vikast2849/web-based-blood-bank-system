<?php
session_start();
require 'file/connection.php';
if (!isset($_SESSION['rid'])) { redirect('login.php'); }
$stmt = db_run($conn, 'SELECT * FROM receivers WHERE id = ?', 'i', [(int)$_SESSION['rid']]);
$row = $stmt ? $stmt->get_result()->fetch_assoc() : null;
if (!$row) { redirect('logout.php'); }
$title = 'Blood Bank | My Profile';
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
					<span class="profile-avatar"><i class="fas fa-user"></i></span>
					<h4 class="mt-3 mb-0 font-weight-bold"><?php echo h($row['rname']); ?></h4>
					<small class="text-muted">Receiver account</small>
				</div>
				<form action="file/updateprofile.php" method="post">
					<div class="form-group"><label>Full name</label><input type="text" name="rname" value="<?php echo h($row['rname']); ?>" class="form-control" required></div>
					<div class="form-group"><label>Email</label><input type="email" name="remail" value="<?php echo h($row['remail']); ?>" class="form-control" required></div>
					<div class="form-group"><label>New password</label><input type="password" name="rpassword" placeholder="Leave blank to keep current password" minlength="6" class="form-control"></div>
					<div class="form-row">
						<div class="form-group col-md-6"><label>Phone</label><input type="text" name="rphone" value="<?php echo h($row['rphone']); ?>" class="form-control" required></div>
						<div class="form-group col-md-6"><label>City</label><input type="text" name="rcity" value="<?php echo h($row['rcity']); ?>" class="form-control" required></div>
					</div>
					<div class="form-group"><label>Blood group</label>
						<select class="form-control" name="bg" required>
							<?php foreach ($BLOOD_GROUPS as $g) { ?>
								<option value="<?php echo h($g); ?>"<?php echo $row['rbg'] === $g ? ' selected' : ''; ?>><?php echo h($g); ?></option>
							<?php } ?>
						</select>
					</div>
					<input type="submit" name="update" class="btn btn-primary btn-block mt-2" value="Save changes">
					<a href="abs.php" class="btn btn-block text-muted">Cancel</a>
				</form>
			</div>
		</div>
	</div>
</main>
<?php require 'footer.php'; ?>
</body>
</html>
