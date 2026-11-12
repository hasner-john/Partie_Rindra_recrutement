
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une offre - Espace RH</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-4">
    <h2>Créer une offre d'emploi</h2>
    <p class="text-muted">
        Complétez les informations de l'offre à partir de la demande sélectionnée.
    </p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Informations de la demande</div>
        <div class="card-body">
            <p><strong>Poste :</strong> <?= esc($demande['poste']) ?></p>
            <p><strong>Département :</strong> <?= esc($demande['departement']) ?></p>
            <p><strong>Effectif demandé :</strong> <?= esc($demande['effectif_demande']) ?></p>
            <p><strong>Salaire par personne :</strong> <?= esc($demande['salaire_par_personne']) ?></p>
            <p><strong>Date du besoin :</strong> <?= esc($demande['date_besoin']) ?></p>
            <p><strong>Motif :</strong> <?= esc($demande['motif']) ?></p>
        </div>
    </div>

    <form action="<?= site_url('rh/offres/enregistrer') ?>" method="post">
        <?= csrf_field() ?>

        <input
            type="hidden"
            name="id_demande"
            value="<?= esc($demande['id_demande']) ?>"
        >

        <div class="mb-3">
            <label for="titre" class="form-label">Titre de l'offre</label>
            <input
                type="text"
                class="form-control"
                id="titre"
                name="titre"
                maxlength="180"
                value="<?= esc(old('titre', $demande['poste'])) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description de l'offre</label>
            <textarea
                class="form-control"
                id="description"
                name="description"
                rows="6"
                required
            ><?= esc(old('description', $demande['description_poste'] ?? '')) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="date_limite" class="form-label">Date limite de candidature</label>
            <input
                type="date"
                class="form-control"
                id="date_limite"
                name="date_limite"
                min="<?= date('Y-m-d') ?>"
                value="<?= esc(old('date_limite')) ?>"
            >
            <div class="form-text">
                Ce champ est facultatif. Vous pourrez le laisser vide si aucune date limite n'est définie.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Enregistrer en brouillon
        </button>

        <a href="<?= site_url('rh/offres/demandes') ?>" class="btn btn-secondary">
            Annuler
        </a>
    </form>
</div>

</body>
</html>
