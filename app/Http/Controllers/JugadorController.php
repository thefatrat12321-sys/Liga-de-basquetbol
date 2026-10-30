<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJugadorRequest;
use App\Http\Requests\UpdateJugadorRequest;
use App\Models\Equipo;
use App\Models\Jugador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JugadorController extends Controller
{
    public function index(Request $request): View
    {
        $equipoId = $request->integer('equipo_id') ?: null;

        $jugadores = Jugador::query()
            ->with('equipo')
            ->when($equipoId, fn ($query) => $query->where('equipo_id', $equipoId))
            ->join('equipos', 'equipos.id', '=', 'jugadores.equipo_id')
            ->orderBy('equipos.nombre')
            ->orderBy('jugadores.numero')
            ->select('jugadores.*')
            ->get();

        return view('jugadores.index', [
            'jugadores' => $jugadores,
            'equipos' => Equipo::orderBy('nombre')->get(),
            'equipoId' => $equipoId,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $equipos = Equipo::orderBy('nombre')->get();

        if ($equipos->isEmpty()) {
            return redirect()
                ->route('equipos.create')
                ->with('error', 'Debe crear al menos un equipo antes de registrar jugadores.');
        }

        return view('jugadores.create', [
            'equipos' => $equipos,
            'equipoSeleccionado' => $request->integer('equipo_id') ?: null,
        ]);
    }

    public function store(StoreJugadorRequest $request): RedirectResponse
    {
        Jugador::create($request->validated());

        return redirect()
            ->route('jugadores.index')
            ->with('success', 'Jugador registrado exitosamente.');
    }

    public function edit(Jugador $jugador): View
    {
        return view('jugadores.edit', [
            'jugador' => $jugador,
            'equipos' => Equipo::orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateJugadorRequest $request, Jugador $jugador): RedirectResponse
    {
        $jugador->update($request->validated());

        return redirect()
            ->route('jugadores.index')
            ->with('success', 'Jugador actualizado con éxito.');
    }

    public function destroy(Jugador $jugador): RedirectResponse
    {
        $jugador->delete();

        return redirect()
            ->route('jugadores.index')
            ->with('success', 'Jugador eliminado correctamente.');
    }
}
