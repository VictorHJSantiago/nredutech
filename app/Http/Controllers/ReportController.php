<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        list($municipios, $escolas) = $this->getLocationFilters($user);

        $reportData = null;
        $columns = [];
        $inputs = [];
        $chartData = null;
        $stats = $this->getStats($user);
        $sortBy = $request->query('sort_by');
        $order = $request->query('order', 'asc');

        if ($request->has('report_type')) {
            $inputs = $request->validate([
                'id_municipio' => 'nullable|exists:municipios,id_municipio',
                'id_escola' => 'nullable|exists:escolas,id_escola',
                'nivel_ensino' => 'nullable|string|in:colegio_estadual,escola_tecnica,escola_municipal',
                'tipo_escola' => 'nullable|string|in:urbana,rural',
                'report_type' => 'required|string|in:usuarios,recursos,componentes,turmas,agendamentos,all',
                'format' => 'nullable|string|in:pdf,xlsx,csv,ods,html',
            ]);

            $chartData = $this->getChartData($inputs, $user);

            if ($inputs['report_type'] === 'all') {
                $reportData = [];
                $reportTypes = ['usuarios', 'recursos', 'componentes', 'turmas', 'agendamentos'];
                foreach ($reportTypes as $type) {
                    $query = $this->buildQuery(array_merge($inputs, ['report_type' => $type]), $user, $sortBy, $order);
                    $reportData[$type] = [
                        'data' => $query->paginate(5, ['*'], $type . '_page')->withQueryString(),
                        'columns' => $this->getColumns($type)
                    ];
                }
            } else {
                $query = $this->buildQuery($inputs, $user, $sortBy, $order);
                $columns = $this->getColumns($inputs['report_type']);

                if (isset($inputs['format'])) {
                    $data = $query->get();
                    $filename = 'relatorio_' . $inputs['report_type'] . '_' . now()->format('Y-m-d') . '.' . $inputs['format'];
                    if ($inputs['format'] === 'pdf') {
                        $pdf = Pdf::loadView('reports.partials.pdf', ['data' => $data, 'columns' => $columns, 'title' => 'Relatório de ' . ucfirst($inputs['report_type'])]);
                        return $pdf->download($filename);
                    }
                    return Excel::download(new ReportExport($data, $columns), $filename);
                }
                
                $reportData = $query->paginate(5)->withQueryString();
            }
        }

        return view('reports.index', [
            'reportData' => $reportData,
            'columns' => $columns,
            'municipios' => $municipios,
            'escolas' => $escolas,
            'inputs' => $inputs,
            'chartData' => $chartData,
            'stats' => $stats,
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }

    private function buildQuery($filters, $user, $sortBy = null, $order = 'asc')
    {
        $reportType = $filters['report_type'];
        $query = null;

        $sortableColumns = [
            'usuarios' => ['nome_completo', 'email', 'tipo_usuario', 'status_aprovacao'],
            'recursos' => ['nome', 'tipo', 'marca', 'status', 'quantidade'],
            'componentes' => ['nome', 'carga_horaria', 'status'],
            'turmas' => ['serie', 'turno', 'ano_letivo'],
            'agendamentos' => ['data_hora_inicio'],
        ];

        switch ($reportType) {
            case 'usuarios': $query = Usuario::query()->with('escola.municipio'); break;
            case 'recursos': $query = RecursoDidatico::query(); break;
            case 'componentes': $query = ComponenteCurricular::query()->with('criador'); break;
            case 'turmas': $query = Turma::query()->with('escola.municipio'); break;
            case 'agendamentos': $query = Agendamento::query()->with(['recurso', 'oferta.professor', 'oferta.turma.escola.municipio']); break;
        }

        $escolaId = $user->tipo_usuario === 'diretor' ? $user->id_escola : ($filters['id_escola'] ?? null);
        if ($escolaId) $this->applyEscolaFilter($query, $reportType, $escolaId);
        if (!empty($filters['id_municipio'])) $this->applyMunicipioFilter($query, $reportType, $filters['id_municipio']);
        if (!empty($filters['nivel_ensino'])) $this->applyGenericEscolaFilter($query, $reportType, 'nivel_ensino', $filters['nivel_ensino']);
        if (!empty($filters['tipo_escola'])) $this->applyGenericEscolaFilter($query, $reportType, 'tipo', $filters['tipo_escola']);
        
        if ($sortBy && in_array($sortBy, $sortableColumns[$reportType])) {
            $query->orderBy($sortBy, $order);
        } else {
            $defaultSortColumn = $sortableColumns[$reportType][0];
            $query->orderBy($defaultSortColumn, 'asc');
        }

        return $query;
    }
    
    private function applyEscolaFilter(&$query, $type, $escolaId) {
        if (in_array($type, ['usuarios', 'turmas'])) $query->where('id_escola', $escolaId);
        if ($type === 'agendamentos') $query->whereHas('oferta.turma', fn($q) => $q->where('id_escola', $escolaId));
    }

    private function applyMunicipioFilter(&$query, $type, $municipioId) {
        if (in_array($type, ['usuarios', 'turmas'])) $query->whereHas('escola', fn($q) => $q->where('id_municipio', $municipioId));
        if ($type === 'agendamentos') $query->whereHas('oferta.turma.escola', fn($q) => $q->where('id_municipio', $municipioId));
    }
    
    private function applyGenericEscolaFilter(&$query, $type, $column, $value) {
        if (in_array($type, ['usuarios', 'turmas'])) $query->whereHas('escola', fn($q) => $q->where($column, $value));
        if ($type === 'agendamentos') $query->whereHas('oferta.turma.escola', fn($q) => $q->where($column, $value));
    }
    
    private function getStats($user)
    {
        $stats = [];
        if ($user->tipo_usuario === 'administrador') {
            $stats['totalUsuarios'] = Usuario::count();
            $stats['totalRecursos'] = RecursoDidatico::where('status', 'funcionando')->count();
            $stats['totalEscolas'] = Escola::count();
            $stats['totalAgendamentos'] = Agendamento::where('data_hora_inicio', '>=', now())->count();
        } else if ($user->id_escola) {
            $stats['totalUsuarios'] = Usuario::where('id_escola', $user->id_escola)->count();
            $stats['totalRecursos'] = RecursoDidatico::where('status', 'funcionando')->count();
        }
        return $stats;
    }

    private function getChartData($filters, $user)
    {
        $recursosPorStatus = RecursoDidatico::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        
        $userQuery = Usuario::query();
        if ($user->tipo_usuario === 'diretor' && $user->id_escola) {
            $userQuery->where('id_escola', $user->id_escola);
        }
        $usuariosPorTipo = (clone $userQuery)->select('tipo_usuario', DB::raw('count(*) as total'))->groupBy('tipo_usuario')->pluck('total', 'tipo_usuario');

        $usuariosPorMunicipio = collect();
        if ($user->tipo_usuario === 'administrador') {
             $usuariosPorMunicipio = Usuario::join('escolas', 'usuarios.id_escola', '=', 'escolas.id_escola')
                                ->join('municipios', 'escolas.id_municipio', '=', 'municipios.id_municipio')
                                ->select('municipios.nome', DB::raw('count(usuarios.id_usuario) as total'))
                                ->whereNull('usuarios.deleted_at')
                                ->groupBy('municipios.nome')->pluck('total', 'nome');
        }

        return [
            'recursosPorStatus' => $recursosPorStatus,
            'usuariosPorMunicipio' => $usuariosPorMunicipio,
            'usuariosPorTipo' => $usuariosPorTipo
        ];
    }

    private function getLocationFilters($user)
    {
        $municipios = Municipio::orderBy('nome')->get();
        $escolas = collect();
        if ($user->tipo_usuario === 'administrador') $escolas = Escola::orderBy('nome')->get();
        elseif ($user->id_escola) $escolas = Escola::where('id_escola', $user->id_escola)->get();
        return [$municipios, $escolas];
    }
    
    private function getColumns($reportType): array
    {
        switch ($reportType) {
            case 'usuarios': return ['nome_completo' => 'Nome', 'email' => 'E-mail', 'tipo_usuario' => 'Tipo', 'escola.nome' => 'Escola', 'status_aprovacao' => 'Status'];
            case 'recursos': return ['nome' => 'Nome', 'tipo' => 'Tipo', 'marca' => 'Marca', 'quantidade' => 'Qtde', 'status' => 'Status'];
            case 'componentes': return ['nome' => 'Nome', 'carga_horaria' => 'C.H.', 'status' => 'Status', 'criador.nome_completo' => 'Criador'];
            case 'turmas': return ['serie' => 'Série', 'turno' => 'Turno', 'ano_letivo' => 'Ano', 'escola.nome' => 'Escola', 'escola.municipio.nome' => 'Município'];
            case 'agendamentos': return ['data_hora_inicio' => 'Início', 'recurso.nome' => 'Recurso', 'oferta.professor.nome_completo' => 'Professor', 'oferta.turma.serie' => 'Turma'];
        }
        return [];
    }
}