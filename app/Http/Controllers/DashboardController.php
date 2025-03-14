<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turista;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index()
    {
        // Verificar si el usuario tiene permisos
        $user = Auth::user();

        // Obtener la fecha actual
        $fechaActual = now()->toDateString();

        // Contar turistas confirmados (asistencia = 1)
        $turistasConfirmados = Turista::whereDate('fecha', $fechaActual)
            ->where('asistencia', 1)
            ->count();

        // Contar turistas sin confirmar (asistencia = 0)
        $turistasSinConfirmar = Turista::whereDate('fecha', $fechaActual)
            ->where('asistencia', 0)
            ->count();

        // Obtener los detalles de los turistas confirmados desde la API
        $detallesTuristasOracle = $this->obtenerTuristasConfirmadosDesdeAPI();

        // Pasar los contadores y los detalles a la vista
        return view('dashboard', compact('turistasConfirmados', 'turistasSinConfirmar', 'detallesTuristasOracle'));
    }



    private function obtenerTuristasConfirmadosDesdeAPI()
    {
        // URL de la API
        $apiUrl = env('API_URL', 'http://10.11.0.10:3005/api') . '/datos-turismo';

        // Obtener los números de documento y las fechas de los turistas confirmados
        $turistasConfirmados = Turista::where('asistencia', 1)
            ->with('guia') // Cargar la relación con el guía
            ->select('numero_documento', 'fecha', 'guia_id')
            ->get();

        if ($turistasConfirmados->isEmpty()) {
            return collect(); // Si no hay turistas confirmados, retornar una colección vacía
        }

        // Preparar los datos para enviar a la API y formatear las fechas
        $datosParaAPI = $turistasConfirmados->map(function ($turista) {
            return [
                'num_doc' => $turista->numero_documento,
                'fecha' => Carbon::parse($turista->fecha)->format('Y-m-d'), // Formatear la fecha
            ];
        });

        // Realizar la solicitud POST a la API
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'X-API-KEY' => 'wOeDUe1VCUuE8u86cV50I9XnGTPfoZ'
        ])->post($apiUrl, [
            'datos' => $datosParaAPI,
        ]);

        if ($response->successful()) {
            $datosAPI = collect($response->json());

            // Filtrar los resultados de la API para que coincidan las fechas
            $resultadosFiltrados = $datosAPI->filter(function ($item) use ($turistasConfirmados) {
                // Buscar el turista correspondiente en la lista de confirmados
                $turista = $turistasConfirmados->firstWhere('numero_documento', $item['num_doc']);

                // Formatear la fecha de la API para compararla
                $fechaAPI = Carbon::parse($item['fecha'])->format('Y-m-d');
                $fechaTurista = Carbon::parse($turista->fecha)->format('Y-m-d');

                // Si el turista existe y las fechas coinciden, incluir el registro
                return $turista && $fechaTurista == $fechaAPI;
            });

            // Convertir los resultados filtrados en un array y agregar un índice incremental
            $resultadosArray = $resultadosFiltrados->values()->toArray();
            $resultadosConIndice = array_map(function ($item, $index) use ($turistasConfirmados) {
                $turista = $turistasConfirmados->firstWhere('numero_documento', $item['num_doc']);
                $item['id'] = $index + 1; // Agregar un ID incremental (comenzando desde 1)
                $item['guia_nombre'] = $turista->guia ? $turista->guia->name : 'Sin guía'; // Agregar el nombre del guía
                return $item;
            }, $resultadosArray, array_keys($resultadosArray));

            // Paginar manualmente los resultados
            $page = request()->get('page', 1); // Obtener el número de página actual
            $perPage = 30; // Número de elementos por página
            $offset = ($page - 1) * $perPage;

            $paginatedResults = array_slice($resultadosConIndice, $offset, $perPage);

            // Crear una instancia de LengthAwarePaginator
            $resultadosPaginados = new LengthAwarePaginator(
                $paginatedResults, // Elementos de la página actual
                count($resultadosConIndice), // Total de elementos
                $perPage, // Elementos por página
                $page, // Página actual
                ['path' => request()->url(), 'query' => request()->query()] // Opciones de paginación
            );

            return $resultadosPaginados; // Retornar los resultados paginados
        } else {
            // Depuración adicional
            $statusCode = $response->status();
            $errorMessage = $response->body();
            logger()->error("Error en la API: Status $statusCode, Response: $errorMessage");
            return collect();
        }
    }
    
    
    
}