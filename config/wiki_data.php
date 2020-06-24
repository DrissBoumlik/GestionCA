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
        0 => [
            "method" => "App\Repositories\StatsRepository::getStats",
            "params" => []
        ],
        1 => [
            "method" => "App\Repositories\StatsRepository::getAgencies",
            "params" => []
        ],
        2 => [
            "method" => "App\Repositories\StatsRepository::getAgenciesAll",
            "params" => []
        ],
        3 => [
            "method" => "App\Repositories\StatsRepository::getAgents",
            "params" => []
        ],
        4 => [
            "method" => "App\Repositories\StatsRepository::getAgentsAll",
            "params" => []
        ],
        5 => [
            "method" => "App\Repositories\StatsRepository::filterList",
            "params" => []
        ],
        6 => [
            "method" => "App\Repositories\StatsRepository::getDateNotes",
            "params" => []
        ],
        7 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRegions",
            "params" => []
        ],
        8 => [
            "method" => "App\Repositories\StatsRepository::GetDataRegions",
            "params" => []
        ],
        9 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRegionsByGrpCall",
            "params" => []
        ],
        10 => [
            "method" => "App\Repositories\StatsRepository::GetDataRegionsByGrpCall",
            "params" => []
        ],
        11 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRegionsCallState",
            "params" => []
        ],
        12 => [
            "method" => "App\Repositories\StatsRepository::GetDataRegionsCallState",
            "params" => []
        ],
        13 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsClientsByCallState",
            "params" => []
        ],
        14 => [
            "method" => "App\Repositories\StatsRepository::GetDataClientsByCallState",
            "params" => []
        ],
        15 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsNonValidatedFolders",
            "params" => []
        ],
        16 => [
            "method" => "App\Repositories\StatsRepository::GetDataNonValidatedFolders",
            "params" => []
        ],
        17 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsClientsByPerimeter",
            "params" => []
        ],
        18 => [
            "method" => "App\Repositories\StatsRepository::GetDataClientsByPerimeter",
            "params" => []
        ],
        19 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsClientsWithCallStates",
            "params" => []
        ],
        20 => [
            "method" => "App\Repositories\StatsRepository::GetDataClientsWithCallStates",
            "params" => []
        ],
        21 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsCloturetechCall",
            "params" => []
        ],
        22 => [
            "method" => "App\Repositories\StatsRepository::GetCloturetechCall",
            "params" => []
        ],
        23 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsGlobalDelayCall",
            "params" => []
        ],
        24 => [
            "method" => "App\Repositories\StatsRepository::GetGlobalDelayCall",
            "params" => []
        ],
        25 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsProcessingDelayCall",
            "params" => []
        ],
        26 => [
            "method" => "App\Repositories\StatsRepository::GetProcessingDelayCall",
            "params" => []
        ],
        27 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsTypeIntervention",
            "params" => []
        ],
        28 => [
            "method" => "App\Repositories\StatsRepository::getTypeIntervention",
            "params" => []
        ],
        29 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsTypeInterventionGrpCall",
            "params" => []
        ],
        30 => [
            "method" => "App\Repositories\StatsRepository::getTypeInterventionGrpCall",
            "params" => []
        ],
        31 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsValTypeIntervention",
            "params" => []
        ],
        32 => [
            "method" => "App\Repositories\StatsRepository::getValTypeIntervention",
            "params" => []
        ],
        33 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsValTypeInterventionGrpCall",
            "params" => []
        ],
        34 => [
            "method" => "App\Repositories\StatsRepository::getValTypeInterventionGrpCall",
            "params" => []
        ],
        35 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepTypeIntervention",
            "params" => []
        ],
        36 => [
            "method" => "App\Repositories\StatsRepository::getRepTypeIntervention",
            "params" => []
        ],
        37 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepTypeInterventionGrpCall",
            "params" => []
        ],
        38 => [
            "method" => "App\Repositories\StatsRepository::getRepTypeInterventionGrpCall",
            "params" => []
        ],
        39 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepJoiDepartement",
            "params" => []
        ],
        40 => [
            "method" => "App\Repositories\StatsRepository::getRepJoiDepartement",
            "params" => []
        ],
        41 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepJoiDepartementGrpCall",
            "params" => []
        ],
        42 => [
            "method" => "App\Repositories\StatsRepository::getRepJoiDepartementGrpCall",
            "params" => []
        ],
        43 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepJoiAutreDepartement",
            "params" => []
        ],
        44 => [
            "method" => "App\Repositories\StatsRepository::getRepJoiAutreDepartement",
            "params" => []
        ],
        45 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsRepJoiAutreDepartementGrpCall",
            "params" => []
        ],
        46 => [
            "method" => "App\Repositories\StatsRepository::getRepJoiAutreDepartementGrpCall",
            "params" => []
        ],
        47 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsGlobalView",
            "params" => []
        ],
        48 => [
            "method" => "App\Repositories\StatsRepository::GetDataGlobalView",
            "params" => []
        ],
        49 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsGlobalViewDetails",
            "params" => []
        ],
        50 => [
            "method" => "App\Repositories\StatsRepository::GetDataGlobalViewDetails",
            "params" => []
        ],
        51 => [
            "method" => "App\Repositories\StatsRepository::GetColumnsgetAgentProd",
            "params" => []
        ],
        52 => [
            "method" => "App\Repositories\StatsRepository::getAgentProd",
            "params" => []
        ],
        53 => [
            "method" => "App\Repositories\StatsRepository::importStats",
            "params" => []
        ],
        54 => [
            "method" => "App\Repositories\StatsRepository::exportXlsCall",
            "params" => []
        ],
        55 => [
            "method" => "App\Repositories\StatsRepository::agentProdExportCall",
            "params" => []
        ],

    ]
];
