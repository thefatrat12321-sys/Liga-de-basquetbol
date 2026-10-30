<?php

namespace App\Console\Commands;

use App\Models\Partido;
use App\Services\StandingsService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;

class GenerarPreview extends Command
{
    protected $signature = 'liga:preview
                            {--output=docs : Carpeta de salida (relativa a la raíz del proyecto o absoluta)}';

    protected $description = 'Genera la vista previa estática de la liga para GitHub Pages';

    public function handle(StandingsService $standings): int
    {
        $salida = $this->rutaSalida();
        $generadoEn = now()->format('d/m/Y H:i');

        File::ensureDirectoryExists($salida.'/css');

        $base = [
            'tabla' => $standings->tabla(),
            'stats' => $standings->estadisticas(),
            'partidosRecientes' => $this->partidosRecientes(),
            'generadoEn' => $generadoEn,
        ];

        $urls = [null => 'partidos.html'];
        foreach (array_keys(Partido::ESTADOS) as $estado) {
            $urls[$estado] = "partidos-{$estado}.html";
        }

        $paginas = [
            'index.html' => view('preview.index', $base + ['seccion' => 'dashboard'])->render(),
            'partidos.html' => view('preview.partidos', $base + [
                'seccion' => 'partidos',
                'titulo' => 'Partidos',
                'estado' => null,
                'urls' => $urls,
                'partidos' => $this->partidos(null),
            ])->render(),
        ];

        foreach (Partido::ESTADOS as $clave => $etiqueta) {
            $paginas["partidos-{$clave}.html"] = view('preview.partidos', $base + [
                'seccion' => 'partidos',
                'titulo' => 'Partidos · '.$etiqueta,
                'estado' => $clave,
                'urls' => $urls,
                'partidos' => $this->partidos($clave),
            ])->render();
        }

        foreach ($paginas as $archivo => $html) {
            File::put($salida.'/'.$archivo, $html);
        }

        File::copy(public_path('css/style.css'), $salida.'/css/style.css');
        File::put($salida.'/.nojekyll', '');

        $this->info("Vista previa generada en {$salida}");
        $this->line('  · '.count($paginas).' páginas HTML');
        $this->line('  · css/style.css y .nojekyll');

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, Partido>
     */
    private function partidos(?string $estado): Collection
    {
        return Partido::query()
            ->with(['equipoLocal', 'equipoVisitante'])
            ->deEstado($estado)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();
    }

    /**
     * @return Collection<int, Partido>
     */
    private function partidosRecientes(): Collection
    {
        return Partido::query()
            ->with(['equipoLocal', 'equipoVisitante'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->limit(5)
            ->get();
    }

    private function rutaSalida(): string
    {
        $ruta = (string) $this->option('output');

        $esAbsoluta = preg_match('/^([A-Za-z]:[\\\\\/]|[\\\\\/])/', $ruta) === 1;

        return $esAbsoluta
            ? rtrim($ruta, '\\/')
            : base_path(trim($ruta, '\\/'));
    }
}
