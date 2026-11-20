<?php

namespace App\Models;

class CalculoPropina
{
    public readonly int $montoEnCentavos;

    public readonly int $propinaEnCentavos;

    public readonly int $totalEnCentavos;

    public function __construct(string $monto, string $porcentaje)
    {
        $this->montoEnCentavos = self::escalarDecimal($monto);
        $porcentajeEnCentesimas = self::escalarDecimal($porcentaje);

        // Redondeo al centavo más cercano; medio centavo se redondea hacia arriba.
        $this->propinaEnCentavos = intdiv($this->montoEnCentavos * $porcentajeEnCentesimas + 5_000, 10_000);
        $this->totalEnCentavos = $this->montoEnCentavos + $this->propinaEnCentavos;
    }

    private static function escalarDecimal(string $valor): int
    {
        $partes = explode('.', $valor, 2);

        return (int) $partes[0] * 100 + (int) str_pad($partes[1] ?? '', 2, '0');
    }
}
