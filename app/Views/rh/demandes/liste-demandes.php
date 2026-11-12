```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demandes de recrutement</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Demandes de recrutement</h1>
            <p class="text-muted mb-0">
                Consultation et suivi des demandes
            </p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (!empty($demandes)): ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Poste</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($demandes as $d): ?>
                                <?php
                                    $id = $d['id_demande'] ?? $d['id'] ?? '';
                                    $poste = $d['poste_libelle']
                                        ?? $d['libelle_poste']
                                        ?? $d['poste']
                                        ?? 'Non renseigné';
                                    $statut = $d['statut'] ?? 'Inconnu';

                                    $badge = 'secondary';

                                    if ($statut === 'BROUILLON') {
                                        $badge = 'secondary';
                                    } elseif ($statut === 'EN_ATTENTE') {
                                        $badge = 'warning';
                                    } elseif ($statut === 'REFUSEE') {
                                        $badge = 'danger';
                                    } elseif ($statut === 'VALIDEE') {
                                        $badge = 'success';
                                    }
                                ?>

                                <tr>
                                    <td><?= esc($id) ?></td>

                                    <td><?= esc($poste) ?></td>

                                    <td>
                                        <span class="badge text-bg-<?= $badge ?>">
                                            <?= esc($statut) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= esc(
                                            $d['date_creation']
                                            ?? $d['created_at']
                                            ?? 'Non renseignée'
                                        ) ?>
                                    </td>

                                    <td>
                                        <a
                                            href="<?= site_url('rh/demandes/' . $id) ?>"
                                            class="btn btn-sm btn-primary"
                                        >
                                            Voir les détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="text-center py-5">
                    <h2 class="h5">Aucune demande disponible</h2>
                    <p class="text-muted mb-0">
                        Les demandes de recrutement apparaîtront ici.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```