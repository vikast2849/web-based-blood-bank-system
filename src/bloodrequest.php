<?php 
require 'file/connection.php'; 
session_start();
  if(!isset($_SESSION['hid']))
  {
  header('location:login.php');
  exit;
  }
  else {
    $hid = $_SESSION['hid'];
    $stmt = db_run($conn, 'SELECT bloodrequest.*, receivers.* FROM bloodrequest, receivers WHERE bloodrequest.hid = ? AND bloodrequest.rid = receivers.id', 'i', [(int)$hid]);
    $result = $stmt ? $stmt->get_result() : false;
?>

<!DOCTYPE html>
<html>
<?php $title="Bloodbank | Blood Requests"; ?>
<?php require 'head.php'; ?>
<body>
	<?php require 'header.php'; ?>
	<div class="container cont">

		<?php require 'message.php'; ?>

	<table class="table table-responsive table-striped rounded mb-5">
		<tr><th colspan="9" class="title">Blood requests</th></tr>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Email</th>
			<th>City</th>
			<th>Phone</th>
			<th>Blood Group</th>
			<th>Status</th>
			<th colspan="2">Action</th>
		</tr>

		    <div>
                <?php
                if ($result) {
                    $row =mysqli_num_rows( $result);
                    if ($row) { //echo "<b> Total ".$row." </b>";
                }else echo '<b style="color:white;background-color:red;padding:7px;border-radius: 15px 50px;">No one has requested yet. </b>';
            }
            ?>
            </div>

		<?php $counter = 0; while($result && ($row = mysqli_fetch_array($result))) { ?>

		<tr>
			<td><?php echo ++$counter;?></td>
			<td><?php echo htmlspecialchars($row['rname']);?></td>
			<td><?php echo htmlspecialchars($row['remail']);?></td>
			<td><?php echo htmlspecialchars($row['rcity']);?></td>
			<td><?php echo htmlspecialchars($row['rphone']);?></td>
			<td><?php echo htmlspecialchars($row['bg']);?></td>
			<td><?php echo 'You have '.htmlspecialchars($row['status']);?></td>
			<td><?php if($row['status'] == 'Accepted'){ ?> <a href="" class="btn btn-success disabled">Accepted</a> <?php }
			else{ ?>
				<a href="file/accept.php?reqid=<?php echo htmlspecialchars($row['reqid']);?>" class="btn btn-success">Accept</a>
			<?php } ?>
			</td>
			<td><?php if($row['status'] == 'Rejected'){ ?> <a href="" class="btn btn-danger disabled">Rejected</a> <?php }
			else{ ?>
				<a href="file/reject.php?reqid=<?php echo htmlspecialchars($row['reqid']);?>" class="btn btn-danger">Reject</a>
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