<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KpiSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $kpis;

    public function __construct(array $kpis)
    {
        $this->kpis = $kpis;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collection = collect();
        foreach ($this->kpis as $key => $value) {
            $collection->push([
                'Indicador' => $this->formatKey($key),
                'Valor' => $value
            ]);
        }
        return $collection;
    }

    public function headings(): array
    {
        return [
            'Indicador',
            'Valor'
        ];
    }

    public function title(): string
    {
        return 'KPIs';
    }

    private function formatKey(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }
}