<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application WIKI DATA
    |--------------------------------------------------------------------------
    |
    | This is a custom user file serves as a data source for the
    | application's wiki which provides information for every
    | request and/or query and it's condition (s) applied
    */

    'wiki_data' => [
        'global' => [
            'title' => 'Global',
            'specifications' => [
                'content' => [
                    0 => 'Exclusion des valeurs commençant par "="',
                ],
                'extra' => []
            ]

        ],
        'pages' => [
            0 => [
                'pageTitle' => 'Dashboard',
                'pageLink' => '/dashboard',
                'items' => [
                    0 => [
                        'title' => 'Résultats Appels',
                        'link' => '/dashboard#statsRegions',
                        'specifications' => [
                            'content' => [
                                0 => 'Exclusion des Groupement "appels Non Renseigné"',
                                1 => 'Exclusion des Groupement "appels Post"',
                                2 => 'Exclusion des Resultat_Appel "Notification par SMS"',
                                3 => 'Appels Entrants – Suite Envoi SMS'
                            ],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Répartition des dossiers traités par périmètre',
                        'link' => '/dashboard#statsFolders',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                                3 => 'Exclusion des Resultat_Appel "Notification par SMS"',
                                4 => 'Appels Entrants – Suite Envoi SMS'
                            ],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard#callsStatesAgencies',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard#callsStatesWeeks',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Code Interventions liés aux RDV Confirmés (Clients Joignables)',
                        'link' => '/dashboard#statsCallsPos',
                        'specifications' => [
                            'content' => [
                                0 => [
                                    'title' => 'Resultat_Appel inclus :',
                                    'values' => [
                                        0 => 'Appels préalables - RDV confirmé',
                                        1 => 'Appels préalables - RDV confirmé Client non informé',
                                        2 => 'Appels préalables - RDV repris et confirmé'
                                    ]
                                ]
                            ],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)',
                        'link' => '/dashboard#statsCallsNeg',
                        'specifications' => [
                            'content' => [
                                0 => [
                                    'title' => 'Resultat_Appel inclus :',
                                    'values' => [
                                        0 => 'Appels préalables - Annulation RDV client non informé',
                                        1 => 'Appels préalables - Client sauvé',
                                        2 => 'Appels préalables - Client Souhaite être rappelé plus tard',
                                        3 => 'Appels préalables - Injoignable / Absence de répondeur',
                                        4 => 'Appels préalables - Injoignable 2ème Tentative',
                                        5 => 'Appels préalables - Injoignable 3ème Tentative',
                                        6 => 'Appels préalables - Injoignable avec Répondeur',
                                        7 => 'Appels préalables - Numéro erroné',
                                        8 => 'Appels préalables - Numéro Inaccessible',
                                        9 => 'Appels préalables - Numéro non attribué',
                                        10 => 'Appels préalables - Numéro non Renseigné',
                                        11 => 'Appels préalables - RDV annulé le client ne souhaite plus d’intervention',
                                        12 => 'Appels préalables - RDV annulé Rétractation/Résiliation',
                                        13 => 'Appels préalables - RDV planifié mais non confirmé',
                                        14 => 'Appels préalables - RDV repris Mais non confirmé',
                                    ]
                                ]
                            ],
                            'extra' => []
                        ]
                    ],
                    6 => [
                        'title' => 'Répartition des dossiers non validés par Code Type intervention',
                        'link' => '/dashboard#statsFoldersByType',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    7 => [
                        'title' => 'Répartition des dossiers non validés par code intervention',
                        'link' => '/dashboard#statsFoldersByCode',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    8 => [
                        'title' => 'Production Globale CAM',
                        'link' => '/dashboard#statsPerimeters',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Type_Note "CAM"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                            ],
                            'extra' => []
                        ]
                    ],
                    9 => [
                        'title' => 'Délai de validation post solde',
                        'link' => '/dashboard#statsColturetech',
                        'specifications' => [
                            'content' => [
                                0 => 'La requête se base sur la différence (en minutes) entre EXPORT_ALL_Date_SOLDE et EXPORT_ALL_Date_VALIDATION',
                                1 => 'Exclure toutes les lignes avec un EXPORT_ALL_Date_SOLDE, EXPORT_ALL_Date_VALIDATION ou groupement vide.',
                                2 => 'Exclure toutes les lignes avec une différence de dates précitées compris entre 6h et 1 jours'
                            ],
                            'extra' => []
                        ]
                    ],
                    10 => [
                        'title' => 'Délai global de traitement OT',
                        'link' => '/dashboard#statsGlobalDelay',
                        'specifications' => [
                            'content' => [
                                0 => 'La requête se base sur la différence (en jours) entre Date_Creation et EXPORT_ALL_Date_VALIDATION',
                                1 => 'Exclure toutes les lignes avec un Date_Creation, EXPORT_ALL_Date_VALIDATION ou groupement vide.'
                            ],
                            'extra' => []
                        ]
                    ],
                    11 => [
                        'title' => 'Production d\'agent par groupement des appels',
                        'link' => '/dashboard#statsAgentProd',
                        'specifications' => [
                            'content' => [
                                0 => 'Liaison par l’utilisateur, année, mois, semaine. cette deniere est optionnel.',
                                1 => 'Exclusion de tous les groupement et les utilisateurs de valeur vides'
                            ],
                            'extra' => []
                        ]
                    ],
                ]
            ],
            1 => [
                'pageTitle' => 'Appel préalable',
                'pageLink' => '/dashboard/appels-pralables',
                'items' => [
                    0 => [
                        'title' => 'Résultats Appels Préalables',
                        'link' => '/dashboard/appels-pralables#statsCallsPrealable',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                                3 => 'Exclusion des Resultat_Appel "Notification par SMS"',
                                4 => 'Appels Entrants – Suite Envoi SMS'
                            ],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard/appels-pralables#callsStatesAgencies',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard/appels-pralables#callsStatesWeeks',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Code Interventions liés aux RDV Confirmés (Clients Joignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsPos',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => [
                                    'title' => 'Resultat_Appel inclus :',
                                    'values' => [
                                        0 => 'Appels préalables - RDV confirmé',
                                        1 => 'Appels préalables - RDV confirmé Client non informé',
                                        2 => 'Appels préalables - RDV repris et confirmé'
                                    ]
                                ]
                            ],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsNeg',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => [
                                    'title' => 'Resultat_Appel inclus :',
                                    'values' => [
                                        0 => 'Appels préalables - Annulation RDV client non informé',
                                        1 => 'Appels préalables - Client sauvé',
                                        2 => 'Appels préalables - Client Souhaite être rappelé plus tard',
                                        3 => 'Appels préalables - Injoignable / Absence de répondeur',
                                        4 => 'Appels préalables - Injoignable 2ème Tentative',
                                        5 => 'Appels préalables - Injoignable 3ème Tentative',
                                        6 => 'Appels préalables - Injoignable avec Répondeur',
                                        7 => 'Appels préalables - Numéro erroné',
                                        8 => 'Appels préalables - Numéro Inaccessible',
                                        9 => 'Appels préalables - Numéro non attribué',
                                        10 => 'Appels préalables - Numéro non Renseigné',
                                        11 => 'Appels préalables - RDV annulé le client ne souhaite plus d’intervention',
                                        12 => 'Appels préalables - RDV annulé Rétractation/Résiliation',
                                        13 => 'Appels préalables - RDV planifié mais non confirmé',
                                        14 => 'Appels préalables - RDV repris Mais non confirmé',
                                    ]
                                ],
                            ],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Global Résultat Appels Préalables',
                        'link' => '/dashboard/appels-pralables#CallResultPrealable',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels préalables"',
                                1 => [
                                    'title' => 'Resultat_Appel inclus :',
                                    'values' => [
                                        0 => 'Appels préalables - RDV confirmé',
                                        1 => 'Appels préalables - RDV confirmé Client non informé',
                                        2 => 'Appels préalables - RDV repris et confirmé',
                                        3 => 'Appels préalables - Annulation RDV client non informé',
                                        4 => 'Appels préalables - Client sauvé',
                                        5 => 'Appels préalables - Client Souhaite être rappelé plus tard',
                                        6 => 'Appels préalables - Injoignable / Absence de répondeur',
                                        7 => 'Appels préalables - Injoignable 2ème Tentative',
                                        8 => 'Appels préalables - Injoignable 3ème Tentative',
                                        9 => 'Appels préalables - Injoignable avec Répondeur',
                                        10 => 'Appels préalables - Numéro erroné',
                                        11 => 'Appels préalables - Numéro Inaccessible',
                                        12 => 'Appels préalables - Numéro non attribué',
                                        13 => 'Appels préalables - Numéro non Renseigné',
                                        14 => 'Appels préalables - RDV annulé le client ne souhaite plus d’intervention',
                                        15 => 'Appels préalables - RDV annulé Rétractation/Résiliation',
                                        16 => 'Appels préalables - RDV planifié mais non confirmé',
                                        17 => 'Appels préalables - RDV repris Mais non confirmé',
                                    ]

                                ]
                            ],
                            'extra' => []
                        ]
                    ],
                ],
            ],
            2 => [
                'pageTitle' => 'Clôture OT & Traitement BL',
                'pageLink' => '/dashboard/appels-clture',
                'items' => [
                    0 => [
                        'title' => 'Répartition des dossiers traités sur le périmètre validation, par catégorie de traitement',
                        'link' => '/dashboard/appels-clture#statsCallsCloture',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                                3 => 'Exclusion des Resultat_Appel "Notification par SMS"',
                                4 => 'Appels Entrants – Suite Envoi SMS'
                            ],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Répartition des dossiers non validés par Code Type intervention',
                        'link' => '/dashboard/appels-clture#statsFoldersByType',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Répartition des dossiers non validés par code intervention',
                        'link' => '/dashboard/appels-clture#statsFoldersByCode',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Délai de validation post solde',
                        'link' => '/dashboard/appels-clture#statsColturetech',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Délai global de traitement OT',
                        'link' => '/dashboard/appels-clture#statsGlobalDelay',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"'
                            ],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Résultat Validation par Type Intervention',
                        'link' => '/dashboard/appels-clture#statsValTypeIntervention',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"',
                                1 => [
                                    'title' => 'Resultat_Appel inclus',
                                    'values' => [
                                        'Appels clôture - Validé conforme',
                                        'Appels clôture - CRI non conforme'
                                    ]
                                ]
                            ],
                            'extra' => []
                        ]
                    ],
                    6 => [
                        'title' => 'Répartition Codes Intervention par Type Intervention',
                        'link' => '/dashboard/appels-clture#statsRepTypeIntervention',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels clôture"',
                            ],
                            'extra' => []
                        ]
                    ],

                ]
            ],
            3 => [
                'pageTitle' => 'Appel GEM',
                'pageLink' => '/dashboard/appels-gem',
                'items' => [
                    0 => [
                        'title' => 'Résultats Appels GEM',
                        'link' => '/dashboard/appels-gem#statsCallsGem',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels GEM"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                                3 => 'Exclusion des Resultat_Appel "Notification par SMS"',
                                4 => 'Appels Entrants – Suite Envoi SMS'
                            ],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard/appels-gem#callsStatesAgencies',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appel GEM"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard/appels-gem#callsStatesWeeks',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Groupement "Appels GEM"',
                                1 => 'Exclusion des Groupement "Non Renseigné"',
                                2 => 'Exclusion des Gpmt_Appel_Pre "Hors Périmètre"',
                            ],
                            'extra' => []
                        ]
                    ],

                ]
            ],
            4 => [
                'pageTitle' => 'Appel CAM',
                'pageLink' => '/dashboard/production_globale_cam',
                'items' => [
                    0 => [
                        'title' => 'Production Globale CAM',
                        'link' => '/dashboard/production_globale_cam#statsPerimeters',
                        'specifications' => [
                            'content' => [
                                0 => 'Inclusion des Type_Note "CAM"',
                                1 => 'Exclusion des Groupement "appels Non Renseigné"',
                                2 => 'Exclusion des Groupement "appels Post"',
                            ],
                            'extra' => []
                        ]
                    ]
                ]
            ],
            5 => [
                'pageTitle' => 'Traitement assistance',
                'pageLink' => '/dashboard/traitement-assistance',
                'items' => [
                    0 => [
                        'title' => 'Délai de traitement BF5 et BF8',
                        'link' => '/dashboard/traitement-assistance#statsProcessingDelay',
                        'specifications' => [
                            'content' => [
                                0 => [
                                    'title' => 'EXPORT_ALL_EXTRACT_CUI inclus',
                                    'values' => [
                                        0 => 'bf5',
                                        1 => 'bf8'
                                    ]
                                ]
                            ],
                            'extra' => []
                        ]
                    ]
                ]
            ]
        ],
        'pages_types' => [
            0 => [
                'title' => 'agent',
                'specifications' => [
                    'content' => [
                        0 => 'Inclusion les doublons'
                    ],
                    'extra' => []
                ]
            ],
            1 => [
                'title' => 'agence',
                'specifications' => [
                    'content' => [],
                    'extra' => []
                ]
            ]
        ]
    ]
];