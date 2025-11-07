<?php
$pageTitle = "Start side";
include 'inc/header.inc.php';
?>

<div class="mx-auto p-4 text-center mt-3 mb-5" style="width: 80%; background-color: #e9ecef; border-radius: 8px;">
  <h2>Søknadsportal for læringsassistenter</h2>
</div>

<div class="container my-4">
  <div class="row text-center">
    <div class="col bg-secondary bg-opacity-10 p-4 border rounded">
      <h4>Column 1</h4>
      <p>Some text inside the first column.</p>
    </div>
    <div class="col bg-secondary bg-opacity-10 p-4 border rounded">
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
        <button class="btn btn-primary" type="submit">Search</button>
      </form>
    </div>
    <div class="col bg-secondary bg-opacity-10 p-4 border rounded">
      <h4>Column 3</h4>
      <p>Third column content here.</p>
    </div>
  </div>
</div>
<?php include 'inc/footer.inc.php'; ?>
