<?php 
require 'file/connection.php'; 
session_start();
  if(!isset($_SESSION['rid']))
  {
  header('location:login.php');
  exit;
  }
  else {
    $rid = $_SESSION['rid'];
    $stmt = db_run($conn, 'SELECT bloodrequest.*, hospitals.* FROM bloodrequest, hospitals WHERE bloodrequest.rid = ? AND bloodrequest.hid = hospitals.id', 'i', [(int)$rid]);
    $result = $stmt ? $stmt->get_result() : false;
?>

<!DOCTYPE html>
<html>
<?php $title="Bloodbank | Sent Requests"; ?>
<?php require 'head.php'; ?>
<body>
	<?php require 'header.php'; ?>
	<div class="container cont">

		<?php require 'message.php'; ?>

	<table class="table table-responsive table-striped rounded mb-5">
		<tr><th colspan="8" class="title">Sent requests</th></tr>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Email</th>
			<th>City</th>
			<th>Phone</th>
			<th>Blood Group</th>
			<th>Status</th>
			<th>Action</th>
		</tr>

		    <div>
                <?php
                if ($result) {
                    $row =mysqli_num_rows( $result);
                    if ($row) { //echo "<b> Total ".$row." </b>";
                }else echo '<b style="color:white;background-color:red;padding:7px;border-radius: 15px 50px;">You have not requested yet. </b>';
            }
            ?>
            </div>

		<?php $counter = 0; while($result && ($row = mysqli_fetch_array($result))) { ?>

		<tr>
			<td><?php echo ++$counter;?></td>
			<td><?php echo htmlspecialchars($row['hname']);?></td>
			<td><?php echo htmlspecialchars($row['hemail']);?></td>
			<td><?php echo htmlspecialchars($row['hcity']);?></td>
			<td><?php echo htmlspecialchars($row['hphone']);?></td>
			<td><?php echo htmlspecialchars($row['bg']);?></td>
			<td><?php echo htmlspecialchars($row['status']);?></td>
			<td><?php if($row['status'] == 'Accepted'){ ?>
			<?php }
			else{ ?>
				<a href="file/cancel.php?reqid=<?php echo htmlspecialchars($row['reqid']);?>" class="btn btn-danger">Cancel</a>
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
    <?php require 'footer.php'; ?>
</body>
</html>
<?php } ?>