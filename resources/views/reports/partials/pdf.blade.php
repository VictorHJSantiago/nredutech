<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Relatório' }}</title>
    {{-- <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}"> --}}
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; word-wrap: break-word; }
        th { background-color: #e9ecef; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 20px; color: #0169b4; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .note { font-size: 8pt; color: #555; text-align: center; margin-bottom: 15px; }
        .no-data { text-align: center; font-style: italic; color: #666; }
    </style>
</head>
<body>
    <h2>{{ $title ?? 'Relatório' }}</h2>
    @if(isset($data) && !$data->isEmpty() && isset($columns) && !empty($columns))
        <table>
            <thead>
                <tr>
                    @foreach($columns as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach(array_keys($columns) as $key)
                            <td>{{ data_get($row, $key) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-data">Nenhum dado encontrado para este relatório com os filtros aplicados.</p>
    @endif
</body>
</html>