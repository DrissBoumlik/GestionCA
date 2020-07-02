<?php

use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use App\Models\UserFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


if (!function_exists('sortGroupementColumnsPreserveKeys')) {
    function sortGroupementColumnsPreserveKeys($columns)
    {
        $sortWith = config('custom_params.groupement');
        $columns = $columns->sortBy(function ($item, $key) use ($sortWith) {
            return array_flip($sortWith)[$key];
        });
        return $columns;
    }
}

if (!function_exists('sortGroupementColumns')) {
    function sortGroupementColumns($columns)
    {
        $columns = $columns->all();
        $sortWith = config('custom_params.groupement');
        $columns = collect(array_intersect($sortWith, $columns));
        return $columns;
    }
}

if (!function_exists('getImportedData')) {
    function getImportedData($wantValue = false)
    {
        $user_flag = UserFlag::where('user_id', getAuthUser()->id)->first();
        $result = $user_flag ? ($wantValue ? $user_flag->flags : $user_flag) : -1;
        return $result;
//        return $wantValue ? ($user_flag ? $user_flag->flags['imported_data'] : 0) : $user_flag;
    }
}

if (!function_exists('checkColumnsExistence')) {
    function checkColumnsExistence($column)
    {
        $columns = [
            'Type_Note',
            'Utilisateur',
            'Resultat_Appel',
            'Date_Nveau_RDV',
            'Heure_Nveau_RDV',
            'Marge_Nveau_RDV',
            'Id_Externe',
            'Date_Creation',
            'Code_Postal_Site',
//                    'Departement' ,
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
            'key_Groupement',

            'Gpmt_Appel_Pre',
            'Code_Intervention',
            'EXPORT_ALL_Nom_SITE',
            'EXPORT_ALL_Nom_TECHNICIEN',
            'EXPORT_ALL_PRENom_TECHNICIEN',
//                    'EXPORT_ALL_Nom_CLIENT' ,
            'EXPORT_ALL_Nom_EQUIPEMENT',
            'EXPORT_ALL_EXTRACT_CUI',
            'EXPORT_ALL_Date_CHARGEMENT_PDA',
            'EXPORT_ALL_Date_SOLDE',
            'EXPORT_ALL_Date_VALIDATION',
        ];
        return in_array($column, $columns);
    }
}

if (!function_exists('replaceAccentedCharacter')) {
    function replaceAccentedCharacter($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        return str_replace($a, $b, $str);
    }
}

if (!function_exists('getStats')) {
    function getStats(Request $request)
    {
        $callType = $request->callType;
        $row = $request->row;
        $rowValue = $request->rowValue;
        $col = $request->col;
        $colValue = $request->colValue;
        $agentName = $request->agent;
        $agenceCode = $request->agence ?? $request->agence_code;
        $queryJoin = $request->queryJoin;

        $allStatsFilter = Filter::firstOrCreate(['user_id' => getAuthUser()->id, 'isGlobal' => 1]);
        if ($request->dates) {
            $dates = $request->dates;
//            $allStatsFilter = Filter::updateOrCreate([
//                'user_id' => getAuthUser()->id,
//                'date_filter' => explode(',', $dates),
//                'isGlobal' => 2
//            ]);
            $allStatsFilter->date_filter =  explode(',', $dates);
            $allStatsFilter->update();
        } else {
            if ($request->refreshMode && filter_var($request->refreshMode, FILTER_VALIDATE_BOOLEAN)) {
                $allStatsFilter->forceDelete();
                $dates = null;
            } else {
                if ($allStatsFilter && $allStatsFilter->date_filter) {
                    $dates = join(",", $allStatsFilter->date_filter);
                } else {
                    $dates = null;
                }
            }
        }

        $resultat_appel = $request->Resultat_Appel;
        $subGroupBy = $request->subGroupBy;
        $queryGroupBy = $request->queryGroupBy;
        $appCltquery = $request->appCltquery;
        $parentValue = $request->parentValue;

        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;
        $allStats = null;

        if ($appCltquery) {
            $allStats = DB::select('SELECT * FROM stats AS st WHERE Nom_Region is not null ' . ($queryJoin ?? '') . ' ' . ($parentValue ?? '') . ' ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                (($row && $rowValue && $row !== 'produit' && $row !== 'utilisateur') ? ' and ' . $row . ' like "%' . $rowValue . '%"' :
                    ($row && $rowValue ? ' and ' . $row . ' like "' . $rowValue . '"' : '')) .
                (!$row && $rowValue ? $rowValue : '') .
                ($col && !$colValue ? ' and ' . $col . ' is null ' : '') .
                ($col && $colValue && $col !== 'produit' && $col !== 'Gpmt_Appel_Pre' ? ' and ' . $col . ' like "%' . $colValue . '%"' :
                    ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '"' : '')) .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' and Date_Heure_Note_Mois = MONTH(NOW()) and Date_Heure_Note_Annee = YEAR(NOW())') .
                ($key_groupement ? ' and key_groupement like "' . $key_groupement . '"' : '') .
                ' and Resultat_Appel not like "=%"' .
                ' group by Id_Externe'
            );
        } else {
            $allStats = DB::select('SELECT * FROM stats AS st INNER JOIN (SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats  where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                (($row && $rowValue && $row !== 'Gpmt_Appel_Pre') ? ' and ' . $row . ' like "' . $rowValue . '%"' : ' ') .
                ($col && $colValue && $col !== 'Gpmt_Appel_Pre' ? ' and ' . $col . ' like "' . $colValue . '%"' : ($col && $col !== 'Gpmt_Appel_Pre' ? ' and ' . $col . ' is null ' : ' ')) .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' and Date_Heure_Note_Mois = MONTH(NOW()) and Date_Heure_Note_Annee = YEAR(NOW())') .
                ($key_groupement ? ' and key_groupement like "' . $key_groupement . '"' : '') .
                ' and Resultat_Appel not like "=%" ' .
                ($queryJoin ?? '') . ' ' . ($subGroupBy ?? ' GROUP BY Id_Externe ) groupedst')
                . ' on st.Id_Externe = groupedst.Id_Externe and st.Date_Heure_Note = groupedst.MaxDateTime where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                ($row && $rowValue ? ' and ' . $row . ' like "' . $rowValue . '%"' : ' ') .
                ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '%"' : ($col ? ' and ' . $col . ' is null ' : ' ')) .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' and Date_Heure_Note_Mois = MONTH(NOW()) and Date_Heure_Note_Annee = YEAR(NOW())') .
                ($key_groupement ? ' and key_groupement like "' . $key_groupement . '"' : '') .
                ' and Resultat_Appel not like "=%" ' .
                ($queryJoin ?? '') . ' ' . ($queryGroupBy ?? ' ')
            );
        }
        return $allStats;
    }
}

if (!function_exists('getMonthName')) {
    function getMonthName($index)
    {
        $Months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];
        return $Months[$index];
    }
}

if (!function_exists('makeFilterSubQuery')) {
    function makeFilterSubQuery(Request $request, $route, $column = null)
    {
        $user = getAuthUser();
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $filters = ['route' => $route, 'user_id' => $user->id, 'agence_name' => $agenceCode, 'agent_name' => $agentName, 'isGlobal' => null];


        $dates = $request->get('dates');
        $rowsFilter = $request->get('rowFilter');
        $currentMonth = date('Y-m') . '%';
        $filter = Filter::firstOrCreate($filters);
        $queryFilters = null;
        $filterSaved = false;
        if ($request->exists('refreshMode')) {
            if(!is_array($dates)){
                $dates = explode(',',$dates);
            }
            $dates = $dates ? array_values($dates) : null;
            $rowsFilter = $rowsFilter ? array_values($rowsFilter) : null;
            $filter->date_filter = $dates;
            $filter->rows_filter = $rowsFilter;
            if ($dates || $rowsFilter) {
                $filterSaved = true;
                $filter->save();
            } else {
                $filter->forceDelete();
                $filter = null;
            }
        } else {
            $globalFilter = Filter::where(['user_id' => $user->id, 'isGlobal' => true])->first();
            if ($globalFilter) {
                $filter->date_filter = $globalFilter->date_filter;
                $filter->save();
                $filterSaved = true;
            }
        }
        if ($filter && $filter->date_filter) {
            $queryFilters[] = 'Date_Note in ("' . join('","', $filter->date_filter) . '")';
        } else {
            $queryFilters[] = 'Date_Note like "' . $currentMonth . '"';
        }
        if ($column && $filter && $filter->rows_filter) {
            $queryFilters[] = $column . ' in ("' . join('","', $filter->rows_filter) . '")';
        }
        $queryFilters = join(' and ', $queryFilters);

        if ($filter && !$filter->exists && !$filterSaved) {
            $filter = null;
        }
        return [$filter, $queryFilters];
    }
}

if (!function_exists('applyFilter')) {
    function applyFilter($results, $filter, $column = null)
    {
        $currentMonth = date('Y-m') . '%';
        if ($column && $filter && $filter->rows_filter) {
            $results = $results->whereIn('st.' . $column, $filter->rows_filter);
        }
        if ($filter && $filter->date_filter) {
            $results = $results->whereIn('st.Date_Note', $filter->date_filter);
        } else {
            $results = $results->where('st.Date_Note', 'like', $currentMonth);
        }
        return $results;
    }
}

if (!function_exists('addRegionWithZero')) {
    function addRegionWithZero(Request $request, $regions, $columns, $column = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        //$resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $nomRegion = $request->get('nomRegion');
        $codeRdvInterventionConfirm = $request->get('codeRdvInterventionConfirm');
        $codeRdvIntervention = $request->get('codeRdvIntervention');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $gpmtAppelPre = $request->get('gpmtAppelPre');

        if ($nomRegion) {
            $groupmentColumns = Stats::select('Groupement')->distinct('Groupement')
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();
            if ($nomRegion) {
                foreach ($nomRegion as $gr) {
                    foreach ($groupmentColumns as $col) {
                        if (!in_array($col->Groupement, $regions->filter(function ($r) use ($gr) {
                            return $r->Nom_Region === $gr;
                        })->map(function ($r) {
                            return $r->Groupement;
                        })->toArray())) {
                            if ($col->Groupement) {
                                $rObj = new \stdClass();
                                $rObj->Groupement = $col->Groupement;
                                $rObj->Key_Groupement = $col->Key_Groupement;
                                $rObj->Nom_Region = $gr;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }
        if ($groupement || $codeIntervention || $codeTypeIntervention || $gpmtAppelPre) {
            $regionsColumns = Stats::select('Nom_Region')->distinct('Nom_Region')
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })
                ->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();

            if ($groupement) {
                foreach ($groupement as $gr) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($gr) {
                            return $r->Groupement === $gr;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Key_Groupement = $col->Key_Groupement;
                            $rObj->Groupement = $gr;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($codeTypeIntervention) {
                foreach ($codeTypeIntervention as $type) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($type) {
                            return $r->Code_Type_Intervention === $type;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Code_Type_Intervention = $type;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($codeIntervention) {
                foreach ($codeIntervention as $type) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($type) {
                            return $r->Code_Intervention === $type;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Code_Intervention = $type;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($column !== 'Date_Heure_Note_Semaine') {
                if ($gpmtAppelPre) {
                    $column = $column ?? 'Nom_Region';
                    foreach ($gpmtAppelPre as $gpm) {
                        foreach ($regionsColumns as $col) {
                            if (!in_array($col->$column, $regions->filter(function ($r) use ($gpm, $column) {
                                return $r->Gpmt_Appel_Pre === $gpm;
                            })->map(function ($r) use ($column) {
                                return $r->$column;
                            })->toArray())) {
                                $rObj = new \stdClass();
                                $rObj->$column = $col->$column;
                                $rObj->Gpmt_Appel_Pre = $gpm;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }
        if ($codeRdvInterventionConfirm || $codeRdvIntervention) {
            $codeColumns = Stats::select('Code_Intervention')->distinct('Code_Intervention')
                ->whereNotNull('Code_Intervention')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })
                ->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();

            $code = $codeRdvInterventionConfirm ? $codeRdvInterventionConfirm : $codeRdvIntervention;
            if ($code) {
                foreach ($code as $gr) {
                    foreach ($codeColumns as $col) {
                        if (!in_array($col->Code_Intervention, $regions->filter(function ($r) use ($gr) {
                            return $r->Nom_Region === $gr;
                        })->map(function ($r) {
                            return $r->Code_Intervention;
                        })->toArray())) {
                            if ($col->Nom_Region !== '') {
                                $rObj = new \stdClass();
                                $rObj->Nom_Region = $col->Nom_Region;
                                $rObj->Code_Intervention = $col->Code_Intervention;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }

        return $columns;
    }
}

if (!function_exists('sortWeeksDates')) {
    function sortWeeksDates($dates, $desc = false)
    {
        uksort($dates, function ($item1, $item2) use ($desc) {
            $date1 = explode('_', $item1);
            $date2 = explode('_', $item2);
            $year1 = $date1[1];
            $year2 = $date2[1];
            if ($year1 != $year2) {
                return ($year1 == $year2) ? 0 :
                    ($desc ? ($year1 < $year2 ? -1 : 1)
                        : ($year1 > $year2 ? -1 : 1));
            } else {
                $week1 = $date1[0];
                $week2 = $date2[0];
                return ($week1 == $week2) ? 0 :
                    ($desc ? ($week1 < $week2 ? -1 : 1)
                        : ($week1 > $week2 ? -1 : 1));
            }
        });
        return $dates;
    }
}

if (!function_exists('sortColumns')) {
    function sortColumns($columns, $desc = false)
    {
        usort($columns, function ($item1, $item2) use ($desc) {
            return ($item1->data == $item2->data) ? 0 :
                ($desc ? ($item1->data > $item2->data ? -1 : 1)
                    : ($item1->data < $item2->data ? -1 : 1));
        });
    }
}

if (!function_exists('getRoute')) {
    function getRoute($route)
    {
        $routeURI = $route->uri;
        $_index = 0;
        return collect($route->parameters)->reduce(function ($carry, $value) use (&$routeURI, &$_index, $route) {
            return $routeURI = str_replace('{' . $route->parameterNames[$_index++] . '}', $value, $routeURI);
        }, $routeURI);
    }
}

if (!function_exists('getRadicalRoute')) {
    function getRadicalRoute($route)
    {
        $radicalRoute = explode('/', getRoute($route));
        if (count($radicalRoute)) {
            return $radicalRoute[0];
        } else {
            return null;
        }
    }
}

if (!function_exists('getPicture')) {
    function getPicture($user = null)
    {
        $user = $user ?? auth()->user();
        $picturePath = Str::contains($user->picture, 'http') ? $user->picture : URL::to('/') . $user->picture;
        return $user->picture ? $picturePath : ($user->gender == 'male' ? '/storage/users/male.png' : '/storage/users/female.png');
    }
}

if (!function_exists('agencesList')) {
    function agencesList()
    {
        $agenceRepository = new \App\Repositories\StatsRepository();
        return $agenceRepository->getAgenciesAll();
    }
}

if (!function_exists('agentsList')) {
    function agentsList()
    {
        $agentRepository = new \App\Repositories\StatsRepository();
        return $agentRepository->getAgentsAll();
    }
}

if (!function_exists('fullName')) {
    function fullName($user, $char_slicer)
    {
        return ucfirst($user->firstname) . $char_slicer . ucfirst($user->lastname);
    }
}

if (!function_exists('assignedSkills')) {
    function assignedSkills($skills, $skill)
    {
        foreach ($skills as $skillItem) {
            if ($skillItem->id == $skill->id) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('assignedPermissions')) {
    function assignedPermission($permissions, $permission)
    {
        foreach ($permissions as $permissionItem) {
            if ($permissionItem->id == $permission->id)
                return true;
        }
        return false;
    }
}

if (!function_exists('fullImageUrl')) {
    function fullImageUrl($path)
    {
        return strpos($path, '//') ? $path : URL::to('/') . '/' . $path;
    }
}

if (!function_exists('pictures')) {
    function pictures()
    {
        return ['https://images2.imgbox.com/ae/76/pS8iBmkr_o.png',
            'https://images2.imgbox.com/7f/13/gFRcrjpl_o.png',
            'https://images2.imgbox.com/9a/a9/kCaSnMi1_o.png',
            'https://images2.imgbox.com/29/c1/P6QuLCTm_o.png',
            'https://images2.imgbox.com/af/db/FZVnDGxz_o.png',
            'https://images2.imgbox.com/78/a1/96M3aKXq_o.png',
            'https://images2.imgbox.com/46/e2/1RIG27Zs_o.png',
            'https://images2.imgbox.com/ae/fa/Cx2Oah56_o.png',
            'https://images2.imgbox.com/85/41/AQpTk0D0_o.png',
            'https://images2.imgbox.com/13/a2/aG2rQ4tK_o.png',
            'https://images2.imgbox.com/05/bc/YxQjPbDx_o.png',
            'https://images2.imgbox.com/c7/fa/Sfw0jxLd_o.png',
            'https://images2.imgbox.com/ba/44/2peQ5dpk_o.png',
            'https://images2.imgbox.com/89/db/qQ4XqwvC_o.png',
            'https://images2.imgbox.com/68/0f/TbhhmjH5_o.png',
            'https://images2.imgbox.com/57/77/2bWU44yq_o.png',
            'https://images2.imgbox.com/f7/ae/3hvnZvVg_o.png',
            'https://images2.imgbox.com/bd/39/l8fusalV_o.png',
            'https://images2.imgbox.com/18/b3/pAvfWo0O_o.png',
            'https://images2.imgbox.com/d1/ca/CMc9oKb4_o.png',
            'https://images2.imgbox.com/86/80/K8kAIYYm_o.png',
            'https://images2.imgbox.com/c4/52/cL99gk5E_o.png',
            'https://images2.imgbox.com/6f/05/El4zVDnh_o.png',
            'https://images2.imgbox.com/29/6a/x6DY12NI_o.png',
            'https://images2.imgbox.com/11/13/OusuH1QY_o.png',
            'https://images2.imgbox.com/18/a9/GKvX59i0_o.png',
            'https://images2.imgbox.com/25/3c/gNH2dITI_o.png',
            'https://images2.imgbox.com/7f/56/DKpDpzfU_o.png',
            'https://images2.imgbox.com/e8/1f/2MbvZCVX_o.png',
            'https://images2.imgbox.com/d6/e9/1X2FzL3z_o.png',
            'https://images2.imgbox.com/ee/87/aYEM6cg9_o.png',
            'https://images2.imgbox.com/d1/9c/HpCSwTUr_o.png',
            'https://images2.imgbox.com/a2/1c/a2GLIjnH_o.png',
            'https://images2.imgbox.com/2b/34/LRs2jOYx_o.png',
            'https://images2.imgbox.com/e8/a0/8DQUTkkn_o.png',
            'https://images2.imgbox.com/23/7e/0hxQRWE2_o.png',
            'https://images2.imgbox.com/f5/9d/9LLBsXzm_o.png',
            'https://images2.imgbox.com/3d/52/6QrNUhnL_o.png',
            'https://images2.imgbox.com/f0/67/M1FbDgqz_o.png',
            'https://images2.imgbox.com/94/ad/7X7paVBw_o.png',
            'https://images2.imgbox.com/96/aa/JzotNivF_o.png',
            'https://images2.imgbox.com/7b/92/VlguVA2h_o.png',
            'https://images2.imgbox.com/7c/46/OLEF6gZ3_o.png',
            'https://images2.imgbox.com/c2/3a/UOg8Ae2F_o.png',
            'https://images2.imgbox.com/f8/c7/pXDXrLwk_o.png',
            'https://images2.imgbox.com/4f/16/Ew3crQwp_o.png',
            'https://images2.imgbox.com/24/90/LEzYHLal_o.png',
            'https://images2.imgbox.com/7e/bb/PAIHBMe8_o.png',
            'https://images2.imgbox.com/64/7a/GswUJQ4p_o.png',
            'https://images2.imgbox.com/af/ca/yrvfCxbX_o.png',
            'https://images2.imgbox.com/6e/45/xLJ4y3ZH_o.png',
            'https://images2.imgbox.com/63/bf/FMCAouwq_o.png',
            'https://images2.imgbox.com/fa/62/wFzSfT01_o.png',
            'https://images2.imgbox.com/3f/4d/lb6MGvcZ_o.png',
            'https://images2.imgbox.com/97/78/vlNYBZoj_o.png',
            'https://images2.imgbox.com/2b/e0/PfDPEqCR_o.png',
            'https://images2.imgbox.com/0d/fa/tdM5gSOY_o.png',
            'https://images2.imgbox.com/b7/93/8VV0tQlQ_o.png',
            'https://images2.imgbox.com/72/9b/Oafp3NTu_o.png',
            'https://images2.imgbox.com/ad/d0/ejSkM41c_o.png',
            'https://images2.imgbox.com/f7/62/9decgZn4_o.png',
            'https://images2.imgbox.com/89/7b/QuYEJ1lH_o.png',
            'https://images2.imgbox.com/bc/df/6YNhcC3I_o.png',
            'https://images2.imgbox.com/51/25/cuMFYJkQ_o.png',
            'https://images2.imgbox.com/37/30/xAcqI2ll_o.png',
            'https://images2.imgbox.com/81/88/VEaPEhqB_o.png',
            'https://images2.imgbox.com/0a/29/70VRrU7x_o.png',
            'https://images2.imgbox.com/b0/2a/3DK9QhKq_o.png',
            'https://images2.imgbox.com/27/5b/1NSDhN2s_o.png',
            'https://images2.imgbox.com/61/44/cP70vInn_o.png',
            'https://images2.imgbox.com/79/77/5SnT7bsr_o.png',
            'https://images2.imgbox.com/f4/0a/4mJxpJGP_o.png',
            'https://images2.imgbox.com/4b/49/e2vySEN0_o.png',
            'https://images2.imgbox.com/c5/91/IjQjGrJ0_o.png',
            'https://images2.imgbox.com/a1/85/BvWh7coO_o.png',
            'https://images2.imgbox.com/35/91/mNWwVUN9_o.png',
            'https://images2.imgbox.com/d0/58/EWaZtdzg_o.png',
            'https://images2.imgbox.com/c8/77/6z9Z8Uj7_o.png',
            'https://images2.imgbox.com/5e/22/JJPkz4AS_o.png',
            'https://images2.imgbox.com/18/9e/Ya3XQsW8_o.png',
            'https://images2.imgbox.com/54/3f/JELDFqyq_o.png',
            'https://images2.imgbox.com/cd/a2/tyahzbTO_o.png',
            'https://images2.imgbox.com/60/f1/0G8Hc8Aq_o.png',
            'https://images2.imgbox.com/d9/0c/G5Wash7V_o.png',
            'https://images2.imgbox.com/2b/be/VrvLPRBd_o.png',
            'https://images2.imgbox.com/75/b0/NyZk9Glf_o.png',
            'https://images2.imgbox.com/9a/55/meQbi4rp_o.png',
            'https://images2.imgbox.com/d9/a0/DUpccg32_o.png',
            'https://images2.imgbox.com/db/02/f8zM7Gp0_o.png',
            'https://images2.imgbox.com/b2/27/hPH0rXdJ_o.png',
            'https://images2.imgbox.com/30/c3/jUcUKUfz_o.png',
            'https://images2.imgbox.com/66/c0/aBYN9M1K_o.png',
            'https://images2.imgbox.com/6e/ad/eA2c7fsT_o.png',
            'https://images2.imgbox.com/3a/0b/lT70yYvI_o.png',
            'https://images2.imgbox.com/b0/0c/15nNJq8h_o.png',
            'https://images2.imgbox.com/0a/21/cbxTPMj3_o.png',
            'https://images2.imgbox.com/90/85/lm2Tmmqd_o.png',
            'https://images2.imgbox.com/29/8b/xCpK19mP_o.png',
            'https://images2.imgbox.com/8e/a5/ItFSmNSV_o.png',
            'https://images2.imgbox.com/92/8c/s0VZjlwI_o.png',
            'https://images2.imgbox.com/5e/4b/41HTmKCj_o.png',
            'https://images2.imgbox.com/40/33/M1gjtGAT_o.png',
            'https://images2.imgbox.com/8e/dc/x6kfnPmN_o.png',
            'https://images2.imgbox.com/68/2a/XpaUWINA_o.png',
            'https://images2.imgbox.com/93/3a/DEPvYe1x_o.png',
            'https://images2.imgbox.com/82/91/uNtwQVfJ_o.png',
            'https://images2.imgbox.com/46/05/k612n6eq_o.png',
            'https://images2.imgbox.com/e9/50/pAbfe5hW_o.png',
            'https://images2.imgbox.com/a9/9d/fWXdDTt4_o.png',
            'https://images2.imgbox.com/79/7f/wXtXk1J4_o.png',
            'https://images2.imgbox.com/1d/c3/rPt7ZJKe_o.png',
            'https://images2.imgbox.com/56/10/58FPBaZ9_o.png',
            'https://images2.imgbox.com/4c/80/RCih5oQP_o.png',
            'https://images2.imgbox.com/87/ee/sPhOO4Dd_o.png',
            'https://images2.imgbox.com/5c/9d/9suZzZPZ_o.png',
            'https://images2.imgbox.com/74/13/xyFZub2q_o.png',
            'https://images2.imgbox.com/b8/8c/QXJtUa1O_o.png',
            'https://images2.imgbox.com/fa/9a/da1CMOLA_o.png',
            'https://images2.imgbox.com/e5/8b/v5tZ6xe0_o.png',
            'https://images2.imgbox.com/be/34/txTJhwkk_o.png',
            'https://images2.imgbox.com/b3/73/dFLIHgJd_o.png',
            'https://images2.imgbox.com/13/98/s5emY8bR_o.png',
            'https://images2.imgbox.com/e5/44/Gb5OKzHx_o.png',
            'https://images2.imgbox.com/6b/c5/6wz9tnBF_o.png',
            'https://images2.imgbox.com/0a/c3/kORBpI8n_o.png',
            'https://images2.imgbox.com/e7/19/krVAAMjd_o.png',
            'https://images2.imgbox.com/1e/65/suComlWL_o.png',
            'https://images2.imgbox.com/2a/d2/WXT1ILdE_o.png',
            'https://images2.imgbox.com/61/d2/6E4EvA5K_o.png',
            'https://images2.imgbox.com/6a/d0/2Croa9dw_o.png',
            'https://images2.imgbox.com/16/97/nibg2lOh_o.png',
            'https://images2.imgbox.com/89/3f/jtUdM6fm_o.png',
            'https://images2.imgbox.com/7d/b0/GDA8VUq3_o.png',
            'https://images2.imgbox.com/de/26/hVe8EO3B_o.png',
            'https://images2.imgbox.com/dc/ed/sZeUhGL3_o.png',
            'https://images2.imgbox.com/43/7f/8QLPjyma_o.png',
            'https://images2.imgbox.com/83/a7/X9RxTc9M_o.png',
            'https://images2.imgbox.com/c6/03/i6Fz35CD_o.png',
            'https://images2.imgbox.com/c3/ce/hOKZGRq4_o.png',
            'https://images2.imgbox.com/bc/90/zDa2iVpJ_o.png',
            'https://images2.imgbox.com/f1/13/DjWxgWmv_o.png',
            'https://images2.imgbox.com/b9/2e/TPjcPNXQ_o.png',
            'https://images2.imgbox.com/d5/ed/7tPXGYT5_o.png',
            'https://images2.imgbox.com/1f/48/HfYSpBtR_o.png',
            'https://images2.imgbox.com/8c/0a/pii9SYv9_o.png',
            'https://images2.imgbox.com/89/a9/SHO4Ygga_o.png',
            'https://images2.imgbox.com/7b/15/V1LlZ48o_o.png',
            'https://images2.imgbox.com/52/56/cke7i0zW_o.png',
            'https://images2.imgbox.com/ca/ac/jrCVc5ZG_o.png',
            'https://images2.imgbox.com/c2/96/uOEsSvxD_o.png',
            'https://images2.imgbox.com/17/e0/UU0DB6TK_o.png',
            'https://images2.imgbox.com/ab/98/7kHG15az_o.png',
            'https://images2.imgbox.com/6d/8a/Fl4jxtUI_o.png',
            'https://images2.imgbox.com/4d/a5/vBrlA3ne_o.png',
            'https://images2.imgbox.com/d9/49/NeYkUVvl_o.png',
            'https://images2.imgbox.com/61/ae/E0QdqwTd_o.png',
            'https://images2.imgbox.com/98/e5/TmLoqrCU_o.png',
            'https://images2.imgbox.com/b2/1a/jZuWb0LI_o.png',
            'https://images2.imgbox.com/59/10/5fQoLTxD_o.png',
            'https://images2.imgbox.com/67/85/kV3Gd3Lq_o.png',
            'https://images2.imgbox.com/47/91/Uk42H3V2_o.png',
            'https://images2.imgbox.com/bc/ce/JmXfKOi3_o.png',
            'https://images2.imgbox.com/53/bf/0MmUJVPA_o.png',
            'https://images2.imgbox.com/9c/5e/F8JZCJLn_o.png',
            'https://images2.imgbox.com/6c/89/E2lerhza_o.png',
            'https://images2.imgbox.com/6e/f6/af3HRf8l_o.png',];
    }
}

if (!function_exists('clean')) {
    function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}

if (!function_exists('getAuthUser')) {
    function getAuthUser()
    {
        return auth()->user();
    }
}

if (!function_exists('isInAdminGroup')) {
    function isInAdminGroup($user = null)
    {
        $roleName = ($user ?? auth()->user())->role->name;
        return $roleName == 'superAdmin' || $roleName == 'admin';
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin($user = null)
    {
        return ($user ?? auth()->user())->role->name == 'superAdmin';
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin($user = null)
    {
        return ($user ?? auth()->user())->role->name == 'admin';
    }
}
if (!function_exists('isAgency')) {
    function isAgency()
    {
        return ($user ?? auth()->user())->role->name == 'Agence';
    }
}
if (!function_exists('isAgent')) {
    function isAgent()
    {
        return ($user ?? auth()->user())->role->name == 'Agent';
    }
}
