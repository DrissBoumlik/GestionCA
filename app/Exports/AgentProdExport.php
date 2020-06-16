<?php


namespace App\Exports;



use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
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
        foreach ($data as $elements){
             foreach ($elements as $element){
                 unset($element->values);
                 array_push($array, $element);
             }
        }
        //dd($array);
         return collect($array);
    }

    public function headings(): array
    {
        $titles = [];
        $__headers = $this->statsRepository->GetColumnsgetAgentProd($this->request);
        foreach (($__headers['columns']) as $header){
            array_push($titles,$header->data);
        }
       // dd($titles);
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
        foreach (($__headers['columns']) as $header){
            array_push($titles,$header->data);
        }
        foreach ($titles as $title){
            $elementMapper = (strpos($row->$title, '|') ? str_replace('|', "\n", $row->$title) : $row->$title) ;
            array_push($mapper,$elementMapper);
        }
        return $mapper;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event)
            {
                $event->sheet->getDelegate()->getStyle('A1:Z100')->getAlignment()->applyFromArray([
                    'horizontal' => 'center'
                ]);
                $event->sheet->getDelegate()->getStyle('A1:Z100')->getAlignment()->setWrapText(true);
            },
           ];
    }

}
