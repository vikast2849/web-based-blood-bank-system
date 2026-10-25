<?php
session_start();
if (isset($_SESSION['hid'])) { header('Location: bloodrequest.php'); exit; }
if (isset($_SESSION['rid'])) { header('Location: abs.php'); exit; }
$title = 'Blood Bank | Register';
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
          <h3>Create your account</h3>
          <p class="sub">Choose how you will use Blood Bank.</p>

          <ul class="nav role-tabs" role="tablist">
            <li class="nav-item flex-fill"><a class="nav-link active" data-toggle="tab" href="#hospitals"><i class="fas fa-hospital mr-1"></i>Hospital</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" data-toggle="tab" href="#receivers"><i class="fas fa-user mr-1"></i>Receiver</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="hospitals">
              <form action="file/hospitalReg.php" method="post">
                <div class="form-group"><label>Hospital name</label>
                  <input type="text" name="hname" placeholder="City General Hospital" class="form-control" required></div>
                <div class="form-group"><label>City</label>
                  <input type="text" name="hcity" placeholder="City" class="form-control" required></div>
                <div class="form-group"><label>Phone</label>
                  <input type="tel" name="hphone" placeholder="10-digit phone number" class="form-control" required pattern="[0,6-9]{1}[0-9]{9,11}" title="Phone number must start with 0, 6, 7, 8 or 9 and have 10 to 12 digits"></div>
                <div class="form-group"><label>Email</label>
                  <input type="email" name="hemail" placeholder="hospital@example.com" class="form-control" required></div>
                <div class="form-group"><label>Password</label>
                  <input type="password" name="hpassword" placeholder="Minimum 6 characters" class="form-control" required minlength="6"></div>
                <input type="submit" name="hregister" value="Register Hospital" class="btn btn-primary btn-block mt-4">
              </form>
            </div>
            <div class="tab-pane fade" id="receivers">
              <form action="file/receiverReg.php" method="post">
                <div class="form-group"><label>Full name</label>
                  <input type="text" name="rname" placeholder="Your name" class="form-control" required></div>
                <div class="form-group"><label>Blood group</label>
                  <select name="rbg" class="form-control" required>
                    <option value="" disabled selected>Select blood group</option>
                    <?php foreach (array('A+','A-','B+','B-','AB+','AB-','O+','O-') as $g) { ?>
                    <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                    <?php } ?>
                  </select></div>
                <div class="form-group"><label>City</label>
                  <input type="text" name="rcity" placeholder="City" class="form-control" required></div>
                <div class="form-group"><label>Phone</label>
                  <input type="tel" name="rphone" placeholder="10-digit phone number" class="form-control" required pattern="[0,6-9]{1}[0-9]{9,11}" title="Phone number must start with 0, 6, 7, 8 or 9 and have 10 to 12 digits"></div>
                <div class="form-group"><label>Email</label>
                  <input type="email" name="remail" placeholder="you@example.com" class="form-control" required></div>
                <div class="form-group"><label>Password</label>
                  <input type="password" name="rpassword" placeholder="Minimum 6 characters" class="form-control" required minlength="6"></div>
                <input type="submit" name="rregister" value="Register as Receiver" class="btn btn-primary btn-block mt-4">
              </form>
            </div>
          </div>

          <div class="auth-links">Already have an account? <a href="login.php"><strong>Sign in</strong></a></div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>
