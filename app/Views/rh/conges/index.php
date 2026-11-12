<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><title>Congés et absences</title></head>
<body>
<h1>Congés et absences</h1>
<?php if ($message = session()->getFlashdata('success')): ?><p><?= esc($message) ?></p><?php endif ?>
<?php if ($message = session()->getFlashdata('error')): ?><p><?= esc($message) ?></p><?php endif ?>
<form method="post" enctype="multipart/form-data" action="<?= site_url('rh/conges') ?>">
    <?= csrf_field() ?>
    <label>Employé <input type="number" name="id_employe" required></label>
    <label>Type <select name="id_type_conge" required><?php foreach ($types as $type): ?><option value="<?= $type['id_type_conge'] ?>"><?= esc($type['libelle']) ?></option><?php endforeach ?></select></label>
    <label>Du <input type="date" name="date_debut" required></label>
    <label>Au <input type="date" name="date_fin" required></label>
    <label>Jours <input type="number" step="0.5" min="0.5" name="nombre_jours" required></label>
    <label>Motif <textarea name="motif"></textarea></label>
    <label>Justificatif <input type="file" name="justificatif"></label>
    <button type="submit">Soumettre</button>
</form>
<h2>Déclarer une absence</h2>
<form method="post" action="<?= site_url('rh/absences') ?>"><?= csrf_field() ?>
    <input type="number" name="id_employe" placeholder="ID employé" required>
    <input type="date" name="date_debut" required><input type="date" name="date_fin" required>
    <input name="type_absence" value="ABSENCE"><input name="motif" placeholder="Motif">
    <label><input type="checkbox" name="justifiee" value="1"> Justifiée</label>
    <button type="submit">Enregistrer l'absence</button>
</form>
<table><tr><th>Employé</th><th>Type</th><th>Période</th><th>Statut</th><th>Action</th></tr>
<?php foreach ($demandes as $demande): ?><tr>
    <td><?= esc($demande['matricule'] . ' - ' . $demande['prenom'] . ' ' . $demande['nom']) ?></td>
    <td><?= esc($demande['type_libelle']) ?></td><td><?= esc($demande['date_debut'] . ' → ' . $demande['date_fin']) ?></td>
    <td><?= esc($demande['statut']) ?></td><td><?php if ($demande['statut'] === 'EN_ATTENTE'): ?>
        <form method="post" action="<?= site_url('rh/conges/' . $demande['id_demande_conge'] . '/decision') ?>"><?= csrf_field() ?><button name="statut" value="VALIDE">Valider</button><button name="statut" value="REFUSE">Refuser</button></form>
    <?php endif ?></td>
</tr><?php endforeach ?></table>
</body></html>
