@extends('layouts.app')

@section('title', 'Partidos')

@section('content')
    @php
        $puedeAccionar = auth()->user()->can('gestionar') || auth()->user()->can('cargar-resultados');

        $urls = [null => route('partidos.index')];
        foreach (\App\Models\Partido::ESTADOS as $clave => $etiqueta) {
            $urls[$clave] = route('partidos.index', ['estado' => $clave]);
        }
    @endphp

    <div class="page-header">
        <h1 class="page-title">📅 Partidos de la Liga</h1>
        @can('gestionar')
            <a href="{{ route('partidos.create') }}" class="btn btn-primary">+ Programar Partido</a>
        @endcan
    </div>

    @include('partials.filtros-partidos', ['estado' => $estado, 'urls' => $urls])

    @include('partials.tabla-partidos', ['partidos' => $partidos, 'puedeAccionar' => $puedeAccionar])
@endsection
