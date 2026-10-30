@extends('preview.layout')

@section('title', $titulo)

@section('content')
    <div class="page-header">
        <h1 class="page-title">📅 Partidos de la Liga</h1>
    </div>

    @include('partials.filtros-partidos', ['estado' => $estado, 'urls' => $urls])

    @include('partials.tabla-partidos', ['partidos' => $partidos, 'puedeAccionar' => false])
@endsection
