<?php
session_start();

#Tittelen på siden 
#Henter headeren ifra fila header.inc.php
include 'inc/navbarController.inc.php';
?>
<!-- Overskriften -->
<div class="mx-auto p-4 text-center mt-3 mb-5" style="width: 80%; background-color: #e9ecef; border-radius: 8px;">
  <head>
  <title>Søknadsportal</title>
  </head>
  <h2>Søknadsportal for læringsassistenter</h2>
</div>
<?php 
#Henter footeren ifra fila footer.inc.php
include 'inc/footer.inc.php'; ?>
