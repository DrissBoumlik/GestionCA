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
            $this->dates = array_map(function ($date) {
                return preg_replace('/\d+-\d+-/', '', $date);
            }, explode(',', $dates));
            \DB::table('agents')->whereIn('imported_at', $this->dates)->delete();
        }
//        $this->user_flag = getImportedData(false);
//        $this->user_flag->flags = [
//            'imported_data' => 0,
//            'is_importing' => 1
//        ];
//        $this->user_flag->update();
    }

    public function collection(Collection $rows)
    {
        $rows->shift();
        $data = $rows->map(function ($row, $index) {
            $item = [
                'pseudo' => $row['pseudo'],
                'fullName'=> $row['nom_complet'],
                'hours'=> $row['heures'],
                'imported_at'=> null,
                'isNotReady' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            return $item;
        });
        Stats::insert($data->all());
    }

    /**
     * @param array $row
     *
     * @return Agent
     */
    public function model($row)
    {
        //            if ($this->user_flag) {
//                $imported_data = $this->user_flag->flags['imported_data'];
//                $this->user_flag->flags = [
//                    'imported_data' => $imported_data + 1,
//                    'is_importing' => 1
//                ];
//                $this->user_flag->save();
//            }
        return new Agent([
            'pseudo' => $row['pseudo'],
            'fullName'=> $row['nom_complet'],
            'hours'=> $row['heures'],
            'imported_at'=> implode(',', $this->dates),
            'isNotReady' => true
        ]);
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
