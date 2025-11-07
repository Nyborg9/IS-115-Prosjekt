<?php
$pageTitle = "Registrering";
include 'inc/header.inc.php';
?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Registrering</h3>
      <form id="Skjema" method="post" action="">
        <div class="mb-3">
          <label for="Fornavn" class="form-label">Fornavn</label>
          <input type="text" class="form-control" name="fornavn" id="Fornavn" maxlength="50" required>
        </div>
        <div class="mb-3">
        <label for="Etternavn" class="form-label">Etternavn</label>
          <input type="text" class="form-control" name="etternavn" id="Etternavn" maxlength="50" required>
        </div>  
        <div class="mb-3">
        <label for="Telefon" class="form-label">Telefon</label>
          <input type="phone" class="form-control" name="telefon" id="Telefon" maxlength="50" required>
        </div>  
        <div class="mb-3">
          <label for="Epost" class="form-label">E-post</label>
          <input type="tel" class="form-control" name="epost" id="Epost" maxlength="50" required>
        </div>
        <div class="mb-3">
          <label for="Passord" class="form-label">Passord</label>
          <input type="password" class="form-control" name="passord" id="Passord" maxlength="8" minlength="8" required>
        </div>
        <div class="mb-3">
        <label for="BekreftPassord" class="form-label">Bekreft passord</label>
        <input type="password" class="form-control" name="bekreftPassord" id="BekreftPassord" maxlength="8" minlength="8" required>
      </div>
        <input type="hidden" name="robot" value="">
        <?php include "inc/registreringsSkjema.inc.php"
        
        ?>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Send inn</button>
        </div>
      </form>
    </div>
  </div>

<?php include 'inc/footer.inc.php'; ?>
