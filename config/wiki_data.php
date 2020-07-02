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
        'pages' => [
            0 => [
                'pageTitle' => 'Dashboard',
                'pageLink' => '/dashboard',
                'items' => [
                    0 => [
                        'title' => 'Résultats Appels',
                        'link' => '/dashboard#statsRegions',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Répartition des dossiers traités par périmètre',
                        'link' => '/dashboard#statsFolders',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard#callsStatesAgencies',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard#callsStatesWeeks',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Code Interventions liés aux RDV Confirmés (Clients Joignables)',
                        'link' => '/dashboard#statsCallsPos',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)',
                        'link' => '/dashboard#statsCallsNeg',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    6 => [
                        'title' => 'Répartition des dossiers non validés par Code Type intervention',
                        'link' => '/dashboard#statsFoldersByType',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    7 => [
                        'title' => 'Répartition des dossiers non validés par code intervention',
                        'link' => '/dashboard#statsFoldersByCode',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    8 => [
                        'title' => 'Production Globale CAM',
                        'link' => '/dashboard#statsPerimeters',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    9 => [
                        'title' => 'Délai de validation post solde',
                        'link' => '/dashboard#statsColturetech',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    10 => [
                        'title' => 'Délai global de traitement OT',
                        'link' => '/dashboard#statsGlobalDelay',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    11 => [
                        'title' => 'Production d\'agent par groupement des appels',
                        'link' => '/dashboard#statsAgentProd',
                        'specifications' => [
                            'content' => [],
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
                            'content' => [],
                            'extra' => []
                        ]
                    ],

                    1 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard/appels-pralables#callsStatesAgencies',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard/appels-pralables#callsStatesWeeks',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Code Interventions liés aux RDV Confirmés (Clients Joignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsPos',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsNeg',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Global Résultat Appels Préalables',
                        'link' => '/dashboard/appels-pralables#CallResultPrealable',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                ]
            ],
            2 => [
                'pageTitle' => 'Clôture OT & Traitement BL',
                'pageLink' => '/dashboard/appels-clture',
                'items' => [
                    0 => [
                        'title' => 'Répartition des dossiers traités sur le périmètre validation, par catégorie de traitement',
                        'link' => '/dashboard/appels-clture#statsCallsCloture',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Répartition des dossiers non validés par Code Type intervention',
                        'link' => '/dashboard/appels-clture#statsFoldersByType',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Répartition des dossiers non validés par code intervention',
                        'link' => '/dashboard/appels-clture#statsFoldersByCode',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    3 => [
                        'title' => 'Délai de validation post solde',
                        'link' => '/dashboard/appels-clture#statsColturetech',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    4 => [
                        'title' => 'Délai global de traitement OT',
                        'link' => '/dashboard/appels-clture#statsGlobalDelay',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    5 => [
                        'title' => 'Résultat Validation par Type Intervention',
                        'link' => '/dashboard/appels-clture#statsValTypeIntervention',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    6 => [
                        'title' => 'Répartition Codes Intervention par Type Intervention',
                        'link' => '/dashboard/appels-clture#statsRepTypeIntervention',
                        'specifications' => [
                            'content' => [],
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
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    1 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard/appels-gem#callsStatesAgencies',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ],
                    2 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard/appels-gem#callsStatesWeeks',
                        'specifications' => [
                            'content' => [],
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
                            'content' => [],
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
                            'content' => [], 'extra' => []
                        ]
                    ]
                ]
            ]

        ]
    ]
    //                    2 => [
//                        'specifications' => [
//                            'content' => [],
//                            'extra' => []
//                        ]
//                    ],
];