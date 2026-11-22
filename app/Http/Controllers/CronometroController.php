<?php

namespace App\Http\Controllers;

use App\Http\Middleware\IdentificarNavegador;
use App\Models\Cronometro;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CronometroController extends Controller
{
    public function index(Request $request): View
    {
        $cronometro = Cronometro::query()
            ->where('navegador_id', IdentificarNavegador::id($request))
            ->firstOrNew();

        return view('cronometro.index', [
            'cronometro' => $cronometro,
            'transcurrido' => $cronometro->transcurrido(now()->getTimestampMs()),
            'vueltas' => $cronometro->vueltas()->latest('numero')->paginate(15),
        ]);
    }

    public function iniciar(Request $request): RedirectResponse
    {
        return $this->actualizar($request, fn (Cronometro $cronometro) => $cronometro->iniciar(now()->getTimestampMs()));
    }

    public function pausar(Request $request): RedirectResponse
    {
        return $this->actualizar($request, fn (Cronometro $cronometro) => $cronometro->pausar(now()->getTimestampMs()));
    }

    public function reiniciar(Request $request): RedirectResponse
    {
        return $this->actualizar($request, fn (Cronometro $cronometro) => $cronometro->reiniciar());
    }

    public function guardarVuelta(Request $request): RedirectResponse
    {
        return $this->actualizar($request, function (Cronometro $cronometro): ?string {
            if (! $cronometro->enMarcha()) {
                return 'Inicia el cronómetro antes de registrar una vuelta.';
            }

            $cronometro->registrarVuelta(now()->getTimestampMs());

            return null;
        });
    }

    private function actualizar(Request $request, Closure $accion): RedirectResponse
    {
        $datos = $request->validate([
            'version' => ['required', 'integer', 'min:0'],
        ], ['version.*' => 'Actualiza la página para usar el estado actual del cronómetro.']);

        $id = Cronometro::query()->firstOrCreate([
            'navegador_id' => IdentificarNavegador::id($request),
        ])->id;

        $error = DB::transaction(function () use ($id, $datos, $accion): ?string {
            $cronometro = Cronometro::query()->lockForUpdate()->findOrFail($id);

            if ($cronometro->version !== (int) $datos['version']) {
                return 'El cronómetro cambió. Usa los controles de esta página.';
            }

            $error = $accion($cronometro);
            if ($error !== null) {
                return $error;
            }

            $cronometro->version++;
            $cronometro->save();

            return null;
        });

        $destino = to_route('cronometro.index');

        return $error === null ? $destino : $destino->withErrors(['cronometro' => $error]);
    }
}
