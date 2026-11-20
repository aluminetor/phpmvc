<?php

namespace App\Models;

class GeneradorContrasenas
{
    public const array TIPOS = [
        'mayusculas' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'minusculas' => 'abcdefghijklmnopqrstuvwxyz',
        'numeros' => '0123456789',
        'simbolos' => '!@#$%^&*()-_=+[]{}:,.?',
    ];

    public function generar(int $longitud, array $tipos): string
    {
        $grupos = array_intersect_key(self::TIPOS, array_flip($tipos));
        $alfabeto = implode('', $grupos);

        // Cada candidato tiene la misma probabilidad; se descartan los que omiten un tipo.
        do {
            $contrasena = '';
            for ($posicion = 0; $posicion < $longitud; $posicion++) {
                $contrasena .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (! $this->contieneTodosLosTipos($contrasena, $grupos));

        return $contrasena;
    }

    private function contieneTodosLosTipos(string $contrasena, array $grupos): bool
    {
        foreach ($grupos as $grupo) {
            if (strpbrk($contrasena, $grupo) === false) {
                return false;
            }
        }

        return true;
    }
}
