<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AllReportsExport;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Excel as ExcelWriterType;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    private $allReportTypes = ['usuarios', 'escolas', 'turmas', 'componentes', 'recursos', 'agendamentos'];

    public function index(Request $request): View|BinaryFileResponse|Response|RedirectResponse
    {
        $user = Auth::user();
        list($municipios, $escolas, $disciplinas, $marcas) = $this->getLocationFiltersAndOptions($user);

        $validatedInputs = $request->validate([
            'id_municipio' => 'nullable|array',
            'id_municipio.*' => 'exists:municipios,id_municipio',
            'id_escola' => 'nullable|array',
            'id_escola.*' => 'exists:escolas,id_escola',
            'nivel_ensino' => 'nullable|array',
            'nivel_ensino.*' => 'string|in:colegio_estadual,escola_tecnica,escola_municipal',
            'tipo_escola' => 'nullable|array',
            'tipo_escola.*' => 'string|in:urbana,rural',
            'user_type' => 'nullable|array',
            'user_type.*' => 'string|in:administrador,diretor,professor',
            'resource_status' => 'nullable|array',
            'resource_status.*' => 'string|in:funcionando,em_manutencao,quebrado,descartado',
            'id_disciplina' => 'nullable|array',
            'id_disciplina.*' => 'exists:componentes_curriculares,id_componente',
            'turma_serie' => 'nullable|string',
            'recurso_marca' => 'nullable|array',
            'recurso_marca.*' => 'string',
            'recurso_qtd_min' => 'nullable|integer|min:0',
            'recurso_qtd_max' => 'nullable|integer|min:0|gte:recurso_qtd_min',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'report_type' => 'nullable|string|in:'.implode(',', $this->allReportTypes),
            'format' => 'nullable|string|in:pdf,xlsx,csv,ods,html',
            'sort_by' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        if (isset($validatedInputs['format']) && $validatedInputs['format']) {
            return $this->handleDownload($validatedInputs, $user);
        }

        $inputs = $request->except('page');
        $sortBy = $validatedInputs['sort_by'] ?? null;
        $order = $validatedInputs['order'] ?? 'asc';
        $selectedReportType = $validatedInputs['report_type'] ?? null;

        $reportData = null;
        $columns = [];
        $stats = $this->getStats($user, $validatedInputs);
        $chartData = $this->getChartData($validatedInputs, $user);

        if ($selectedReportType) {
            if ($selectedReportType === 'escolas' && $user->tipo_usuario !== 'administrador') {
                Log::warning("Tentativa de acesso não autorizado ao relatório de escolas pelo usuário ID: {$user->id_usuario}");
                $reportData = collect()->paginate(10);
                $columns = $this->getColumns('escolas');
            } else {
                $query = $this->buildQuery(['report_type' => $selectedReportType] + $validatedInputs, $user, $sortBy, $order);
                $columns = $this->getColumns($selectedReportType);
                if ($query && $query instanceof \Illuminate\Database\Eloquent\Builder) {
                    $reportData = $query->paginate(10)->withQueryString();
                } else {
                    Log::warning("ReportController: Não foi possível construir a query para o tipo '{$selectedReportType}' na visualização.");
                    $reportData = collect()->paginate(10);
                    if (empty($columns)) $columns = $this->getColumns($selectedReportType);
                }
            }
        }

        return view('reports.index', compact(
            'reportData', 'columns', 'municipios', 'escolas', 'disciplinas', 'marcas', 
            'inputs', 'chartData', 'stats', 'sortBy', 'order', 'selectedReportType'
        ));
    }

    private function handleDownload($validatedInputs, $user): BinaryFileResponse|RedirectResponse|Response
    {
        $format = $validatedInputs['format'];
        $filenameBase = 'relatorio_NREduTech_' . now()->format('Y-m-d_His');
        
        $typesToGenerate = [];
        if (!empty($validatedInputs['report_type']) && in_array($validatedInputs['report_type'], $this->allReportTypes)) {
            $typesToGenerate[] = $validatedInputs['report_type'];
        } else {
            $typesToGenerate = $this->allReportTypes;
        }

        if ($user->tipo_usuario !== 'administrador') {
            $typesToGenerate = array_diff($typesToGenerate, ['escolas']);
        }

        $stats = $this->getStats($user, $validatedInputs);
        $chartData = $this->getChartData($validatedInputs, $user);

        $hasAnyData = false;
        foreach($typesToGenerate as $type) {
            $query = $this->buildQuery(['report_type' => $type] + $validatedInputs, $user);
            if ($query && $query->exists()) {
                $hasAnyData = true;
                break;
            }
        }
        
        if (!$hasAnyData && empty(array_filter($stats)) && empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
            $redirectInputs = Arr::except($validatedInputs, ['format']);
            return redirect()->route('reports.index', $redirectInputs)->with('error', 'Nenhum dado encontrado para exportar com os filtros aplicados.');
        }

        $isSingleReport = count($typesToGenerate) === 1;
        $fileNameSuffix = $isSingleReport ? '_' . $typesToGenerate[0] : '_completo';

        try {
            if ($format === 'csv') {
                return $this->streamCsvReportsToZip($typesToGenerate, $validatedInputs, $user, $filenameBase, $stats, $chartData);
            }

            if ($format === 'pdf') {
                return $this->generatePdfReports($typesToGenerate, $validatedInputs, $user, $filenameBase, $fileNameSuffix, $isSingleReport, $stats, $chartData);
            }
            
            $reportsToExport = $this->fetchAllReportData($typesToGenerate, $validatedInputs, $user);

            if (empty($reportsToExport) && empty(array_filter($stats)) && empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
                 $redirectInputs = Arr::except($validatedInputs, ['format']);
                 return redirect()->route('reports.index', $redirectInputs)->with('error', 'Nenhum dado encontrado para exportar com os filtros aplicados.');
            }

            if (in_array($format, ['xlsx', 'ods'])) {
                if (!class_exists(AllReportsExport::class)) throw new \Exception('Classe AllReportsExport não encontrada.');
                $fileExtension = $format === 'xlsx' ? '.xlsx' : '.ods';
                $writerType = $format === 'xlsx' ? ExcelWriterType::XLSX : ExcelWriterType::ODS;
                $fileName = $filenameBase . $fileNameSuffix . $fileExtension;
                
                return Excel::download(new AllReportsExport($reportsToExport, $stats, $chartData), $fileName, $writerType);
            }
            
            if ($format === 'html') {
                if (!view()->exists('reports.partials.html_multi')) throw new \Exception('A view reports.partials.html_multi não foi encontrada.');
                $htmlContent = view('reports.partials.html_multi', [
                    'reports' => $reportsToExport,
                    'stats' => $stats,
                    'chartData' => $chartData
                ])->render();
                $fileName = $filenameBase . $fileNameSuffix . '.html';
                return response($htmlContent)
                    ->header('Content-Type', 'text/html')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            }

            $redirectInputs = Arr::except($validatedInputs, ['format']);
            return redirect()->route('reports.index', $redirectInputs)->with('error', 'Formato de download inválido.');

        } catch (\Throwable $e) {
            Log::error("Erro no download ({$format}): " . $e->getMessage() . " Arquivo: " . $e->getFile() . " Linha: " . $e->getLine());
            $errorMessage = 'Ocorreu um erro inesperado ao gerar o arquivo.';
            if (app()->environment('local', 'development')) $errorMessage .= ' Detalhes: ' . $e->getMessage();
            $redirectInputs = Arr::except($validatedInputs, ['format']);
            return redirect()->route('reports.index', $redirectInputs)->with('error', $errorMessage);
        }
    }
    
    private function fetchAllReportData($typesToGenerate, $validatedInputs, $user): array
    {
        $reportsToExport = [];
        foreach ($typesToGenerate as $type) {
            $filters = ['report_type' => $type] + $validatedInputs;
            $query = $this->buildQuery($filters, $user);
            if (!$query || !$query instanceof \Illuminate\Database\Eloquent\Builder) {
                Log::warning("ReportController->fetchAllReportData: Falha ao construir query para tipo '{$type}'.");
                continue;
            }
            $data = $query->get();
            if ($data->isNotEmpty()) {
                $reportsToExport[$type] = [
                    'data' => $data,
                    'columns' => $this->getColumns($type),
                    'title' => Str::ucfirst(str_replace('_', ' ', $type))
                ];
            }
        }
        return $reportsToExport;
    }
    
    private function generatePdfReports($typesToGenerate, $validatedInputs, $user, $filenameBase, $fileNameSuffix, $isSingleReport, $stats = [], $chartData = []): BinaryFileResponse|Response
    {
        set_time_limit(300); 
        ini_set('memory_limit', '512M');

        $chunkSize = 1000; 

        $hasAnyData = false;
        if (!empty(array_filter($stats))) {
            $hasAnyData = true;
        } else if (!empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
            $hasAnyData = true;
        } else {
            foreach($typesToGenerate as $type) {
                $query = $this->buildQuery(['report_type' => $type] + $validatedInputs, $user);
                if ($query && $query->exists()) {
                    $hasAnyData = true;
                    break;
                }
            }
        }

        if (!$hasAnyData) {
            throw new \Exception("Não foram encontrados dados para gerar o PDF.");
        }

        if (!class_exists('ZipArchive')) {
            throw new \Exception('A extensão ZipArchive do PHP é necessária para exportar múltiplos PDFs.');
        }

        $zip = new ZipArchive;
        $zipFileName = $isSingleReport ? $filenameBase . $fileNameSuffix . '.zip' : $filenameBase . '_pdf_reports.zip';
        $tempZipPath = storage_path('app/temp/' . $zipFileName);
        File::ensureDirectoryExists(dirname($tempZipPath));

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Não foi possível criar o arquivo ZIP para os PDFs.");
        }

        $tempPdfDir = storage_path('app/temp/pdf_temp_' . uniqid());
        File::ensureDirectoryExists($tempPdfDir);

        if (!empty(array_filter($stats)) || !empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
            try {
                $statsPdf = Pdf::loadView('reports.partials.pdf_multi', [
                    'reports' => [], 
                    'stats' => $stats,
                    'chartData' => $chartData
                ])->setPaper('a4', 'portrait');
                
                $pdfFileNameOnly = '00_Resumo_KPIs_e_Graficos.pdf';
                $pdfAbsolutePath = $tempPdfDir . '/' . $pdfFileNameOnly;
                $statsPdf->save($pdfAbsolutePath);

                if (File::exists($pdfAbsolutePath)) {
                    $zip->addFile($pdfAbsolutePath, $pdfFileNameOnly);
                }
                unset($statsPdf); 
            } catch (\Exception $e) {
                Log::error("Falha ao gerar PDF de Stats: " . $e->getMessage());
            }
        }

        foreach ($typesToGenerate as $type) {
            $query = $this->buildQuery(['report_type' => $type] + $validatedInputs, $user);
            if (!$query) continue;

            $columns = $this->getColumns($type);
            $title = Str::ucfirst(str_replace('_', ' ', $type));
            $basePdfName = Str::snake($title);
            $chunkIndex = 1;

            $query->chunkById($chunkSize, function($results) use (&$zip, $tempPdfDir, $type, $columns, $title, $basePdfName, &$chunkIndex) {
                if ($results->isEmpty()) {
                    return;
                }
                $reportData = [
                    'data' => $results,
                    'columns' => $columns,
                    'title' => $title . " (Parte $chunkIndex)"
                ];
                $pdf = Pdf::loadView('reports.partials.pdf_multi', ['reports' => [$type => $reportData]])->setPaper('a4', 'landscape');
                $pdfFileNameOnly = $basePdfName . '_parte_' . $chunkIndex . '.pdf';
                $pdfAbsolutePath = $tempPdfDir . '/' . $pdfFileNameOnly;
                $pdf->save($pdfAbsolutePath);
                if (File::exists($pdfAbsolutePath)) {
                    $zip->addFile($pdfAbsolutePath, $pdfFileNameOnly);
                }
                $chunkIndex++;
                unset($pdf); 
                unset($reportData);
                unset($results);
            });
        }

        $zip->close();
        File::deleteDirectory($tempPdfDir);

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function streamCsvReportsToZip($typesToGenerate, $validatedInputs, $user, $filenameBase, $stats = [], $chartData = []): BinaryFileResponse
    {
        if (!class_exists('ZipArchive')) throw new \Exception('A extensão ZipArchive do PHP é necessária para exportar múltiplos CSVs.');

        $zip = new ZipArchive;
        $zipFileName = $filenameBase . '_csv_reports.zip';
        $tempZipPath = storage_path('app/temp/' . $zipFileName);
        File::ensureDirectoryExists(dirname($tempZipPath));

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Não foi possível criar o arquivo ZIP em {$tempZipPath}");
        }

        $tempCsvDir = storage_path('app/temp/csv_temp_' . uniqid());
        File::ensureDirectoryExists($tempCsvDir);

        if (!empty(array_filter($stats)) || !empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
            $csvAbsolutePath = $tempCsvDir . '/00_Resumo_KPIs_e_Graficos.csv';
            $handle = fopen($csvAbsolutePath, 'w');
            
            if (!empty(array_filter($stats))) {
                fputcsv($handle, ['Indicador (KPI)', 'Valor']);
                foreach ($stats as $key => $value) {
                    $formattedKey = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", Str::ucfirst(Str::camel($key)));
                    $formattedKey = str_replace('Total ', 'Total de ', $formattedKey);
                    fputcsv($handle, [$formattedKey, $value]);
                }
            }

            if (!empty(array_filter($chartData, fn($c) => $c->isNotEmpty()))) {
                fputcsv($handle, []); 
                fputcsv($handle, ['Dados dos Gráficos']);
                fputcsv($handle, ['Indicador', 'Categoria', 'Valor']);
                
                foreach ($chartData as $chartKey => $data) {
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
                            fputcsv($handle, [$title, $key, $value]);
                        }
                    }
                }
            }
            
            fclose($handle);
            if (File::exists($csvAbsolutePath)) {
                $zip->addFile($csvAbsolutePath, '00_Resumo_KPIs_e_Graficos.csv');
            }
        }

        foreach ($typesToGenerate as $type) {
            $query = $this->buildQuery(['report_type' => $type] + $validatedInputs, $user);
            if (!$query || !$query->clone()->exists()) {
                continue;
            }

            $columns = $this->getColumns($type);
            $columnKeys = array_keys($columns);
            $columnHeaders = array_values($columns);
            $csvFileNameOnly = Str::snake(Str::ucfirst(str_replace('_', ' ', $type))) . '.csv';
            $csvAbsolutePath = $tempCsvDir . '/' . $csvFileNameOnly;

            $handle = fopen($csvAbsolutePath, 'w');
            fputcsv($handle, $columnHeaders);

            $query->chunk(500, function($results) use ($handle, $columnKeys) {
                foreach ($results as $row) {
                    $rowData = [];
                    foreach ($columnKeys as $key) {
                        $rowData[] = data_get($row, $key);
                    }
                    fputcsv($handle, $rowData);
                }
            });

            fclose($handle);

            if (File::exists($csvAbsolutePath)) {
                $zip->addFile($csvAbsolutePath, $csvFileNameOnly);
            }
        }

        $zip->close();
        File::deleteDirectory($tempCsvDir);

        if (!File::exists($tempZipPath) || filesize($tempZipPath) === 0) {
            throw new \Exception("Falha na criação do arquivo ZIP final. O arquivo não existe ou está vazio.");
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }
    
    private function getColumns($reportType): array {
        return match ($reportType) {
            'usuarios' => ['id_usuario' => 'ID', 'nome_completo' => 'Nome', 'email' => 'E-mail', 'tipo_usuario' => 'Tipo', 'escola.nome' => 'Escola', 'escola.municipio.nome' => 'Município', 'status_aprovacao' => 'Status', 'data_registro' => 'Cadastro'],
            'recursos' => ['id_recurso' => 'ID', 'nome' => 'Nome', 'tipo' => 'Tipo', 'marca' => 'Marca', 'quantidade' => 'Qtde', 'status' => 'Status', 'data_aquisicao' => 'Aquisição', 'numero_serie' => 'Série/Patrim.'],
            'componentes' => ['id_componente' => 'ID', 'nome' => 'Disciplina', 'escola.nome' => 'Escola', 'carga_horaria' => 'C.H.', 'status' => 'Status', 'criador.nome_completo' => 'Criador', 'created_at' => 'Criação'],
            'turmas' => ['id_turma' => 'ID', 'serie' => 'Série', 'turno' => 'Turno', 'ano_letivo' => 'Ano', 'nivel_escolaridade' => 'Nível', 'escola.nome' => 'Escola', 'escola.municipio.nome' => 'Município'],
            'agendamentos' => ['id_agendamento' => 'ID', 'data_hora_inicio' => 'Início', 'data_hora_fim' => 'Fim', 'recurso.nome' => 'Recurso', 'oferta.professor.nome_completo' => 'Professor', 'oferta.componenteCurricular.nome' => 'Disciplina', 'oferta.turma.serie' => 'Turma', 'oferta.turma.escola.nome' => 'Escola'],
            'escolas' => ['id_escola' => 'ID', 'nome' => 'Escola', 'municipio.nome' => 'Município', 'nivel_ensino' => 'Nível', 'tipo' => 'Tipo'],
            default => [],
        };
    }

    private function getLocationFiltersAndOptions($user): array {
        $municipios = Municipio::orderBy('nome')->get();
        $escolas = collect();
        $escolaIds = collect();
    
        if ($user->tipo_usuario === 'administrador') {
            $escolas = Escola::with('municipio')->orderBy('nome')->get();
            $escolaIds = $escolas->pluck('id_escola');
        } elseif ($user->id_escola) {
            $escolas = Escola::where('id_escola', $user->id_escola)->with('municipio')->get();
            $escolaIds = $escolas->pluck('id_escola');
        }
    
        $disciplinasQuery = ComponenteCurricular::with('escola')->orderBy('nome');
        $marcasQuery = RecursoDidatico::select('marca')
            ->whereNotNull('marca')->where('marca', '!=', '');
    
        if ($user->tipo_usuario !== 'administrador') {
            if ($escolaIds->isNotEmpty()) {
                $disciplinasQuery->whereIn('id_escola', $escolaIds);
                if (Schema::hasColumn('recursos_didaticos', 'id_escola')) {
                    $marcasQuery->whereIn('id_escola', $escolaIds);
                }
            } else {
                $disciplinasQuery->whereRaw('1 = 0');
                $marcasQuery->whereRaw('1 = 0');
            }
        }
    
        $disciplinas = $disciplinasQuery->get();
        $marcas = $marcasQuery->distinct()->orderBy('marca')->pluck('marca');
    
        return [$municipios, $escolas, $disciplinas, $marcas];
    }

    private function buildQuery($filters, $user, $sortBy = null, $order = 'asc'): ?\Illuminate\Database\Eloquent\Builder {
        if (empty($filters['report_type']) || !in_array($filters['report_type'], $this->allReportTypes)) {
             return null;
        }
        $reportType = $filters['report_type'];
        $baseTable = null;
        switch ($reportType) {
            case 'usuarios': $query = Usuario::query()->with(['escola.municipio']); $baseTable = 'usuarios'; break;
            case 'recursos': $query = RecursoDidatico::query(); $baseTable = 'recursos_didaticos'; break;
            case 'componentes': $query = ComponenteCurricular::query()->with(['criador', 'escola']); $baseTable = 'componentes_curriculares'; break;
            case 'turmas': $query = Turma::query()->with(['escola.municipio']); $baseTable = 'turmas'; break;
            case 'agendamentos': $query = Agendamento::query()->with(['recurso', 'oferta.professor', 'oferta.componenteCurricular', 'oferta.turma.escola.municipio']); $baseTable = 'agendamentos'; break;
            case 'escolas': $query = Escola::query()->with(['municipio']); $baseTable = 'escolas'; break;
            default: return null;
        }
        
        if ($user->tipo_usuario === 'administrador') {
            if (!empty($filters['id_municipio'])) $this->applyMunicipioFilter($query, $reportType, $filters['id_municipio']);
            if (!empty($filters['id_escola'])) $this->applyEscolaFilter($query, $reportType, $filters['id_escola']);
            if (!empty($filters['nivel_ensino'])) $this->applyGenericEscolaFilter($query, $reportType, 'nivel_ensino', $filters['nivel_ensino']);
            if (!empty($filters['tipo_escola'])) $this->applyGenericEscolaFilter($query, $reportType, 'tipo', $filters['tipo_escola']);
        } elseif($user->id_escola) {
           $this->applyEscolaFilter($query, $reportType, $user->id_escola);
        } elseif ($user->tipo_usuario !== 'administrador') {
            if (in_array($reportType, ['usuarios', 'turmas', 'agendamentos', 'escolas', 'recursos', 'componentes'])) {
                 $query->whereRaw('1 = 0'); 
            }
        }

        if (!empty($filters['user_type']) && $reportType === 'usuarios') $query->whereIn($baseTable.'.tipo_usuario', Arr::wrap($filters['user_type']));
        if (!empty($filters['resource_status']) && $reportType === 'recursos') $query->whereIn($baseTable.'.status', Arr::wrap($filters['resource_status']));
        if (!empty($filters['start_date']) && $reportType === 'agendamentos') $query->whereDate($baseTable.'.data_hora_inicio', '>=', $filters['start_date']);
        if (!empty($filters['end_date']) && $reportType === 'agendamentos') $query->whereDate($baseTable.'.data_hora_inicio', '<=', $filters['end_date']);
        if (!empty($filters['id_disciplina']) && $reportType === 'componentes') $query->whereIn($baseTable.'.id_componente', Arr::wrap($filters['id_disciplina']));
        if (!empty($filters['turma_serie']) && $reportType === 'turmas') $query->where($baseTable.'.serie', 'like', '%'.trim($filters['turma_serie']).'%');
        if (!empty($filters['recurso_marca']) && $reportType === 'recursos') $query->whereIn($baseTable.'.marca', Arr::wrap($filters['recurso_marca']));
        if (!empty($filters['recurso_qtd_min']) && $reportType === 'recursos') $query->where($baseTable.'.quantidade', '>=', $filters['recurso_qtd_min']);
        if (!empty($filters['recurso_qtd_max']) && $reportType === 'recursos') $query->where($baseTable.'.quantidade', '<=', $filters['recurso_qtd_max']);

        if ($sortBy) {
            $this->applySortingForView($query, $reportType, $sortBy, $order);
        } else {
             $this->applySimpleSorting($query, $reportType, null, 'asc');
        }
         if ($sortBy && Str::contains($sortBy, '.')) {
             $query->select($baseTable . '.*');
         }
        return $query;
    }

    private function applySortingForView($query, $reportType, $sortBy, $order) {
        $columns = $this->getColumns($reportType);
        $baseTable = $query->getModel()->getTable();
        $primaryKey = $query->getModel()->getKeyName();
        if (!array_key_exists($sortBy, $columns)) {
            Log::warning("Tentativa de ordenar por coluna inválida '{$sortBy}' no relatório '{$reportType}'.");
            $this->applySimpleSorting($query, $reportType, null, $order);
            return;
        }
        $finalOrder = in_array(strtolower($order), ['asc', 'desc']) ? strtolower($order) : 'asc';
        $sortColumnKey = $sortBy;
        if (Str::contains($sortColumnKey, '.')) {
            $relations = explode('.', $sortColumnKey);
            $relatedColumn = array_pop($relations);
            $relationName = $relations[0];
            try {
                switch ($relationName) {
                    case 'escola':
                        if (!collect($query->getQuery()->joins)->pluck('table')->contains('escolas')) {
                           $query->leftJoin('escolas', $baseTable.'.id_escola', '=', 'escolas.id_escola');
                        }
                        if (isset($relations[1]) && $relations[1] === 'municipio') {
                             if (!collect($query->getQuery()->joins)->pluck('table')->contains('municipios')) {
                                 $query->leftJoin('municipios', 'escolas.id_municipio', '=', 'municipios.id_municipio');
                             }
                             $query->orderBy('municipios.' . $relatedColumn, $finalOrder);
                        } else {
                             $query->orderBy('escolas.' . $relatedColumn, $finalOrder);
                        }
                        break;
                    case 'municipio':
                        if ($baseTable === 'escolas' && !collect($query->getQuery()->joins)->pluck('table')->contains('municipios')) {
                           $query->leftJoin('municipios', $baseTable.'.id_municipio', '=', 'municipios.id_municipio');
                        }
                        $query->orderBy('municipios.' . $relatedColumn, $finalOrder);
                        break;
                    case 'criador':
                          if ($baseTable === 'componentes_curriculares' && !collect($query->getQuery()->joins)->pluck('table')->contains('usuarios')) {
                             $query->leftJoin('usuarios as criador_join', $baseTable.'.id_usuario_criador', '=', 'criador_join.id_usuario');
                             $query->orderBy('criador_join.' . $relatedColumn, $finalOrder);
                          } else {
                               $query->orderBy($baseTable . '.' . $primaryKey, $finalOrder);
                          }
                          break;
                    case 'recurso':
                        if($baseTable === 'agendamentos' && !collect($query->getQuery()->joins)->pluck('table')->contains('recursos_didaticos')) {
                             $query->leftJoin('recursos_didaticos', $baseTable.'.id_recurso', '=', 'recursos_didaticos.id_recurso');
                             $query->orderBy('recursos_didaticos.' . $relatedColumn, $finalOrder);
                        }
                        break;
                     case 'oferta':
                           $query->orderBy($baseTable . '.' . $primaryKey, $finalOrder);
                          break;
                    default:
                        $query->orderBy($baseTable . '.' . $primaryKey, $finalOrder);
                }
                $query->select($baseTable . '.*');
            } catch (\Exception $e) {
                Log::error("Erro ao tentar aplicar join para ordenação por '{$sortBy}': " . $e->getMessage());
                $this->applySimpleSorting($query, $reportType, null, $order);
                return;
            }
        } else {
            $query->orderBy($baseTable . '.' . $sortColumnKey, $finalOrder);
        }
        if ($sortColumnKey !== $primaryKey) {
          $query->orderBy($baseTable . '.' . $primaryKey, 'asc');
        }
    }

    private function applySimpleSorting($query, $reportType, $sortBy, $order) {
        $sortableColumnsMap = [
            'usuarios' => ['id_usuario', 'nome_completo', 'email', 'tipo_usuario', 'status_aprovacao', 'data_registro'],'recursos' => ['id_recurso', 'nome', 'tipo', 'marca', 'status', 'quantidade', 'data_aquisicao'],'componentes' => ['id_componente', 'nome', 'carga_horaria', 'status', 'created_at'],'turmas' => ['id_turma', 'serie', 'turno', 'ano_letivo', 'nivel_escolaridade'],'agendamentos' => ['id_agendamento', 'data_hora_inicio', 'data_hora_fim', 'status'],'escolas' => ['id_escola', 'nome', 'nivel_ensino', 'tipo'],
        ];
        $baseTable = $query->getModel()->getTable();
        $primaryKey = $query->getModel()->getKeyName();
        $validSortColumns = $sortableColumnsMap[$reportType] ?? [$primaryKey];
        $defaultSortColumn = $validSortColumns[0];
        $finalSortBy = $sortBy && in_array($sortBy, $validSortColumns) ? $sortBy : $defaultSortColumn;
        $finalOrder = in_array(strtolower($order), ['asc', 'desc']) ? strtolower($order) : 'asc';
        $query->orderBy($baseTable . '.' . $finalSortBy, $finalOrder);
        if ($finalSortBy !== $primaryKey) {
          $query->orderBy($baseTable . '.' . $primaryKey, 'asc');
        }
    }

    private function getStats($user, $filters = []): array {
        $stats = [];
        $baseFilters = Arr::except($filters, ['report_type', 'sort_by', 'order', 'format', 'page']);
        $typesToCount = $this->allReportTypes;
        if($user->tipo_usuario !== 'administrador') {
             $typesToCount = array_diff($typesToCount, ['escolas']);
        }
        foreach($typesToCount as $type) {
            $key = 'total' . Str::ucfirst(Str::camel($type));
             try {
                 $query = $this->buildQuery(array_merge($baseFilters, ['report_type' => $type]), $user);
                  if ($query && $query instanceof \Illuminate\Database\Eloquent\Builder) {
                       $stats[$key] = $query->count();
                  } else {
                       $stats[$key] = 0;
                  }
             } catch (\Exception $e) {
                 Log::error("Erro ao calcular stat {$key}: " . $e->getMessage());
                 $stats[$key] = 0;
             }
        }
        return $stats;
    }

    private function getChartData($filters, $user): array {
        $baseFilters = Arr::except($filters, ['report_type', 'sort_by', 'order', 'format', 'page']);
        $chartData = [];
        try {
            $recursoQuery = $this->buildQuery(array_merge($baseFilters, ['report_type' => 'recursos']), $user);
             $chartData['recursosPorStatus'] = ($recursoQuery && $recursoQuery instanceof \Illuminate\Database\Eloquent\Builder)
                 ? (clone $recursoQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status')
                 : collect();
        } catch (\Exception $e) { Log::error("Chart Data Error (Recursos): ".$e->getMessage()); $chartData['recursosPorStatus'] = collect(); }
        try {
            $usuarioQuery = $this->buildQuery(array_merge($baseFilters, ['report_type' => 'usuarios']), $user);
            if ($usuarioQuery && $usuarioQuery instanceof \Illuminate\Database\Eloquent\Builder) {
                $chartData['usuariosPorTipo'] = (clone $usuarioQuery)->select('tipo_usuario', DB::raw('count(*) as total'))->groupBy('tipo_usuario')->pluck('total', 'tipo_usuario');
                $usuariosPorMunicipio = collect();
                if ($user->tipo_usuario === 'administrador') {
                    $locationQuery = clone $usuarioQuery;
                    
                    if (empty($filters['id_escola'])) { 
                         if (!collect($locationQuery->getQuery()->joins)->pluck('table')->contains('escolas')) {
                             $locationQuery->join('escolas', 'usuarios.id_escola', '=', 'escolas.id_escola');
                         }
                         if (!collect($locationQuery->getQuery()->joins)->pluck('table')->contains('municipios')) {
                             $locationQuery->join('municipios', 'escolas.id_municipio', '=', 'municipios.id_municipio');
                         }

                         if (empty($filters['id_municipio'])) {
                               $usuariosPorMunicipio = $locationQuery->select('municipios.nome', DB::raw('count(usuarios.id_usuario) as total'))
                                 ->groupBy('municipios.nome')->pluck('total', 'nome');
                         } else {
                               $usuariosPorMunicipio = $locationQuery->select('escolas.nome', DB::raw('count(usuarios.id_usuario) as total'))
                                 ->whereIn('escolas.id_municipio', Arr::wrap($filters['id_municipio']))
                                 ->groupBy('escolas.nome')->pluck('total', 'nome');
                         }
                    }
                }
                 $chartData['usuariosPorMunicipio'] = $usuariosPorMunicipio;
            } else {
                 $chartData['usuariosPorTipo'] = collect();
                 $chartData['usuariosPorMunicipio'] = collect();
            }
        } catch (\Exception $e) { Log::error("Chart Data Error (Usuarios): ".$e->getMessage()); $chartData['usuariosPorTipo'] = collect(); $chartData['usuariosPorMunicipio'] = collect();}
        try {
            $turmaQuery = $this->buildQuery(array_merge($baseFilters, ['report_type' => 'turmas']), $user);
            $chartData['turmasPorTurno'] = ($turmaQuery && $turmaQuery instanceof \Illuminate\Database\Eloquent\Builder)
                ? (clone $turmaQuery)->select('turno', DB::raw('count(*) as total'))->groupBy('turno')->pluck('total', 'turno')
                : collect();
        } catch (\Exception $e) { Log::error("Chart Data Error (Turmas): ".$e->getMessage()); $chartData['turmasPorTurno'] = collect(); }
        try {
            $componenteQuery = $this->buildQuery(array_merge($baseFilters, ['report_type' => 'componentes']), $user);
             $chartData['componentesPorStatus'] = ($componenteQuery && $componenteQuery instanceof \Illuminate\Database\Eloquent\Builder)
                 ? (clone $componenteQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status')
                 : collect();
        } catch (\Exception $e) { Log::error("Chart Data Error (Componentes): ".$e->getMessage()); $chartData['componentesPorStatus'] = collect(); }
        return $chartData;
    }

    private function applyEscolaFilter(&$query, $type, $escolaIds) {
        if(!$query) return;
        $ids = Arr::wrap($escolaIds); 
        if(empty($ids)) return;
        
        $baseTable = $query->getModel()->getTable();
        if (in_array($type, ['usuarios', 'turmas', 'componentes'])) $query->whereIn($baseTable.'.id_escola', $ids);
        if ($type === 'escolas') $query->whereIn($baseTable.'.id_escola', $ids);
        if ($type === 'agendamentos') $query->whereHas('oferta.turma', fn($q) => $q->whereIn('id_escola', $ids));
        
        if ($type === 'recursos' && Schema::hasColumn($baseTable, 'id_escola')) {
            $query->whereIn($baseTable.'.id_escola', $ids);
        }
    }

    private function applyMunicipioFilter(&$query, $type, $municipioIds) {
        if(!$query) return;
        $ids = Arr::wrap($municipioIds);
        if(empty($ids)) return;
        
        $baseTable = $query->getModel()->getTable();
        if (in_array($type, ['usuarios', 'turmas', 'componentes'])) {
            $query->whereHas('escola', fn($q) => $q->whereIn('id_municipio', $ids));
        } elseif ($type === 'escolas') {
            $query->whereIn($baseTable.'.id_municipio', $ids);
        } elseif ($type === 'agendamentos') {
            $query->whereHas('oferta.turma.escola', fn($q) => $q->whereIn('id_municipio', $ids));
        }
        
        if ($type === 'recursos' && Schema::hasColumn($baseTable, 'id_escola')) {
             $query->whereHas('escola', fn($q) => $q->whereIn('id_municipio', $ids));
        }
    }

    private function applyGenericEscolaFilter(&$query, $type, $column, $values) {
        if(!$query) return;
        $vals = Arr::wrap($values);
        if(empty($vals)) return;
        
        $baseTable = $query->getModel()->getTable();
        if (in_array($type, ['usuarios', 'turmas', 'componentes'])) {
            $query->whereHas('escola', fn($q) => $q->whereIn($column, $vals));
        } elseif ($type === 'escolas') {
            $query->whereIn($baseTable.'.'.$column, $vals);
        } elseif ($type === 'agendamentos') {
            $query->whereHas('oferta.turma.escola', fn($q) => $q->whereIn($column, $vals));
        }
        
         if ($type === 'recursos' && Schema::hasColumn($baseTable, 'id_escola')) {
             $query->whereHas('escola', fn($q) => $q->whereIn($column, $vals));
         }
    }
}