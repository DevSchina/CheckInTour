<?php

namespace App\Policies;

use App\Models\Planilla;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanillaPolicy
{
    /**
     * Determine whether the user can view any models.
    */
    public function view(User $user, Planilla $planilla)
    {
        return $user->rol === 'admin' || $user->rol === 'encargado_turismo' || $user->id === $planilla->guia_id;
    }

    public function create(User $user)
    {
        return in_array($user->rol, ['admin', 'encargado_turismo', 'guia']);
    }
}
