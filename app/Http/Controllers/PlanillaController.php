<?php

namespace App\Http\Controllers;

use App\Models\Planilla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TuristasImport;

class PlanillaController extends Controller
{
    // Listar planillas según el rol
    private function getPlanillasForUser($user, $sortBy, $sortOrder)
    {
        if ($user->rol === 'admin' || $user->rol === 'encargado_turismo') {
            return Planilla::with('guia')
                ->orderBy($sortBy, $sortOrder)
                ->paginate(10);
        } else {
            return Planilla::where('guia_id', $user->id)
                ->orderBy($sortBy, $sortOrder)
                ->paginate(10);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $sortBy = $request->get('sort_by', 'fecha');
        $sortOrder = $request->get('sort_order', 'desc');

        // Obtener los filtros del request
        $fechaFiltro = $request->get('fecha');
        $guiaFiltro = $request->get('guia_id');

        // Iniciar la consulta base
        $query = Planilla::query();

        // Aplicar filtros según el rol del usuario
        if ($user->rol === 'admin' || $user->rol === 'encargado_turismo') {
            // Si es admin o encargado, puede ver todas las planillas
            $query->with('guia');
        } else {
            // Si es guía, solo puede ver sus propias planillas
            $query->where('guia_id', $user->id);
        }

        // Aplicar filtro por fecha
        if ($fechaFiltro) {
            $query->whereDate('fecha', $fechaFiltro);
        }

        // Aplicar filtro por guía (solo para admin y encargado)
        if ($guiaFiltro && ($user->rol === 'admin' || $user->rol === 'encargado_turismo')) {
            $query->where('guia_id', $guiaFiltro);
        }

        // Ordenar y paginar los resultados
        $planillas = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        // Obtener la lista de guías para el filtro (solo para admin y encargado)
        $guias = ($user->rol === 'admin' || $user->rol === 'encargado_turismo')
            ? User::where('rol', 'guia')->get()
            : collect();

        return view('planillas.index', compact('planillas', 'sortBy', 'sortOrder', 'guias', 'fechaFiltro', 'guiaFiltro'));
    }

    // Mostrar formulario para crear planilla
    public function create()
    {
        return view('planillas.create');
    }

    private function generarNumeroPlanilla($user, $fecha)
    {
        $ultimaPlanilla = Planilla::orderBy('id', 'desc')->first();
        $numeroPlanilla = $ultimaPlanilla ? $ultimaPlanilla->id + 1 : 1;

        $nombreGuia = str_replace(' ', '', $user->name);
        $nombreGuia = preg_replace('/[^A-Za-z0-9]/', '', $nombreGuia);
        $fechaFormateada = date('Ymd', strtotime($fecha));

        return "PL-{$numeroPlanilla}-{$nombreGuia}-{$fechaFormateada}";
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
        ]);

        $user = Auth::user();

        if (!in_array($user->rol, ['admin', 'encargado_turismo', 'guia'])) {
            return redirect()->back()->with('error', 'No autorizado');
        }

        $numero = $this->generarNumeroPlanilla($user, $request->fecha);

        Planilla::create([
            'numero' => $numero,
            'fecha' => $request->fecha,
            'guia_id' => $user->id,
        ]);

        return redirect()->route('planillas.index')->with('success', 'Planilla creada con éxito');
    }

    public function show($id)
    {
        $planilla = Planilla::with('turistas')->findOrFail($id);
        $user = Auth::user();
    
        if ($user->rol !== 'admin' && $user->rol !== 'encargado_turismo' && $user->id !== $planilla->guia_id) {
            return redirect()->route('planillas.index')->with('error', 'No autorizado');
        }
    
        return view('planillas.show', compact('planilla'));
    }

    
 /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    // Eliminar planilla (solo admin y encargado de turismo )
    public function destroy(string $id)
    {
        $planilla = Planilla::findOrFail($id);

        if (Auth::user()->rol !== 'admin' && Auth::user()->rol !== 'encargado_turismo') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $planilla->delete();

        return response()->json(['message' => 'Planilla eliminada']);
    }

    public function finalizar(Planilla $planilla)
    {
        $user = Auth::user();

        if ($user->rol !== 'admin' && $user->rol !== 'encargado_turismo' && $user->id !== $planilla->guia_id) {
            return redirect()->route('planillas.index')->with('error', 'No autorizado');
        }

        $planilla->estado = true;
        $planilla->save();

        return redirect()->route('planillas.show', $planilla->id)->with('status', 'Planilla finalizada correctamente');
    }

    public function imprimir($id)
    {
        // Obtener la planilla con sus turistas
        $planilla = Planilla::with('turistas')->findOrFail($id);

        // Verificar permisos
        $user = Auth::user();
        if ($user->rol !== 'admin' && $user->rol !== 'encargado_turismo' && $user->id !== $planilla->guia_id) {
            return redirect()->route('planillas.index')->with('error', 'No autorizado');
        }

        // Generar el PDF
        $pdf = Pdf::loadView('planillas.pdf', compact('planilla'));

        // Descargar el PDF
        return $pdf->download("planilla-{$planilla->numero}.pdf");
    }
    
    public function cargarExcel(Request $request, Planilla $planilla)
    {
        
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls',
        ]);
    
        Excel::import(new TuristasImport($planilla), $request->file('archivo_excel'));
    
        return redirect()->route('planillas.show', $planilla->id)->with('success', 'Turistas importados correctamente.');
    }
     
}
