<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SingleReportSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $title;
    protected $data;
    protected $headings;
    protected $columnKeys;

    public function __construct(string $title, Collection $data, array $headings)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->columnKeys = array_keys($headings);
        
        $this->data = $data->map(function ($row) {
            $rowData = [];
            foreach ($this->columnKeys as $key) {
                $rowData[] = data_get($row, $key);
            }
            return $rowData;
        });
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_values($this->headings);
    }

    public function title(): string
    {
        return substr($this->title, 0, 31);
    }
}
