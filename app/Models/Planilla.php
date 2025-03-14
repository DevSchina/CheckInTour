<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Planilla extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'fecha',
        'guia_id',
        'estado',
    ];

    // Una planilla pertenece a un guía
    public function guia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guia_id');
    }

    // Una planilla tiene muchos turistas (relacion muchos a muchos)
    public function turistas(): BelongsToMany
    {
        return $this->belongsToMany(Turista::class, 'planilla_turista');
    }
}
