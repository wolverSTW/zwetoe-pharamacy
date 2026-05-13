<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfWrongPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        if (!$panel) {
            return $next($request);
        }

        $panelId = $panel->getId();
        $role = $user->role;

        // If staff is on admin panel, redirect to staff panel
        if ($panelId === 'admin' && $role === 'staff') {
            return redirect('/staff');
        }

        // If admin is on staff panel, redirect to admin panel
        if ($panelId === 'staff' && $role === 'admin') {
            return redirect('/admin');
        }

        return $next($request);
    }
}
