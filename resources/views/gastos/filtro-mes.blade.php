<div class="d-flex flex-column flex-sm-row align-items-sm-end gap-2">
    <div>
        <label for="mes-gastos" class="form-label">Mes del resumen</label>
        <input id="mes-gastos" name="mes" type="month" min="1000-01" max="9999-12"
               class="form-control @error('mes') is-invalid @enderror" value="{{ $mes }}" required>
    </div>
    <button class="btn btn-outline-success" type="submit">Consultar resumen</button>
</div>
