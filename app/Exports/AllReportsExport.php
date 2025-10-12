<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class AllReportsExport implements WithMultipleSheets
{
    protected $reports;

    public function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->reports as $reportName => $reportData) {
            $dataCollection = $reportData['data'] instanceof Collection 
                ? $reportData['data'] 
                : new Collection($reportData['data']);

            $sheets[] = new SingleReportSheet(
                \Illuminate\Support\Str::of(str_replace('_', ' ', $reportName))->ucfirst(),
                $dataCollection,
                $reportData['columns']
            );
        }

        return $sheets;
    }
}
