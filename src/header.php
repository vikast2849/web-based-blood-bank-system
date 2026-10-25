<?php
$current = basename($_SERVER['SCRIPT_NAME']);
$navItem = function ($file, $icon, $label) use ($current) {
    $a = ($file === $current) ? ' active' : '';
    return '<li class="nav-item"><a class="nav-link' . $a . '" href="' . $file . '"><i class="fas fa-' . $icon . ' mr-1"></i>' . $label . '</a></li>';
};
$isHospital = isset($_SESSION['hid']);
$isReceiver = isset($_SESSION['rid']);
?>
<nav class="navbar navbar-expand-lg navbar-light app-nav sticky-top">
	<div class="container">
		<a class="navbar-brand brand" href="index.php"><span class="brand-mark"><i class="fas fa-tint"></i></span>Blood Bank</a>
		<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav ml-auto align-items-lg-center">
			<?php if ($isHospital) { ?>
				<?php echo $navItem('bloodrequest.php', 'th-large', 'Dashboard'); ?>
				<?php echo $navItem('bloodinfo.php', 'plus-circle', 'Manage stock'); ?>
				<?php echo $navItem('abs.php', 'search', 'Available samples'); ?>
				<?php echo $navItem('info.php', 'book-medical', 'Blood guide'); ?>
				<li class="nav-item ml-lg-2"><a class="nav-link user-chip" href="hprofile.php"><span class="avatar"><i class="fas fa-hospital"></i></span><?php echo htmlspecialchars($_SESSION['hname']); ?></a></li>
				<li class="nav-item"><a class="nav-link btn-logout" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a></li>
			<?php } elseif ($isReceiver) { ?>
				<?php echo $navItem('abs.php', 'search', 'Find blood'); ?>
				<?php echo $navItem('sentrequest.php', 'paper-plane', 'My requests'); ?>
				<?php echo $navItem('info.php', 'book-medical', 'Blood guide'); ?>
				<li class="nav-item ml-lg-2"><a class="nav-link user-chip" href="rprofile.php"><span class="avatar"><i class="fas fa-user"></i></span><?php echo htmlspecialchars($_SESSION['rname']); ?></a></li>
				<li class="nav-item"><a class="nav-link btn-logout" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a></li>
			<?php } else { ?>
				<?php echo $navItem('abs.php', 'search', 'Available samples'); ?>
				<?php echo $navItem('info.php', 'book-medical', 'Blood guide'); ?>
				<?php echo $navItem('register.php', 'user-plus', 'Register'); ?>
				<li class="nav-item ml-lg-2"><a class="btn btn-primary" href="login.php">Login</a></li>
			<?php } ?>
			</ul>
		</div>
	</div>
</nav>
