<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentificarNavegador
{
    public const COOKIE = 'ejercicios_navegador';

    public static function id(Request $request): string
    {
        return $request->attributes->get(self::COOKIE);
    }

    public function handle(Request $request, Closure $siguiente): Response
    {
        $id = $request->cookie(self::COOKIE);

        if (! is_string($id) || ! Str::isUuid($id)) {
            $id = (string) Str::uuid();
            Cookie::queue(self::COOKIE, $id, 60 * 24 * 365);
        }

        $request->attributes->set(self::COOKIE, $id);

        return $siguiente($request);
    }
}
