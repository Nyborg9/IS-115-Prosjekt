<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'My Website'; ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/IS-115-Prosjekt/index.php">phinn.no</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor03">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="/IS-115-Prosjekt/index.php">Hjem
            <span class="visually-hidden">(current)</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/IS-115-Prosjekt/view/listings.view.php">Jobb utlysninger</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/IS-115-Prosjekt/view/profile.view.php">Min profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/IS-115-Prosjekt/view/myApplications.view.php">Mine søknader</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/IS-115-Prosjekt/view/logout.view.php">Logg ut</a>
        </li>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
    </header>
