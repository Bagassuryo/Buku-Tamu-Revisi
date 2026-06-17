<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Admin|null $user */
        $user = Auth::user();

        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        return redirect()->route('rekap.index')
            ->with('error', 'Anda tidak memiliki hak akses Super Admin.');
    }
}