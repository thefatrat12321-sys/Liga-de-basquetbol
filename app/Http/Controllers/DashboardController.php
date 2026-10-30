<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Services\StandingsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(StandingsService $standings): View
    {
        return view('dashboard', [
            'tabla' => $standings->tabla(),
            'stats' => $standings->estadisticas(),
            'partidosRecientes' => Partido::query()
                ->with(['equipoLocal', 'equipoVisitante'])
                ->orderByDesc('fecha')
                ->orderByDesc('hora')
                ->limit(5)
                ->get(),
        ]);
    }
}
