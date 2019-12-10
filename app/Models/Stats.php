<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    protected $fillable = [
        'Type_Note',
        'Utilisateur',
        'Resultat_Appel',
        'Date_Nveau_RDV',
        'Heure_Nveau_RDV',
        'Marge_Nveau_RDV',
        'Id_Externe',
        'Date_Création',
        'Code_Postal_Site',
        'Drapeaux',
        'Code_Type_Intervention',
        'Date_Rdv',
        'Nom_Societe',
        'Nom_Region',
        'Nom_Domaine',
        'Nom_Agence',
        'Nom_Activite',
        'Date_Heure_Note',
        'Date_Heure_Note_Annee',
        'Date_Heure_Note_Mois',
        'Date_Heure_Note_Semaine',
        'Date_Note',
        'Groupement',
        'Gpmt_Appel_Pré',
        'Code_Intervention',
        'EXPORT_ALL_Nom_SITE',
        'EXPORT_ALL_Nom_TECHNICIEN',
        'EXPORT_ALL_PRENom_TECHNICIEN',
        'EXPORT_ALL_Nom_CLIENT',
        'EXPORT_ALL_Nom_EQUIPEMENT',
        'EXPORT_ALL_EXTRACT_CUI',
        'EXPORT_ALL_Date_CHARGEMENT_PDA',
        'EXPORT_ALL_Date_SOLDE',
        'EXPORT_ALL_Date_VALIDATION',
    ];
}
