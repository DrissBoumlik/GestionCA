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
                'pageTitle' => 'dashboard',
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
                            'content' => '',
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
                            'content' => '',
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
                            'content' => '',
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
                            'content' => '',
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
                            'content' => '',
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
                            'content' => '',
                            'extra' => []
                        ]
                    ],
                ]
            ],
            1 => [
                'pageTitle' => 'Appel préalable',
                'items' => [
                    0 => [
                        'title' => 'Résultats Appels Préalables',
                        'link' => '/dashboard/appels-pralables#statsCallsPrealable',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ]
                    ,

                    1 => [
                        'title' => 'Résultats Appels Préalables par agence',
                        'link' => '/dashboard/appels-pralables#callsStatesAgencies',
                        'specifications' => [
                            'content' => '',
                            'extra' => []
                        ]
                    ]
                    ,

                    2 => [
                        'title' => 'Résultats Appels Préalables par semaine',
                        'link' => '/dashboard/appels-pralables#callsStatesWeeks',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ]
                    ,

                    3 => [
                        'title' => 'Code Interventions liés aux RDV Confirmés (Clients Joignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsPos',
                        'specifications' => [
                            'content' => '',
                            'extra' => []
                        ]
                    ]
                    ,

                    4 => [
                        'title' => 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)',
                        'link' => '/dashboard/appels-pralables#statsCallsNeg',
                        'specifications' => [
                            'content' => [],
                            'extra' => []
                        ]
                    ]
                    ,

                    5 => [
                        'title' => 'Global Résultat Appels Préalables',
                        'link' => '/dashboard/appels-pralables#CallResultPrealable',
                        'specifications' => [
                            'content' => '',
                            'extra' => []
                        ]
                    ]
                    ,
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
