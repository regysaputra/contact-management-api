<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from request header
        $token = $request->bearerToken();
        // Asumsi awal user dalam keadaan login
        $authenticate = true;

        // Jika token tidak valid, user dianggap belum login
        if(!$token) {
            $authenticate = false;
        }

        // Cek di database user apakah terdapat token yang diinginkan
        $user = User::where('access_token', $token)->first();

        // Jika token tidak ditemukan maka user dianggap belum login
        if(!$user) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }

        if($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
