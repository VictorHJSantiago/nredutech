<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $headings;

    public function __construct(Collection $data, array $headings)
    {
        $this->data = $data->map(function ($row) use ($headings) {
            $rowData = [];
            foreach (array_keys($headings) as $key) {
                $rowData[] = data_get($row, $key);
            }
            return $rowData;
        });
        $this->headings = array_values($headings);
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}