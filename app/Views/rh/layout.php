<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Espace RH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
  <div class="container">
    <span class="navbar-brand">Espace RH</span>
    <div class="navbar-nav">
      <a class="nav-link" href="<?= site_url('rh/evaluations') ?>">Évaluations</a>
      <a class="nav-link" href="<?= site_url('rh/entretiens') ?>">Entretiens</a>
      <a class="nav-link" href="<?= site_url('rh/tests') ?>">Tests techniques</a>
      <a class="nav-link" href="<?= site_url('rh/requetes') ?>">Requêtes candidats</a>
      <a class="nav-link" href="<?= site_url('rh/employes') ?>">Dossiers salariés</a>
      <a class="nav-link" href="<?= site_url('rh/temps-travail') ?>">Pointage</a>
      <a class="nav-link" href="<?= site_url('rh/conges') ?>">Congés</a>
    </div>
  </div>
</nav>
<main class="container pb-5">
  <?php if (session()->getFlashdata('succes')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('succes')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('erreur')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
  <?php endif; ?>
  <?= $this->renderSection('content') ?>
</main>
</body>
</html>
