<?php

namespace App\Http\Controllers;

use App\Rules\UniqueTuristaForDate;
use App\Models\Turista;
use App\Models\Planilla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuristaController extends Controller
{

    // Agregar el método index
    public function index(Planilla $planilla)
    {
        // Verificar que el usuario tenga acceso a la planilla
        if ($planilla->user_id !== auth()->id() && !in_array(auth()->user()->rol, ['admin', 'encargado_turismo'])) {
            return redirect()->route('planillas.index')->with('error', 'No tienes permiso para ver los turistas de esta planilla.');
        }

        // Obtener los turistas relacionados con la planilla
        $turistas = $planilla->turistas;

        // Retornar la vista con los turistas
        return view('planillas.show', compact('planilla', 'turistas'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(Request $request, Planilla $planilla)
    {
        // Verificar que el usuario sea el dueño de la planilla o tenga permisos
        if (auth()->user()->rol === 'guia' && $planilla->guia_id !== auth()->id()) {
            return redirect()->route('planillas.index')->with('error', 'No tienes permiso para agregar turistas a esta planilla.');
        }

        // Validación de datos (sin la fecha)
        $request->validate([
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                new UniqueTuristaForDate($planilla->fecha), // Usar la fecha de la planilla
            ],
            'nombre' => 'required|string|max:255',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        // Verificar si el turista ya está registrado en otra planilla el mismo día
        $turistaExistente = Turista::where('numero_documento', $request->numero_documento)
            ->whereDate('fecha', $planilla->fecha) // Usar la fecha de la planilla
            ->exists();

        if ($turistaExistente) {
            return redirect()->route('planillas.show', $planilla->id)->with('error', 'El turista ya está registrado en otra planilla para este día.');
        }

        // Convertir nombre y número de documento a mayúsculas, y correo a minúsculas
        $nombre = strtoupper($request->nombre);
        $numeroDocumento = strtoupper($request->numero_documento);
        $correo = strtolower($request->correo);

        // Crear el turista con la fecha de la planilla
        $turista = Turista::create([
            'guia_id' => $planilla->guia_id,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $numeroDocumento, // Número de documento en mayúsculas
            'nombre' => $nombre, // Nombre en mayúsculas
            'correo' => $correo, // Correo en minúsculas
            'telefono' => $request->telefono,
            'fecha' => $planilla->fecha, // Asignar la fecha de la planilla
        ]);

        // Asociar el turista a la planilla (usando la tabla pivote)
        $planilla->turistas()->attach($turista->id);

        return redirect()->route('planillas.show', $planilla->id)->with('success', 'Turista agregado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($planillaId, $turistaId)
    {
        $planilla = Planilla::where('id', $planillaId)
        ->with(['turistas' => function ($query) use ($turistaId) {
            $query->where('turistas.id', $turistaId);  // Especifica que es el 'id' de la tabla 'turistas'
        }])
        ->firstOrFail();


        $turista = $planilla->turistas->first();

        if (!$turista) {
            return redirect()->route('planillas.show', $planillaId)->with('error', 'Turista no encontrado en esta planilla.');
        }

        return view('turistas.edit', compact('turista', 'planilla'));
    }

    
    public function update(Request $request, $planillaId, $turistaId)
    {
        // Obtener el turista que se está editando
        $turista = Turista::whereHas('planillas', function ($query) use ($planillaId) {
            $query->where('planillas.id', $planillaId);
        })->where('id', $turistaId)->firstOrFail();

        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                new UniqueTuristaForDate($turista->fecha, $turista->id), // Usar la regla personalizada
            ],
            'tipo_documento' => 'required|string|max:10',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        // Convertir nombre y número de documento a mayúsculas, y correo a minúsculas
        $nombre = strtoupper($request->nombre);
        $numeroDocumento = strtoupper($request->numero_documento);
        $correo = strtolower($request->correo);

        // Actualizar los datos del turista (sin modificar la fecha)
        $turista->update([
            'nombre' => $nombre, // Nombre en mayúsculas
            'numero_documento' => $numeroDocumento, // Número de documento en mayúsculas
            'tipo_documento' => $request->tipo_documento,
            'correo' => $correo, // Correo en minúsculas
            'telefono' => $request->telefono,
        ]);

        return redirect()->route('planillas.show', $planillaId)->with('success', 'Turista actualizado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turista $turista)
    {
        // Verificar permisos
        if (!in_array(auth()->user()->rol, ['encargado_turismo', 'admin'])) {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar turistas.');
        }
    
        // Obtener la planilla asociada al turista
        $planilla = $turista->planillas()->first();
    
        // Eliminar el turista
        $turista->delete();
    
        // Redirigir a la vista de la planilla
        return redirect()->route('planillas.show', $planilla->id)
            ->with('success', 'Turista eliminado correctamente.');
    }
    

    public function actualizarAsistencia(Request $request, Turista $turista)
    {
        if (!in_array(auth()->user()->rol, ['encargado_turismo', 'admin'])) {
            return response()->json(['error' => 'No tienes permisos para actualizar la asistencia.'], 403);
        }

        $turista->update([
            'asistencia' => $request->input('asistencia', 0),
        ]);

        return response()->json(['success' => true]);
    }   
    
}
