<?php

namespace App\Imports;

use App\Models\Turista;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TuristasImport implements ToModel, WithHeadingRow
{
    protected $planilla;

    public function __construct($planilla)
    {
        $this->planilla = $planilla;
    }

    public function model(array $row)
    {
        // Normalizar los encabezados (usar las claves correctas)
        $normalizedRow = [
            'tipo_documento' => $row['tipo_documento'] ?? null,
            'numero_documento' => $row['numero_de_documento'] ?? null, // Usar la clave correcta
            'nombre' => $row['nombre'] ?? null,
            'correo' => $row['correo'] ?? null,
            'telefono' => $row['telefono'] ?? null,
        ];

        // Validar que los campos requeridos no estén vacíos
        if (empty($normalizedRow['tipo_documento']) || empty($normalizedRow['numero_documento']) || empty($normalizedRow['nombre'])) {
            return null; // Omitir esta fila
        }

        // Verificar si el turista ya está registrado en otra planilla el mismo día
        $turistaExistente = Turista::where('numero_documento', $normalizedRow['numero_documento'])
            ->whereDate('fecha', $this->planilla->fecha)
            ->exists();

        if ($turistaExistente) {
            return null; // Omitir esta fila
        }

        // Convertir a mayúsculas (excepto el correo)
        $tipoDocumento = strtoupper($normalizedRow['tipo_documento']);
        $numeroDocumento = strtoupper($normalizedRow['numero_documento']);
        $nombre = strtoupper($normalizedRow['nombre']);
        $telefono = strtoupper($normalizedRow['telefono']);

        // Convertir el correo a minúsculas
        $correo = strtolower($normalizedRow['correo']);

        // Crear el turista
        $turista = new Turista([
            'guia_id' => $this->planilla->guia_id,
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
            'nombre' => $nombre,
            'correo' => $correo,
            'telefono' => $telefono,
            'fecha' => $this->planilla->fecha,
        ]);

        // Guardar el turista en la base de datos
        $turista->save();

        // Asociar el turista a la planilla (usando la tabla pivote)
        $this->planilla->turistas()->attach($turista->id);

        return $turista;
    }
}