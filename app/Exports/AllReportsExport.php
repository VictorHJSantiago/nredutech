<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class AllReportsExport implements WithMultipleSheets
{
    protected $reports;
    protected $stats; 
    protected $chartData; 

    /**
     * @param array $reports 
     * @param array $stats 
     * @param array $chartData 
     */
    public function __construct(array $reports, array $stats = [], array $chartData = [])
    {
        $this->reports = $reports;
        $this->stats = $stats;
        $this->chartData = $chartData; 
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        if (!empty(array_filter($this->stats))) {
            if (class_exists(KpiSheet::class)) {
                $sheets[] = new KpiSheet($this->stats);
            }
        }

        if (!empty(array_filter($this->chartData, fn($c) => $c->isNotEmpty()))) {
            if (class_exists(ChartDataSheet::class)) {
                $sheets[] = new ChartDataSheet($this->chartData);
            }
        }

        foreach ($this->reports as $reportName => $reportData) {
            $dataCollection = $reportData['data'] instanceof Collection 
                ? $reportData['data'] 
                : new Collection($reportData['data']);

            if (class_exists(SingleReportSheet::class)) {
                $sheets[] = new SingleReportSheet(
                    \Illuminate\Support\Str::of(str_replace('_', ' ', $reportName))->ucfirst(),
                    $dataCollection,
                    $reportData['columns']
                );
            }
        }

        return $sheets;
    }
}