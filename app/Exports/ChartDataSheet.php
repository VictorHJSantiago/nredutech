<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Str;

class ChartDataSheet implements FromArray, WithHeadings, WithTitle
{
    protected $chartData;

    public function __construct(array $chartData)
    {
        $this->chartData = $chartData;
    }

    /**
    * @return array
    */
    public function array(): array
    {
        $rows = [];
        
        foreach ($this->chartData as $chartKey => $data) {
            if ($data->isNotEmpty()) {
                
                $title = match($chartKey) {
                    'recursosPorStatus' => 'Recursos por Status',
                    'usuariosPorTipo' => 'Usuários por Tipo',
                    'usuariosPorMunicipio' => 'Usuários por Localização',
                    'turmasPorTurno' => 'Turmas por Turno',
                    'componentesPorStatus' => 'Disciplinas por Status',
                    default => Str::ucfirst($chartKey)
                };

                foreach($data as $key => $value) {
                    $rows[] = [
                        'Indicador' => $title,
                        'Categoria' => $key,
                        'Valor' => $value
                    ];
                }
                $rows[] = ['Indicador' => '', 'Categoria' => '', 'Valor' => ''];
            }
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Indicador',
            'Categoria',
            'Valor'
        ];
    }

    public function title(): string
    {
        return 'Dados dos Gráficos';
    }
}