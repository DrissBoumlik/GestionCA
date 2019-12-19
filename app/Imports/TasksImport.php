<?php

namespace App\Imports;

use Exception;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Task;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TasksImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Task
     */
    public function model(array $row)
    {
//        dd($row);
        return new Task([
            'date_reception_demande' => $this->transformDate($row['date_de_reception_de_la_demande']),
            'operateur' => $row['operateur'],
            'code_projet_operateur' => $row['code_projet_operateur'],
            'cdp_operateur' => $row['cdp_operateur'],
            'agence' => $row['agence'],
            'cdp_circet' => $row['cdp_circet'],
            'otc_uo' => $row['otc_uo'],
            'code_site' => $row['code_site'],
            'patrimoine' => $row['patrimoine'],
            'site_b' => $row['site_b'],
            'cle' => $row['cle'],
            'type_op' => $row['type_dop'],
            'type_support' => $row['type_support'],
            'conf' => $row['conf'],
            'acteur' => $row['acteur'],
        ]);
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return string|null
     */
    public function transformDate($value, $format = 'Y-m-d H:i:s')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (Exception $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }

}
