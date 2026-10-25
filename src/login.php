<?php
session_start();
if (isset($_SESSION['hid'])) { header('Location: bloodrequest.php'); exit; }
if (isset($_SESSION['rid'])) { header('Location: abs.php'); exit; }
$title = 'Blood Bank | Login';
?>
<!DOCTYPE html>
<html lang="en">
<?php require 'head.php'; ?>
<body class="auth">
  <div class="auth-wrap">
    <section class="auth-hero">
      <div class="auth-brand"><span class="brand-mark"><i class="fas fa-tint"></i></span>Blood Bank</div>
      <div>
        <h2>Every drop counts.<br>Connect with blood when it matters.</h2>
        <p class="lead">A simple platform where hospitals publish their blood stock and receivers find and request the right blood group in minutes.</p>
        <ul class="hero-points">
          <li><i class="fas fa-check-circle"></i>Real-time view of available blood groups</li>
          <li><i class="fas fa-check-circle"></i>One-click requests with live status tracking</li>
          <li><i class="fas fa-check-circle"></i>Separate secure dashboards for hospitals and receivers</li>
        </ul>
      </div>
      <div class="hero-stats">
        <div><strong>8</strong><span>Blood groups</span></div>
        <div><strong>24/7</strong><span>Availability</span></div>
        <div><strong>2</strong><span>Role dashboards</span></div>
      </div>
    </section>
    <section class="auth-panel">
      <div style="width:100%;max-width:430px;">
        <?php require 'message.php'; ?>
        <div class="auth-card">
          <h3>Welcome back</h3>
          <p class="sub">Sign in to continue to your dashboard.</p>

          <ul class="nav role-tabs" role="tablist">
            <li class="nav-item flex-fill"><a class="nav-link active" data-toggle="tab" href="#hospitals"><i class="fas fa-hospital mr-1"></i>Hospital</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" data-toggle="tab" href="#receivers"><i class="fas fa-user mr-1"></i>Receiver</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="hospitals">
              <form action="file/hospitalLogin.php" method="post">
                <div class="form-group"><label>Hospital email</label>
                  <input type="email" name="hemail" placeholder="hospital@example.com" class="form-control" required></div>
                <div class="form-group"><label>Password</label>
                  <input type="password" name="hpassword" placeholder="Your password" class="form-control" required></div>
                <input type="submit" name="hlogin" value="Sign in as Hospital" class="btn btn-primary btn-block mt-4">
              </form>
            </div>
            <div class="tab-pane fade" id="receivers">
              <form action="file/receiverLogin.php" method="post">
                <div class="form-group"><label>Receiver email</label>
                  <input type="email" name="remail" placeholder="you@example.com" class="form-control" required></div>
                <div class="form-group"><label>Password</label>
                  <input type="password" name="rpassword" placeholder="Your password" class="form-control" required></div>
                <input type="submit" name="rlogin" value="Sign in as Receiver" class="btn btn-primary btn-block mt-4">
              </form>
            </div>
          </div>

          <div class="auth-links">New here? <a href="register.php"><strong>Create an account</strong></a><br>
            <a href="abs.php">Browse available blood samples</a></div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>
