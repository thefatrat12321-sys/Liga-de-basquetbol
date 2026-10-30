<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartidoRequest;
use App\Http\Requests\UpdateMarcadorRequest;
use App\Models\Equipo;
use App\Models\Partido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartidoController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->query('estado');

        if (! array_key_exists((string) $estado, Partido::ESTADOS)) {
            $estado = null;
        }

        $partidos = Partido::query()
            ->with(['equipoLocal', 'equipoVisitante'])
            ->deEstado($estado)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();

        return view('partidos.index', compact('partidos', 'estado'));
    }

    public function create(): View|RedirectResponse
    {
        $equipos = Equipo::orderBy('nombre')->get();

        if ($equipos->count() < 2) {
            return redirect()
                ->route('partidos.index')
                ->with('error', 'Se necesitan al menos 2 equipos para programar un partido.');
        }

        return view('partidos.create', compact('equipos'));
    }

    public function store(StorePartidoRequest $request): RedirectResponse
    {
        Partido::create([
            ...$request->validated(),
            'puntos_local' => 0,
            'puntos_visitante' => 0,
            'estado' => 'programado',
        ]);

        return redirect()
            ->route('partidos.index')
            ->with('success', 'Partido programado con éxito.');
    }

    public function score(Partido $partido): View
    {
        $partido->load(['equipoLocal', 'equipoVisitante']);

        return view('partidos.score', compact('partido'));
    }

    public function updateScore(UpdateMarcadorRequest $request, Partido $partido): RedirectResponse
    {
        $partido->update($request->validated());

        return redirect()
            ->route('partidos.index')
            ->with('success', 'Resultado y estado del partido actualizados correctamente.');
    }

    public function destroy(Partido $partido): RedirectResponse
    {
        $partido->delete();

        return redirect()
            ->route('partidos.index')
            ->with('success', 'Partido eliminado exitosamente.');
    }
}
