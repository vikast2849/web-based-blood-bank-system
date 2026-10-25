<?php $counter = 0; ?>
<div class="flash-wrap">
	<?php if (isset($_GET['msg'])) { ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_GET['msg']); ?>
			<button type="button" class="close" data-dismiss="alert" title="close">&times;</button>
		</div>
	<?php } ?>
	<?php if (isset($_GET['error'])) { ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
			<button type="button" class="close" data-dismiss="alert" title="close">&times;</button>
		</div>
	<?php } ?>
</div>
<script>
$(function(){ setTimeout(function(){ $('.flash-wrap .alert').alert('close'); }, 6000); });
</script>
