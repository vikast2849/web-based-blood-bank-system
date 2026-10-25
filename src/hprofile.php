<?php
require 'file/connection.php';
session_start();
if(!isset($_SESSION['hid']))
{
  header('location:login.php');
  exit;
}
$id=$_SESSION['hid'];
$stmt = db_run($conn, 'SELECT * FROM hospitals WHERE id = ?', 'i', [(int)$id]);
$row = $stmt ? $stmt->get_result()->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html>
<?php $title="Bloodbank | Hospital Profile"; ?>
<?php require 'head.php';?>
<body>
	<?php require 'header.php'; ?>

	<div class="container cont">

		<?php require 'message.php'; ?>

		<div class="row justify-content-center">
			<div class="col-lg-4 col-md-4 col-sm-6 mb-5">
				<div class="card">
					<div class="media justify-content-center mt-1">
						<img src="image/hospital.png" alt="profile" class="rounded-circle" width="70" height="60">
					</div>
					<div class="card-body">
					   <form action="file/updateprofile.php" method="post">
					   	<label class="text-muted font-weight-bold" class="text-muted font-weight-bold">Hospital Name</label>
						<input type="text" name="hname" value="<?php echo htmlspecialchars($row['hname']); ?>" class="form-control mb-3">
						<label class="text-muted font-weight-bold">Hospital Email</label>
						<input type="email" name="hemail" value="<?php echo htmlspecialchars($row['hemail']); ?>" class="form-control mb-3">
						<label class="text-muted font-weight-bold">New Password</label>
						<input type="password" name="hpassword" placeholder="Leave blank to keep current password" minlength="6" class="form-control mb-3">
						<label class="text-muted font-weight-bold">Hospital Phone Number</label>
						<input type="text" name="hphone" value="<?php echo htmlspecialchars($row['hphone']); ?>" class="form-control mb-3">
						<label class="text-muted font-weight-bold">Hospital City</label>
						<input type="text" name="hcity" value="<?php echo htmlspecialchars($row['hcity']); ?>" class="form-control mb-3">
						<input type="submit" name="update" class="btn btn-block btn-primary" value="Update">
					   </form>
					</div>
					<a href="index.php" class="text-center">Cancel</a><br>
				</div>
			</div>
		</div>
	</div>
	<?php require 'footer.php'; ?>
</body>
</html>