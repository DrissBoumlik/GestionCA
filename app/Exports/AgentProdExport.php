<?php


namespace App\Exports;



use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpParser\Node\Stmt\Return_;
use function Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Repositories\StatsRepository;

class AgentProdExport implements FromCollection,WithHeadings,WithMapping, ShouldAutoSize, WithEvents
{
    public $request;
    protected $statsRepository;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->statsRepository = new StatsRepository();
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        $array = [];
        $data = collect($this->statsRepository->getAgentProd($this->request));
        /*foreach ($data as $elements){
             foreach ($elements as $element){
                 unset($element->values);
                 array_push($array, $element);
             }
        }*/
        $data->map(function ($elements) use(&$array){
            collect($elements)->map(function ($element) use(&$array){
                unset($element->values);
                array_push($array, $element);
            });
        });
         return collect($array);
    }

    public function headings(): array
    {
        $titles = [];
        $__headers = $this->statsRepository->GetColumnsgetAgentProd($this->request);
        collect($__headers['columns'])->map(function ($header) use (&$titles){
            array_push($titles,$header->title);
        });
        return $titles;
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        $titles = [];
        $mapper = [];
        $__headers = $this->statsRepository->GetColumnsgetAgentProd($this->request);
        collect($__headers['columns'])->map(function ($header) use (&$titles){
            array_push($titles,$header->data);
        });
        collect($titles)->map(function ($title) use (&$mapper,&$row){
            $elementMapper = (strpos($row->$title, '|') ? str_replace('|', "\n", $row->$title) : $row->$title) ;
            array_push($mapper,$elementMapper);
        });
        return $mapper;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event)
            {
                $HighestCol = $event->sheet->getDelegate()->getHighestColumn();
                $HighestRow = $event->sheet->getDelegate()->getHighestRow();
                $cellsRange = 'A1:'.$HighestCol.$HighestRow;

                $event->sheet->getDelegate()->getStyle($cellsRange)->getAlignment()->applyFromArray([
                    'horizontal' => 'center'
                ]);
                $event->sheet->getDelegate()->getStyle($cellsRange)->getAlignment()->setWrapText(true);

                $color ='';
                $arrayalphabet = range('A', $HighestCol);
                foreach (range(1,$HighestRow) as $row){
                    if($row % 2 === 0){
                        $color = 'DDEBF7';
                    }else{
                        $color = 'FFFFFF';
                    }
                    $event->sheet->getDelegate()->getStyle($arrayalphabet[0].$row.':'.$arrayalphabet[sizeof($arrayalphabet)-1].$row)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB($color);
                }

            },
           ];
    }

}
