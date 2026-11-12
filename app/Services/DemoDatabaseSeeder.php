<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Creates a complete, deterministic dataset for local demonstrations and tests.
 */
class DemoDatabaseSeeder
{
    public function initialize(bool $reset = false): void
    {
        $database = ROOTPATH . 'writable/database.db';

        if ($reset && is_file($database) && ! unlink($database)) {
            throw new RuntimeException('Impossible de supprimer la base SQLite existante.');
        }

        if (! is_file($database)) {
            $this->createDirectory(dirname($database));
            touch($database);
        }

        $pdo = new \PDO('sqlite:' . $database);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON');

        $hasSchema = (bool) $pdo->query(
            "SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'utilisateur'"
        )->fetchColumn();

        if ($hasSchema && ! $reset && (int) $pdo->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn() > 0) {
            throw new RuntimeException('La base contient déjà des données. Utilisez php spark db:reset --force.');
        }

        if (! $hasSchema) {
            $schema = ROOTPATH . 'base_recrutement.sql';
            if (! is_file($schema)) {
                throw new RuntimeException('Le fichier base_recrutement.sql est introuvable.');
            }
            $pdo->exec((string) file_get_contents($schema));
        }

        $pdo->beginTransaction();
        try {
            if ($reset) {
                $this->clearTables($pdo);
            }
            $this->seed($pdo);
            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    private function seed(\PDO $pdo): void
    {
        $year = (int) date('Y');
        $password = password_hash('password', PASSWORD_DEFAULT);

        $this->insert($pdo, 'utilisateur', [
            ['id_utilisateur' => 1, 'email' => 'admin@test.local', 'mot_de_passe_hash' => $password, 'role' => 'ADMINISTRATEUR', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
            ['id_utilisateur' => 2, 'email' => 'rh@test.local', 'mot_de_passe_hash' => $password, 'role' => 'RH', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
            ['id_utilisateur' => 3, 'email' => 'direction@test.local', 'mot_de_passe_hash' => $password, 'role' => 'DIRECTION', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
            ['id_utilisateur' => 4, 'email' => 'departement@test.local', 'mot_de_passe_hash' => $password, 'role' => 'DEPARTEMENT', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
            ['id_utilisateur' => 5, 'email' => 'finance@test.local', 'mot_de_passe_hash' => $password, 'role' => 'FINANCE', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
            ['id_utilisateur' => 6, 'email' => 'candidat@test.local', 'mot_de_passe_hash' => $password, 'role' => 'CANDIDAT', 'actif' => 1, 'date_creation' => '2025-01-01 08:00:00'],
        ]);
        $this->insert($pdo, 'departement', [
            ['id_departement' => 1, 'nom' => 'Ressources humaines', 'description' => 'Administration du personnel', 'actif' => 1],
            ['id_departement' => 2, 'nom' => 'Informatique', 'description' => 'Systèmes et développement', 'actif' => 1],
        ]);
        $this->insert($pdo, 'personne', [
            ['id_personne' => 1, 'id_utilisateur' => 2, 'nom' => 'Rakoto', 'prenom' => 'Hery', 'telephone' => '0340000001', 'adresse' => 'Antananarivo', 'date_naissance' => '1990-04-12'],
            ['id_personne' => 2, 'id_utilisateur' => 3, 'nom' => 'Rasoanaivo', 'prenom' => 'Miora', 'telephone' => '0340000002', 'adresse' => 'Antananarivo', 'date_naissance' => '1987-09-20'],
            ['id_personne' => 3, 'id_utilisateur' => 6, 'nom' => 'Andria', 'prenom' => 'Tovo', 'telephone' => '0340000003', 'adresse' => 'Fianarantsoa', 'date_naissance' => '1998-02-15'],
        ]);
        $this->insert($pdo, 'poste', [
            ['id_poste' => 1, 'id_departement' => 1, 'libelle' => 'Responsable RH', 'description' => 'Gestion administrative et sociale', 'statut' => 'ACTIF', 'cree_par' => 3, 'date_creation' => '2025-01-02 09:00:00'],
            ['id_poste' => 2, 'id_departement' => 2, 'libelle' => 'Développeur logiciel', 'description' => 'Développement des applications', 'statut' => 'ACTIF', 'cree_par' => 3, 'date_creation' => '2025-01-02 09:00:00'],
        ]);
        $this->insert($pdo, 'candidat', [
            ['id_candidat' => 1, 'id_personne' => 3, 'niveau_etude' => 'Master informatique', 'experience' => '2 ans'],
        ]);
        $this->insert($pdo, 'demande_recrutement', [
            ['id_demande' => 1, 'id_poste' => 2, 'id_demandeur' => 3, 'effectif_demande' => 1, 'motif' => 'Renforcement de l’équipe', 'salaire_par_personne' => 1800000, 'date_besoin' => '2025-02-01', 'statut' => 'VALIDE', 'date_creation' => '2025-01-03 09:00:00', 'date_modification' => '2025-01-03 09:00:00'],
        ]);
        $this->insert($pdo, 'offre_emploi', [
            ['id_offre' => 1, 'id_demande' => 1, 'titre' => 'Développeur PHP junior', 'description' => 'Développement CodeIgniter', 'date_publication' => '2025-01-04', 'date_limite' => '2025-01-31', 'statut' => 'FERMEE', 'cree_par' => 3, 'date_creation' => '2025-01-04 09:00:00'],
        ]);
        $this->insert($pdo, 'candidature', [
            ['id_candidature' => 1, 'id_offre' => 1, 'id_candidat' => 1, 'cv_nom' => 'cv-tovo.pdf', 'cv_type_mime' => 'application/pdf', 'cv_donnees' => 'CV de démonstration', 'lettre_nom' => 'lettre.pdf', 'lettre_type_mime' => 'application/pdf', 'lettre_donnees' => 'Lettre de démonstration', 'cin_nom' => 'cin.pdf', 'cin_type_mime' => 'application/pdf', 'cin_donnees' => 'CIN de démonstration', 'motivation' => 'Je souhaite rejoindre votre équipe.', 'statut' => 'RETENUE', 'date_candidature' => '2025-01-05 10:00:00'],
        ]);
        $this->insert($pdo, 'entretien', [
            ['id_entretien' => 1, 'id_candidature' => 1, 'id_organisateur' => 2, 'date_entretien' => '2025-01-10 09:00:00', 'lieu' => 'Salle RH', 'notes' => 'Profil validé', 'statut' => 'REALISE', 'date_creation' => '2025-01-05 11:00:00'],
        ]);
        $this->insert($pdo, 'evaluation_candidat', [
            ['id_evaluation' => 1, 'id_candidature' => 1, 'id_evaluateur' => 2, 'id_entretien' => 1, 'id_test' => null, 'type_evaluation' => 'ENTRETIEN', 'note' => 8, 'commentaire' => 'Bon niveau technique', 'date_evaluation' => '2025-01-10'],
        ]);
        $this->insert($pdo, 'test_technique', [
            ['id_test' => 1, 'id_candidature' => 1, 'id_organisateur' => 2, 'intitule' => 'Test PHP', 'description' => 'API CRUD simple', 'date_test' => '2025-01-08', 'statut' => 'REALISE', 'date_creation' => '2025-01-05 12:00:00'],
        ]);
        $this->insert($pdo, 'contrat', [
            ['id_contrat' => 1, 'id_candidature' => 1, 'reference' => 'CTR-2025-001', 'type_contrat' => 'CDI', 'date_debut' => '2025-02-01', 'date_fin' => null, 'salaire' => 1800000, 'periodicite_salaire' => 'MENSUEL', 'lieu_travail' => 'Antananarivo', 'missions' => 'Développement logiciel', 'horaires' => '08:00-17:00', 'conges_absences' => 'Selon règlement intérieur', 'statut' => 'SIGNE', 'pdf_nom' => null, 'pdf_type_mime' => null, 'pdf_donnees' => null, 'date_signature' => '2025-01-15'],
        ]);
        $this->insert($pdo, 'employe', [
            ['id_employe' => 1, 'id_contrat' => 1, 'id_personne' => 3, 'matricule' => 'EMP-0001', 'date_embauche' => '2025-02-01', 'statut' => 'ACTIF'],
        ]);
        $this->insert($pdo, 'affectation', [
            ['id_affectation' => 1, 'id_employe' => 1, 'id_departement' => 2, 'date_debut' => '2025-02-01', 'date_fin' => null, 'fonction' => 'Développeur logiciel'],
        ]);
        $this->insert($pdo, 'historique_salaire', [
            ['id_historique' => 1, 'id_employe' => 1, 'ancien_salaire' => null, 'nouveau_salaire' => 1800000, 'periodicite_salaire' => 'MENSUEL', 'motif' => 'Embauche', 'id_utilisateur' => 2, 'date_effet' => '2025-02-01', 'date_action' => '2025-02-01 08:00:00'],
        ]);
        $this->insert($pdo, 'document_employe', [
            ['id_document' => 1, 'id_employe' => 1, 'type_document' => 'CONTRAT', 'nom_fichier' => 'contrat-emp-0001.pdf', 'type_mime' => 'application/pdf', 'donnees' => 'Document de démonstration', 'date_expiration' => null, 'commentaire' => 'Contrat signé', 'id_utilisateur' => 2, 'date_creation' => '2025-02-01 08:00:00', 'date_modification' => '2025-02-01 08:00:00'],
        ]);
        $this->insert($pdo, 'regime_travail', [
            ['id_regime' => 1, 'libelle' => 'Temps plein', 'heures_hebdomadaires' => 40, 'actif' => 1],
            ['id_regime' => 2, 'libelle' => 'Temps partiel', 'heures_hebdomadaires' => 20, 'actif' => 1],
        ]);
        $this->insert($pdo, 'horaire', [
            ['id_horaire' => 1, 'id_regime' => 1, 'libelle' => 'Horaire standard', 'heure_debut' => '08:00', 'heure_fin' => '17:00', 'pause_minutes' => 60, 'actif' => 1],
        ]);
        $this->insert($pdo, 'planning', [
            ['id_planning' => 1, 'id_employe' => 1, 'id_horaire' => 1, 'date_planning' => '2025-02-10', 'statut' => 'PLANIFIE'],
        ]);
        $this->insert($pdo, 'pointage', [
            ['id_pointage' => 1, 'id_employe' => 1, 'date_pointage' => '2025-02-10', 'heure_entree' => '2025-02-10 08:12:00', 'heure_sortie' => '2025-02-10 18:00:00', 'source' => 'MANUEL', 'statut' => 'CLOTURE', 'commentaire' => 'Pointage de démonstration'],
            ['id_pointage' => 2, 'id_employe' => 1, 'date_pointage' => '2025-02-11', 'heure_entree' => '2025-02-11 08:00:00', 'heure_sortie' => null, 'source' => 'MANUEL', 'statut' => 'OUVERT', 'commentaire' => null],
        ]);
        $this->insert($pdo, 'retard', [
            ['id_retard' => 1, 'id_pointage' => 1, 'minutes' => 12, 'motif' => 'Transport', 'justifie' => 0, 'statut' => 'A_TRAITER'],
        ]);
        $this->insert($pdo, 'heure_supplementaire', [
            ['id_heure_supplementaire' => 1, 'id_employe' => 1, 'date_travail' => '2025-02-10', 'heures' => 1, 'motif' => 'Livraison urgente', 'statut' => 'VALIDE', 'id_validateur' => 2],
        ]);
        $this->insert($pdo, 'temps_travaille', [
            ['id_temps' => 1, 'id_pointage' => 1, 'minutes_travaillees' => 468, 'minutes_theoriques' => 480, 'ecart_minutes' => -12, 'calcule_le' => '2025-02-10 18:05:00'],
        ]);
        $this->insert($pdo, 'anomalie_pointage', [
            ['id_anomalie' => 1, 'id_pointage' => 2, 'id_employe' => 1, 'date_anomalie' => '2025-02-11', 'type_anomalie' => 'SORTIE_MANQUANTE', 'description' => 'Heure de sortie absente', 'statut' => 'OUVERTE', 'date_resolution' => null],
        ]);
        $this->insert($pdo, 'type_conge', [
            ['id_type_conge' => 1, 'code' => 'CA', 'libelle' => 'Congé annuel', 'description' => 'Congé payé annuel', 'jours_annuels' => 30, 'justificatif_obligatoire' => 0, 'actif' => 1],
            ['id_type_conge' => 2, 'code' => 'MAL', 'libelle' => 'Congé maladie', 'description' => 'Absence pour maladie', 'jours_annuels' => 0, 'justificatif_obligatoire' => 1, 'actif' => 1],
        ]);
        $this->insert($pdo, 'droit_conge', [
            ['id_droit_conge' => 1, 'id_type_conge' => 1, 'code_role' => 'EMPLOYE', 'anciennete_mois' => 0, 'jours_annuels' => 30],
            ['id_droit_conge' => 2, 'id_type_conge' => 2, 'code_role' => 'EMPLOYE', 'anciennete_mois' => 0, 'jours_annuels' => 0],
        ]);
        $this->insert($pdo, 'solde_conge', [
            ['id_solde_conge' => 1, 'id_employe' => 1, 'id_type_conge' => 1, 'annee' => $year, 'acquis' => 30, 'pris' => 2, 'ajuste' => 0],
            ['id_solde_conge' => 2, 'id_employe' => 1, 'id_type_conge' => 2, 'annee' => $year, 'acquis' => 0, 'pris' => 0, 'ajuste' => 0],
        ]);
        $this->insert($pdo, 'demande_conge', [
            ['id_demande_conge' => 1, 'id_employe' => 1, 'id_type_conge' => 1, 'date_debut' => "$year-03-10", 'date_fin' => "$year-03-11", 'nombre_jours' => 2, 'motif' => 'Repos', 'statut' => 'VALIDE', 'id_validateur' => 2, 'commentaire_validation' => 'Accord RH', 'date_creation' => "$year-02-20 09:00:00", 'date_decision' => "$year-02-21 09:00:00"],
            ['id_demande_conge' => 2, 'id_employe' => 1, 'id_type_conge' => 1, 'date_debut' => "$year-04-15", 'date_fin' => "$year-04-17", 'nombre_jours' => 3, 'motif' => 'Projet personnel', 'statut' => 'EN_ATTENTE', 'id_validateur' => null, 'commentaire_validation' => null, 'date_creation' => "$year-03-01 09:00:00", 'date_decision' => null],
        ]);
        $this->insert($pdo, 'justificatif_conge', [
            ['id_justificatif' => 1, 'id_demande_conge' => 1, 'nom_fichier' => 'demande-conge.pdf', 'type_mime' => 'application/pdf', 'donnees' => 'Justificatif de démonstration', 'date_depot' => "$year-02-20 09:00:00"],
        ]);
        $this->insert($pdo, 'absence', [
            ['id_absence' => 1, 'id_employe' => 1, 'date_debut' => "$year-05-05", 'date_fin' => "$year-05-05", 'type_absence' => 'MALADIE', 'motif' => 'Consultation médicale', 'justifiee' => 1, 'id_justificatif' => 1, 'cree_par' => 2, 'date_creation' => "$year-05-05 10:00:00"],
        ]);
        $this->insert($pdo, 'journal_acces', [
            ['id_journal' => 1, 'id_utilisateur' => 2, 'id_employe' => 1, 'action' => 'CONSULTATION', 'ressource' => 'dossier_employe', 'adresse_ip' => '127.0.0.1', 'details' => 'Consultation du dossier de démonstration', 'date_action' => "$year-02-01 08:10:00"],
        ]);
        $this->insert($pdo, 'notification', [
            ['id_notification' => 1, 'id_utilisateur' => 2, 'titre' => 'Nouvelle demande de congé', 'message' => 'Une demande est en attente de validation.', 'lien' => '/rh/conges', 'lue' => 0, 'date_creation' => "$year-03-01 09:01:00", 'date_lecture' => null],
        ]);
        $this->insert($pdo, 'utilisateur_departement', [['id_utilisateur' => 4, 'id_departement' => 2]]);
        $this->insert($pdo, 'condition_contrat', [
            ['id_condition' => 1, 'id_poste' => 2, 'libelle' => 'Période d’essai', 'type_valeur' => 'TEXTE', 'valeur_condition' => '3 mois', 'ordre_affichage' => 1, 'statut' => 'CONFIRMER', 'modifie_par' => 2, 'date_creation' => "$year-01-02 09:00:00", 'date_modification' => "$year-01-02 09:00:00"],
        ]);
        $this->insert($pdo, 'contrat_condition', [['id_contrat_condition' => 1, 'id_contrat' => 1, 'libelle' => 'Période d’essai', 'type_valeur' => 'TEXTE', 'valeur_finale' => '3 mois', 'ordre_affichage' => 1]]);
        $this->insert($pdo, 'requete_condition_contrat', [['id_requete' => 1, 'id_candidature' => 1, 'id_condition' => 1, 'valeur_proposee' => '3 mois', 'commentaire_candidat' => 'Accepté', 'statut' => 'ACCEPTEE_RH', 'date_creation' => '2025-01-06 09:00:00', 'date_modification' => '2025-01-06 09:00:00']]);
        $this->insert($pdo, 'validation_demande', [['id_validation' => 1, 'id_demande' => 1, 'id_validateur' => 3, 'type_validation' => 'DIRECTION', 'decision' => 'VALIDE', 'commentaire' => 'Besoin confirmé', 'date_decision' => '2025-01-03 10:00:00']]);
        $this->insert($pdo, 'validation_offre', [['id_validation_offre' => 1, 'id_offre' => 1, 'id_validateur' => 3, 'decision' => 'VALIDE', 'commentaire' => 'Publication autorisée', 'date_decision' => '2025-01-04 09:00:00']]);
        $this->insert($pdo, 'historique_candidature', [['id_historique' => 1, 'id_candidature' => 1, 'ancien_statut' => 'SOUMISE', 'nouveau_statut' => 'ACCEPTEE', 'id_utilisateur' => 2, 'commentaire' => 'Candidat retenu', 'date_action' => '2025-01-15 09:00:00']]);
        $this->insert($pdo, 'historique_demande', [['id_historique' => 1, 'id_demande' => 1, 'ancien_statut' => 'BROUILLON', 'nouveau_statut' => 'VALIDE', 'id_utilisateur' => 3, 'commentaire' => 'Demande approuvée', 'date_action' => '2025-01-03 10:00:00']]);
        $this->insert($pdo, 'historique_offre_emploi', [['id_historique' => 1, 'id_offre' => 1, 'ancien_statut' => 'BROUILLON', 'nouveau_statut' => 'FERMEE', 'id_utilisateur' => 2, 'commentaire' => 'Recrutement terminé', 'date_action' => '2025-02-01 08:00:00']]);
        $this->insert($pdo, 'historique_poste', [['id_historique' => 1, 'id_poste' => 2, 'ancien_statut' => 'INACTIF', 'nouveau_statut' => 'ACTIF', 'id_utilisateur' => 3, 'commentaire' => 'Poste ouvert', 'date_action' => '2025-01-03 09:00:00']]);
        $this->insert($pdo, 'historique_entretien', [['id_historique' => 1, 'id_entretien' => 1, 'ancien_statut' => 'PLANIFIE', 'nouveau_statut' => 'REALISE', 'id_utilisateur' => 2, 'commentaire' => 'Entretien réalisé', 'date_action' => '2025-01-10 12:00:00']]);
        $this->insert($pdo, 'historique_test_technique', [['id_historique' => 1, 'id_test' => 1, 'ancien_statut' => 'PLANIFIE', 'nouveau_statut' => 'REALISE', 'id_utilisateur' => 2, 'commentaire' => 'Test corrigé', 'date_action' => '2025-01-08 17:00:00']]);
        $this->insert($pdo, 'historique_condition_contrat', [['id_historique' => 1, 'id_condition' => 1, 'ancienne_valeur' => null, 'nouvelle_valeur' => '3 mois', 'ancien_statut' => 'CREER', 'nouveau_statut' => 'CONFIRMER', 'id_utilisateur' => 2, 'commentaire' => null, 'date_action' => '2025-01-06 09:00:00']]);
        $this->insert($pdo, 'historique_contrat', [['id_historique' => 1, 'id_contrat' => 1, 'ancien_statut' => 'BROUILLON', 'nouveau_statut' => 'SIGNE', 'id_utilisateur' => 2, 'commentaire' => 'Contrat signé', 'date_action' => '2025-02-01 08:00:00']]);
        $this->insert($pdo, 'historique_employe', [['id_historique' => 1, 'id_employe' => 1, 'ancien_statut' => 'INACTIF', 'nouveau_statut' => 'ACTIF', 'id_utilisateur' => 2, 'commentaire' => 'Embauche confirmée', 'date_action' => '2025-02-01 08:00:00']]);
        $this->insert($pdo, 'historique_requete_condition', [['id_historique' => 1, 'id_requete' => 1, 'ancien_statut' => 'SOUMISE', 'nouveau_statut' => 'ACCEPTEE_RH', 'id_utilisateur' => 2, 'commentaire' => null, 'date_action' => '2025-01-06 09:00:00']]);
        $this->insert($pdo, 'historique_formulaire_reponse', [['id_historique' => 1, 'id_utilisateur' => 2, 'type_formulaire' => 'employe', 'id_enregistrement' => 1, 'donnees' => '{"statut":"ACTIF"}', 'action' => 'CREATION', 'date_action' => '2025-02-01 08:00:00']]);
    }

    private function insert(\PDO $pdo, string $table, array $rows): void
    {
        if ($rows === []) {
            return;
        }
        $columns = array_keys($rows[0]);
        $fields = implode(', ', array_map(static fn (string $column): string => '"' . $column . '"', $columns));
        $parameters = implode(', ', array_fill(0, count($columns), '?'));
        $statement = $pdo->prepare("INSERT INTO \"$table\" ($fields) VALUES ($parameters)");
        foreach ($rows as $row) {
            $statement->execute(array_map(static fn (string $column): mixed => $row[$column] ?? null, $columns));
        }
    }

    private function clearTables(\PDO $pdo): void
    {
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")->fetchAll(\PDO::FETCH_COLUMN);
        $pdo->exec('PRAGMA foreign_keys = OFF');
        foreach ($tables as $table) {
            $pdo->exec('DELETE FROM "' . str_replace('"', '""', (string) $table) . '"');
        }
        if ($pdo->query("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'sqlite_sequence'")->fetchColumn()) {
            $pdo->exec('DELETE FROM sqlite_sequence');
        }
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    private function createDirectory(string $directory): void
    {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Impossible de créer le dossier writable.');
        }
    }
}
