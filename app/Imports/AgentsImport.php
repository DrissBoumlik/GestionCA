<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Stats;
use App\Models\UserFlag;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;


class AgentsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts //ToCollection
{
    use Importable;

    private $dates = [];
    public $data = [];
    private $index = 0;
    private $user_flag;

    public function __construct($dates)
    {
        if ($dates) {
            $this->dates = explode(',', $dates);
            \DB::table('agents')->whereIn('imported_at', $this->dates)->delete();
            if(is_array($this->dates)) {
                collect($this->dates)->map(function ($date, $index) {
                    $date_parts = explode('-', $date);
                    if (count($date_parts) > 1) {
                        $x = \DB::table('agents')
                            ->where('imported_at_annee', $date_parts[0])
                            ->where('imported_at_mois', $date_parts[1])
                            ->delete();
                    }
                });
            }
        }
    }

    /**
     * @param array $row
     *
     * @return Agent
     */
    public function model($row)
    {
        $year = isset($row['annee']) ? $row['annee'] : null;
        $month = isset($row['mois']) ? $row['mois'] : null;
        $week = isset($row['semaine']) ? $row['semaine'] : null;

        $rowDate = null;
        if ($year && $month) {
            $rowDate = $year . '-' . $month;
            if ($week) {
                $rowDate = $rowDate . '-' . $week;
            }
        }

        $inDateWeekMissing = false;
        if(is_array($this->dates) && !$week) {
            $inDateWeekMissing = collect($this->dates)->contains(function ($date, $index) use ($year, $month, $rowDate) {
                $date_parts = explode('-', $date);
                if (count($date_parts) > 1) {
                    if ($date_parts[0] == $year && $date_parts[1] == $month) {
                        return true;
                    }
                    return false;
                }
                return false;
            });
        }

        if (!$this->dates || in_array($rowDate, $this->dates) || (!$week && $inDateWeekMissing)) {
            $hours = $row['heures'];
            if ($hours == null || $hours == '') {
                $hours = 0;
            }
            return new Agent([
                'pseudo' => isset($row['pseudo']) ? $row['pseudo'] : array_values($row)[0],
                'fullName' => isset( $row['nom_complet']) ?  $row['nom_complet'] : array_values($row)[1],
                'hours' => $hours,
                'imported_at' => $rowDate,
                'imported_at_annee' => $year,
                'imported_at_mois' => $month,
                'imported_at_semaine' => $week,
                'isNotReady' => true
            ]);
        }
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return string|null
     */
    public function transformDate($value, $format = 'Y - m - d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (Exception $e) {
            return Carbon::createFromFormat($format, $value)->toDateString();
        }
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
