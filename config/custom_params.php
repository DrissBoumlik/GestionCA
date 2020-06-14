<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Custom Parameters
    |--------------------------------------------------------------------------
    |
    | This is a user custom file where you can store values to be used
    | as parameters for the application globally and can / will be
    | changed later to be stored in a separate table in the db
    */

    'MAX_TOP_SKILLS' => 4,
    'groupement' => [
        'Appels préalables',
        'Appels clôture',
        'Appels GEM',
        'Traitement/Assistance CA',
        'AE Contrôle IH',
        'AE Contre Appel'
    ],
    'agents_production' => [
        'data' => [
            'amount' => 0.425,
            'currency' => '€',
            'exceptions' => [
                'data' => [
                    'amount' => 1.7,
                    'currency' => '€',
                    'agents' => [
                        '0' => [
                            'pseudo' => 'SBARZOUKI',
                            'nom_complet' => 'BARZOUKI SALWA'
                        ],
                        '1' => [
                            'pseudo' => 'DCHKAIRI',
                            'nom_complet' => 'CHKAIRI DOUNIA'
                        ],
                        '2' => [
                            'pseudo' => 'ACHAFIK',
                            'nom_complet' => 'CHAFIK ASMAA'
                        ],
                        '3' => [
                            'pseudo' => 'SELAFDEL',
                            'nom_complet' => 'EL-AFDEL SOPHIA'
                        ],
                        '4' => [
                            'pseudo' => 'KTAOUIL',
                            'nom_complet' => 'TAOUIL KENZA'
                        ]
                    ]
                ]
            ]
        ],
    ]
];
