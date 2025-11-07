<?php
$pageTitle = "Innlogging";
include 'inc/header.inc.php';
?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Logg inn</h3>
      <form id="Skjema" method="post" action="">
        <div class="mb-3">
          <label for="Epost" class="form-label">E-post</label>
          <input type="email" class="form-control" name="epost" id="Epost" maxlength="50" required>
        </div>
        <div class="mb-3">
          <label for="Passord" class="form-label">Passord</label>
          <input type="password" class="form-control" name="passord" id="Passord" maxlength="8" minlength="8" required>
        </div>

        <input type="hidden" name="robot" value="">

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Send inn</button>
        </div>
      </form>
      <?php
      include 'inc/innloggingsSkjema.inc.php';
      ?>
    </div>
  </div>

<?php 
include 'inc/footer.inc.php'; 
?>
