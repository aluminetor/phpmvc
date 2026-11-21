@php
    $titulo = old('titulo', $evento?->titulo);
    $descripcion = old('descripcion', $evento?->descripcion);
    $inicio = old('inicio_en', $evento?->inicioLocal()->format('Y-m-d\TH:i'));
    $recordatorio = old('recordatorio_minutos', $evento?->recordatorio_minutos);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="titulo-evento" class="form-label">Título</label>
        <input id="titulo-evento" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
               value="{{ is_scalar($titulo) ? $titulo : '' }}" maxlength="160" required>
    </div>
    <div class="col-md-6">
        <label for="inicio-evento" class="form-label">Fecha y hora de inicio (Bogotá)</label>
        <input id="inicio-evento" name="inicio_en" type="datetime-local" class="form-control @error('inicio_en') is-invalid @enderror"
               value="{{ is_scalar($inicio) ? $inicio : '' }}" min="1000-01-01T00:00" max="9999-12-31T18:59" required>
    </div>
    <div class="col-12">
        <label for="descripcion-evento" class="form-label">Descripción (opcional)</label>
        <textarea id="descripcion-evento" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                  rows="3" maxlength="3000">{{ is_scalar($descripcion) ? $descripcion : '' }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="recordatorio-evento" class="form-label">Recordatorio visual</label>
        <select id="recordatorio-evento" name="recordatorio_minutos" class="form-select @error('recordatorio_minutos') is-invalid @enderror">
            <option value="">Sin recordatorio</option>
            @foreach ($opcionesRecordatorio as $minutos => $etiqueta)
                <option value="{{ $minutos }}" @selected(is_scalar($recordatorio) && (string) $recordatorio === (string) $minutos)>{{ $etiqueta }}</option>
            @endforeach
        </select>
    </div>
</div>
