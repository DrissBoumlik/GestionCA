$(document).ready(function () {
    $('#stats').DataTable({
        language: frLang,
        pageLength: 100,
        processing: true,
        serverSide: true,
        type: 'POST',
        ajax: {
            type: 'POST',
            url: `stats/get-stats`
        },
        columns: [
            {
                data: 'Type_Note',
                name: 'Type_Note'
            },
            {
                data: 'Utilisateur',
                name: 'Utilisateur'
            },
            {
                data: 'Resultat_Appel',
                name: 'Resultat_Appel'
            },
            {
                data: 'Date_Nveau_RDV',
                name: 'Date_Nveau_RDV'
            },
            {
                data: 'Heure_Nveau_RDV',
                name: 'Heure_Nveau_RDV'
            },
            {
                data: 'Marge_Nveau_RDV',
                name: 'Marge_Nveau_RDV'
            },
            {
                data: 'Id_Externe',
                name: 'Id_Externe'
            },
            {
                data: 'Date_Creation',
                name: 'Date_Creation'
            },
            {
                data: 'Code_Postal_Site',
                name: 'Code_Postal_Site'
            },
            {
                data: 'Drapeaux',
                name: 'Drapeaux'
            },
            {
                data: 'Code_Type_Intervention',
                name: 'Code_Type_Intervention'
            },
            {
                data: 'Date_Rdv',
                name: 'Date_Rdv'
            },
            {
                data: 'Nom_Societe',
                name: 'Nom_Societe'
            },
            {
                data: 'Nom_Region',
                name: 'Nom_Region'
            },
            {
                data: 'Nom_Domaine',
                name: 'Nom_Domaine'
            },
            {
                data: 'Nom_Agence',
                name: 'Nom_Agence'
            },
            {
                data: 'Nom_Activite',
                name: 'Nom_Activite'
            },
            {
                data: 'Date_Heure_Note',
                name: 'Date_Heure_Note'
            },
            {
                data: 'Date_Heure_Note_Annee',
                name: 'Date_Heure_Note_Annee'
            },
            {
                data: 'Date_Heure_Note_Mois',
                name: 'Date_Heure_Note_Mois'
            },
            {
                data: 'Date_Heure_Note_Semaine',
                name: 'Date_Heure_Note_Semaine'
            },
            {
                data: 'Date_Note',
                name: 'Date_Note'
            },
            {
                data: 'Groupement',
                name: 'Groupement'
            },
            {
                data: 'key_Groupement',
                name: 'key_Groupement'
            },
            {
                data: 'Gpmt_Appel_Pre',
                name: 'Gpmt_Appel_Pre'
            },
            {
                data: 'Code_Intervention',
                name: 'Code_Intervention'
            },
            {
                data: 'EXPORT_ALL_Nom_SITE',
                name: 'EXPORT_ALL_Nom_SITE'
            },
            {
                data: 'EXPORT_ALL_Nom_TECHNICIEN',
                name: 'EXPORT_ALL_Nom_TECHNICIEN'
            },
            {
                data: 'EXPORT_ALL_PRENom_TECHNICIEN',
                name: 'EXPORT_ALL_PRENom_TECHNICIEN'
            },
            {
                data: 'EXPORT_ALL_Nom_EQUIPEMENT',
                name: 'EXPORT_ALL_Nom_EQUIPEMENT'
            },
            {
                data: 'EXPORT_ALL_EXTRACT_CUI',
                name: 'EXPORT_ALL_EXTRACT_CUI'
            },
            {
                data: 'EXPORT_ALL_Date_CHARGEMENT_PDA',
                name: 'EXPORT_ALL_Date_CHARGEMENT_PDA'
            },
            {
                data: 'EXPORT_ALL_Date_SOLDE',
                name: 'EXPORT_ALL_Date_SOLDE'
            },
            {
                data: 'EXPORT_ALL_Date_VALIDATION',
                name: 'EXPORT_ALL_Date_VALIDATION'
            }
        ]
    });
});
