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
                    'agendamentosPorMes' => 'Agendamentos por Mês',
                    default => Str::ucfirst($chartKey)
                };

                $firstItem = $data->first();

                if (is_array($firstItem) && isset($firstItem['label']) && isset($firstItem['value'])) {
                    foreach($data as $item) {
                        $rows[] = [
                            $title,
                            $item['label'],
                            $item['value']
                        ];
                    }
                } else {
                    foreach($data as $key => $value) {
                        $rows[] = [
                            $title,
                            $key,
                            $value
                        ];
                    }
                }
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