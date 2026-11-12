<?php

namespace App\Models;

use CodeIgniter\Model;

class DemandeModel extends Model
{
    protected $table = 'demande_recrutement';
    protected $primaryKey = 'id_demande';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_poste',
        'id_demandeur',
        'effectif_demande',
        'motif',
        'salaire_par_personne',
        'date_besoin',
        'statut',
        'date_modification'
    ];

    protected $useTimestamps = false;

    // Recuperer toutes les demandes avec le poste,
    // le departement et le nom du demandeur.
    public function getDemandesAvecDetails()
    {
        return $this->db->table('demande_recrutement dr')
            ->select('
                dr.id_demande,
                dr.effectif_demande,
                dr.motif,
                dr.salaire_par_personne,
                dr.date_besoin,
                dr.statut,
                dr.date_creation,
                p.libelle AS poste,
                d.nom AS departement,
                pe.nom AS nom_demandeur,
                pe.prenom AS prenom_demandeur
            ')
            ->join('poste p', 'p.id_poste = dr.id_poste')
            ->join('departement d', 'd.id_departement = p.id_departement')
            ->join('utilisateur u', 'u.id_utilisateur = dr.id_demandeur')
            ->join(
                'personne pe',
                'pe.id_utilisateur = u.id_utilisateur',
                'left'
            )
            ->orderBy('dr.date_creation', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Recuperer une demande precise.
    public function getDemandeAvecDetails($id)
    {
        return $this->db->table('demande_recrutement dr')
            ->select('
                dr.*,
                p.libelle AS poste,
                p.description AS description_poste,
                d.nom AS departement,
                pe.nom AS nom_demandeur,
                pe.prenom AS prenom_demandeur,
                u.email AS email_demandeur
            ')
            ->join('poste p', 'p.id_poste = dr.id_poste')
            ->join('departement d', 'd.id_departement = p.id_departement')
            ->join('utilisateur u', 'u.id_utilisateur = dr.id_demandeur')
            ->join(
                'personne pe',
                'pe.id_utilisateur = u.id_utilisateur',
                'left'
            )
            ->where('dr.id_demande', $id)
            ->get()
            ->getRowArray();
    }

    public function updateStatutDemande($id, $statut)
    {
        return $this->update($id, ['statut' => $statut]);
    }
}
