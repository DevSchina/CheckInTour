<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Turista;

class UniqueTuristaForDate implements Rule
{
    protected $fecha;
    protected $turistaId; // Nuevo: Para excluir al turista actual

    public function __construct($fecha, $turistaId = null)
    {
        $this->fecha = $fecha;
        $this->turistaId = $turistaId;
    }

    public function passes($attribute, $value)
    {
        // Verificar si ya existe un turista con el mismo número de documento y fecha
        $query = Turista::where('numero_documento', $value)
            ->whereDate('fecha', $this->fecha);

        // Excluir al turista actual si se proporciona un ID
        if ($this->turistaId) {
            $query->where('id', '!=', $this->turistaId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'El número de documento ya está registrado para esta fecha.';
    }
}