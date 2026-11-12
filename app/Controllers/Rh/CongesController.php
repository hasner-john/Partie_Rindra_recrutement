<?php
namespace App\Controllers\Rh;

use App\Controllers\BaseController;
use App\Models\DemandeCongeModel;
use App\Models\JustificatifCongeModel;
use App\Models\AbsenceModel;
use App\Models\TypeCongeModel;
use App\Services\CongesService;

class CongesController extends BaseController
{
    public function index()
    {
        return view('rh/conges/index', [
            'demandes' => (new DemandeCongeModel())->avecDetails(),
            'types' => (new TypeCongeModel())->where('actif', 1)->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost([
            'id_employe', 'id_type_conge', 'date_debut', 'date_fin', 'nombre_jours', 'motif',
        ]);
        $id = (new CongesService())->creerDemande($data);
        if (!$id) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant, dates invalides ou chevauchement.');
        }
        $fichier = $this->request->getFile('justificatif');
        if ($fichier && $fichier->isValid() && !$fichier->hasMoved()) {
            (new JustificatifCongeModel())->insert([
                'id_demande_conge' => $id,
                'nom_fichier' => $fichier->getClientName(),
                'type_mime' => $fichier->getClientMimeType(),
                'donnees' => $fichier->getTempName() ? file_get_contents($fichier->getTempName()) : '',
            ]);
        }
        return redirect()->to('rh/conges')->with('success', 'Demande enregistrée.');
    }

    public function decision(int $id)
    {
        $statut = $this->request->getPost('statut');
        $user = (int) (session()->get('id_utilisateur') ?? 0);
        if (!(new CongesService())->decider($id, $statut, $user ?: null, (string) $this->request->getPost('commentaire'))) {
            return redirect()->back()->with('error', 'Décision impossible.');
        }
        return redirect()->to('rh/conges')->with('success', 'Décision enregistrée.');
    }

    public function absence()
    {
        $data = $this->request->getPost([
            'id_employe', 'date_debut', 'date_fin', 'type_absence', 'motif', 'justifiee',
        ]);
        if (!$data['id_employe'] || !$data['date_debut'] || !$data['date_fin'] || $data['date_fin'] < $data['date_debut']) {
            return redirect()->back()->with('error', 'Dates d’absence invalides.');
        }
        $data['justifiee'] = !empty($data['justifiee']) ? 1 : 0;
        $data['cree_par'] = (int) (session()->get('id_utilisateur') ?? 0) ?: null;
        (new AbsenceModel())->insert($data);
        return redirect()->to('rh/conges')->with('success', 'Absence enregistrée.');
    }
}
