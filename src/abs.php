<?php 
session_start();
require 'file/connection.php';
$searchKey = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($searchKey !== '' && in_array($searchKey, $BLOOD_GROUPS, true)) {
    $stmt = db_run($conn, 'SELECT bloodinfo.*, hospitals.* FROM bloodinfo, hospitals WHERE bloodinfo.hid = hospitals.id AND bloodinfo.bg = ?', 's', [$searchKey]);
} else {
    $searchKey = '';
    $stmt = db_run($conn, 'SELECT bloodinfo.*, hospitals.* FROM bloodinfo, hospitals WHERE bloodinfo.hid = hospitals.id');
}
$result = $stmt ? $stmt->get_result() : false;
?>

<!DOCTYPE html>
<html>
<?php $title="Bloodbank | Available Blood Samples"; ?>
<?php require 'head.php'; ?>
<body>
    <?php require 'header.php'; ?>
    <div class="container cont">
        
        <?php require 'message.php'; ?>
        
        <div class="row col-lg-8 col-md-8 col-sm-12 mb-3">
            <form method="get" action="" style="margin-top:-20px; ">
            <label class="font-weight-bolder">Select Blood Group:</label>
               <select name="search" class="form-control">
               <option value=""><?php echo htmlspecialchars($searchKey); ?></option>
               <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
               </select><br>
               <a href="abs.php" class="btn btn-info mr-4"> Reset</a>
               <input type="submit" name="submit" class="btn btn-info" value="search">
           </form>
        </div>

        <table class="table table-responsive table-striped rounded mb-5">
            <tr><th colspan="7" class="title">Available Blood Samples</th></tr>
            <tr>
                <th>#</th>
                <th>Hospital Name</th>
                <th>Hospital City</th>
                <th>Hospital Email</th>
                <th>Hospital Phone</th>
                <th>Blood Group</th>
                <th>Action</th>
            </tr>

            <div>
                <?php
                if ($result) {
                    $row =mysqli_num_rows( $result);
                    if ($row) { //echo "<b> Total ".$row." </b>";
                }else echo '<b style="color:white;background-color:red;padding:7px;border-radius: 15px 50px;">Nothing to show.</b>';
            }
            ?>
            </div>

        <?php $counter = 0; while($result && ($row = mysqli_fetch_array($result))) { ?>

            <tr>
                <td><?php echo ++$counter;?></td>
                <td><?php echo htmlspecialchars($row['hname']);?></td>
                <td><?php echo htmlspecialchars($row['hcity']); ?></td>
                <td><?php echo htmlspecialchars($row['hemail']); ?></td>
                <td><?php echo htmlspecialchars($row['hphone']); ?></td>
                <td><?php echo htmlspecialchars($row['bg']); ?></td>

                <?php if (isset($_SESSION['hid'])) { ?>
                <td><a href="javascript:void(0);" class="btn btn-info hospital">Request Sample</a></td>
                <?php } else { ?>
                <td>
                    <form action="file/request.php" method="post">
                        <input type="hidden" name="bid" value="<?php echo (int)$row['bid']; ?>">
                        <input type="hidden" name="hid" value="<?php echo (int)$row['hid']; ?>">
                        <input type="hidden" name="bg" value="<?php echo htmlspecialchars($row['bg']); ?>">
                        <input type="submit" name="request" class="btn btn-info" value="Request Sample">
                    </form>
                </td>
                <?php } ?>
            </tr>

        <?php } ?>
        </table>
    </div>
    <?php require 'footer.php' ?>
</body>

<script type="text/javascript">
    $('.hospital').on('click', function(){
        alert("Hospital user can't request for blood.");
    });
</script>