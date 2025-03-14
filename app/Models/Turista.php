<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Turista extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre',
        'correo',
        'telefono',
        'fecha',
        'guia_id',
        'asistencia',
    ];

    protected $casts = [
        'asistencia' => 'boolean',
    ];

    // Un turista pertenece a un guia
    public function guia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guia_id');
    }

    // Un turista puede estar en varias planillas (relacion muchos a muchos)
    public function planillas(): BelongsToMany
    {
        return $this->belongsToMany(Planilla::class, 'planilla_turista');
    }
}
